<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function signin()
    {
        helper(['form', 'url']);
        $session = session();

        if ($session->get('isUserLoggedIn')) {
            return redirect()->to('/profile');
        }

        if ($this->request->is('post')) {
            $rules = [
                'uname' => 'required|min_length[3]|max_length[50]',
                'pword' => 'required|min_length[8]|max_length[255]',
            ];

            // if (! $this->validate($rules)) {
            //     return view('auth/bsb_signin', [
            //         'pageTitle'   => 'Sign In',
            //         'validation'  => $this->validator,
            //         'login_error' => 'Please check your input.',
            //     ]);
            // }

            $uname = trim((string) $this->request->getPost('uname'));
            $pword = (string) $this->request->getPost('pword');

            $userModel = new UserModel();
            $user = $userModel->where('uname', $uname)->first();

            $passwordValid = $user
                && (password_verify($pword, (string) $user['pword']) || $pword === (string) $user['pword']);

            if (! $user || ! $passwordValid) {
                return view('auth/bsb_signin', [
                    'pageTitle'   => 'Sign In',
                    'login_error' => 'Invalid username or password.',
                ]);
            }

            // Optional: block admin accounts from user login page
            if (($user['role'] ?? '') === 'admin') {
                return redirect()->to('/admin/login');
            }

            $session->set([
                'user_auth_id'     => (int) $user['user_id'],
                'user_auth_uname'  => $user['uname'],
                'user_auth_name'   => trim(($user['fname'] ?? '') . ' ' . ($user['lname'] ?? '')),
                'user_auth_email'  => $user['email'] ?? '',
                'user_auth_role'   => $user['role'] ?? 'user',
                'isUserLoggedIn'   => true,
            ]);

            return redirect()->to('/profile');
        }

        return view('auth/bsb_signin', ['pageTitle' => 'Sign In']);
    }

    public function signup()
    {
        helper(['form', 'url']);
        $session = session();

        if ($session->get('isUserLoggedIn')) {
            return redirect()->to('/profile');
        }

        if ($this->request->is('post')) {
            $rules = [
                'fname'         => 'required|min_length[2]|max_length[50]',
                'lname'         => 'required|min_length[2]|max_length[50]',
                'uname'         => 'required|min_length[3]|max_length[50]|is_unique[user_tbl.uname]',
                'email'         => 'required|valid_email|max_length[255]|is_unique[user_tbl.email]',
                'pword'         => 'required|min_length[8]|max_length[255]',
                'pword_confirm' => 'required|matches[pword]',
            ];

            if (! $this->validate($rules)) {
                return view('auth/bsb_signup', [
                    'pageTitle'   => 'Sign Up',
                    'validation'  => $this->validator,
                    'signup_error'=> 'Please fix the highlighted fields.',
                ]);
            }

            $userModel = new UserModel();
            $inserted = $userModel->insert([
                'role'       => 'user',
                'fname'      => trim((string) $this->request->getPost('fname')),
                'lname'      => trim((string) $this->request->getPost('lname')),
                'uname'      => trim((string) $this->request->getPost('uname')),
                'email'      => trim((string) $this->request->getPost('email')),
                'pword'      => password_hash((string) $this->request->getPost('pword'), PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if (! $inserted) {
                return view('auth/bsb_signup', [
                    'pageTitle'    => 'Sign Up',
                    'signup_error' => 'Failed to create account. Please try again.',
                ]);
            }

            $newUser = $userModel->find($inserted);

            $session->set([
                'user_auth_id'     => (int) $newUser['user_id'],
                'user_auth_uname'  => $newUser['uname'],
                'user_auth_name'   => trim(($newUser['fname'] ?? '') . ' ' . ($newUser['lname'] ?? '')),
                'user_auth_email'  => $newUser['email'] ?? '',
                'user_auth_role'   => $newUser['role'] ?? 'user',
                'isUserLoggedIn'   => true,
            ]);

            return redirect()->to('/profile');
        }

        return view('auth/bsb_signup', ['pageTitle' => 'Sign Up']);
    }

    public function profile()
    {
        if (! session('isUserLoggedIn')) {
            return redirect()->to('/signin');
        }

        $userId = (int) session('user_auth_id');
        $this->syncSimulatedOrderStatuses($userId);

        $userModel = new UserModel();
        $user = $userModel->find((int) session('user_auth_id'));

        if (! $user) {
            session()->remove([
                'user_auth_id',
                'user_auth_uname',
                'user_auth_name',
                'user_auth_email',
                'user_auth_role',
                'isUserLoggedIn',
            ]);
            return redirect()->to('/signin');
        }

        $activeTab = (string) ($this->request->getGet('tab') ?? 'overview');
        $allowedTabs = ['overview', 'history', 'wishlist', 'payment', 'security'];
        if (! in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'overview';
        }

        $orders = [];
        $historyByDay = [];

        if ($activeTab === 'history') {
            $db = \Config\Database::connect();

            $rows = $db->table('order_tbl o')
                ->select('
                    o.order_id,
                    o.order_number,
                    o.created_at,
                    o.payment_method,
                    o.total_amount,
                    o.order_status,
                    oi.order_item_id,
                    oi.product_id,
                    oi.product_name,
                    oi.category_name,
                    oi.qty,
                    oi.line_total,
                    p.main_image
                ')
                ->join('order_item_tbl oi', 'oi.order_id = o.order_id')
                ->join('product_tbl p', 'p.product_id = oi.product_id', 'left')
                ->where('o.user_id', (int) session('user_auth_id'))
                ->orderBy('o.created_at', 'DESC')
                ->orderBy('o.order_id', 'DESC')
                ->orderBy('oi.order_item_id', 'ASC')
                ->get()
                ->getResultArray();

            // Group by day -> order -> items
            foreach ($rows as $row) {
                $dayKey = date('F j, Y', strtotime((string) $row['created_at']));
                $orderKey = (int) $row['order_id'];

                if (!isset($historyByDay[$dayKey])) {
                    $historyByDay[$dayKey] = [];
                }

                if (!isset($historyByDay[$dayKey][$orderKey])) {
                    $historyByDay[$dayKey][$orderKey] = [
                        'order_id' => $row['order_id'],
                        'order_number' => $row['order_number'],
                        'created_at' => $row['created_at'],
                        'payment_method' => $row['payment_method'],
                        'total_amount' => $row['total_amount'],
                        'order_status' => $row['order_status'],
                        'items' => [],
                    ];
                }

                $historyByDay[$dayKey][$orderKey]['items'][] = [
                    'product_name' => $row['product_name'],
                    'category_name' => $row['category_name'],
                    'qty' => $row['qty'],
                    'line_total' => $row['line_total'],
                    'main_image' => $row['main_image'],
                ];
            }
        }

        return view('templates/bsb_header', ['pageTitle' => 'My Profile'])
            . view('auth/bsb_profile', [
                'pageTitle' => 'My Profile',
                'user'      => $user,
                'activeTab' => $activeTab,
                'orders'    => $orders,
                'historyByDay' => $historyByDay,
            ])
            . view('templates/bsb_footer');
    }

    private function syncSimulatedOrderStatuses(int $userId): void
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $orders = $db->table('order_tbl')
            ->select('order_id, order_status, eta_half_at, eta_due_at, stock_applied')
            ->where('user_id', $userId)
            ->whereIn('order_status', ['pending', 'on_the_way'])
            ->get()
            ->getResultArray();

        foreach ($orders as $order) {
            $orderId = (int) $order['order_id'];
            $status = (string) $order['order_status'];
            $halfAt = (string) ($order['eta_half_at'] ?? '');
            $dueAt = (string) ($order['eta_due_at'] ?? '');

            if ($dueAt !== '' && strtotime($now) >= strtotime($dueAt)) {
                // Mark delivered
                $db->table('order_tbl')
                    ->where('order_id', $orderId)
                    ->update([
                        'order_status' => 'delivered',
                        'delivered_at' => $now,
                        'updated_at'   => $now,
                    ]);

                // Claim stock application once (atomic guard)
                $db->table('order_tbl')
                    ->where('order_id', $orderId)
                    ->where('stock_applied', 0)
                    ->update(['stock_applied' => 1, 'updated_at' => $now]);

                if ($db->affectedRows() === 1) {
                    $this->applyDeliveredStock($orderId);
                }

                continue;
            }

            if ($status === 'pending' && $halfAt !== '' && strtotime($now) >= strtotime($halfAt)) {
                $db->table('order_tbl')
                    ->where('order_id', $orderId)
                    ->update([
                        'order_status' => 'on_the_way',
                        'updated_at'   => $now,
                    ]);
            }
        }
    }

    private function applyDeliveredStock(int $orderId): void
    {
        $db = \Config\Database::connect();

        $items = $db->table('order_item_tbl')
            ->select('product_id, qty')
            ->where('order_id', $orderId)
            ->get()
            ->getResultArray();

        foreach ($items as $item) {
            $productId = (int) $item['product_id'];
            $qty = max(0, (int) $item['qty']);

            if ($qty > 0) {
                $db->table('product_tbl')
                    ->set('stock_qty', 'GREATEST(stock_qty - ' . $qty . ', 0)', false)
                    ->where('product_id', $productId)
                    ->update();
            }
        }
    }

    public function logout()
    {
        session()->remove([
            'user_auth_id',
            'user_auth_uname',
            'user_auth_name',
            'user_auth_email',
            'user_auth_role',
            'isUserLoggedIn',
        ]);

        return redirect()->to('/signin');
    }

    public function history()
    {
        $uname = trim((string) $this->request->getPost('uname'));
        $pword = (string) $this->request->getPost('pword');

        $userModel = new UserModel();
        $user = $userModel->where('uname', $uname)->first();

        $activeTab = (string) ($this->request->getGet('tab') ?? 'history');
        $allowedTabs = ['history', 'wishlist', 'payment', 'security'];
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'history';
        }

        $orders = [];
        $historyByDay = [];

        if ($activeTab === 'history') {
            $db = \Config\Database::connect();

            $rows = $db->table('order_tbl o')
                ->select('
                    o.order_id,
                    o.order_number,
                    o.created_at,
                    o.payment_method,
                    o.total_amount,
                    o.order_status,
                    oi.order_item_id,
                    oi.product_id,
                    oi.product_name,
                    oi.category_name,
                    oi.qty,
                    oi.line_total,
                    p.main_image
                ')
                ->join('order_item_tbl oi', 'oi.order_id = o.order_id')
                ->join('product_tbl p', 'p.product_id = oi.product_id', 'left')
                ->where('o.user_id', (int) session('user_auth_id'))
                ->orderBy('o.created_at', 'DESC')
                ->orderBy('o.order_id', 'DESC')
                ->orderBy('oi.order_item_id', 'ASC')
                ->get()
                ->getResultArray();

            // Group by day -> order -> items
            foreach ($rows as $row) {
                $dayKey = date('F j, Y', strtotime((string) $row['created_at']));
                $orderKey = (int) $row['order_id'];

                if (!isset($historyByDay[$dayKey])) {
                    $historyByDay[$dayKey] = [];
                }

                if (!isset($historyByDay[$dayKey][$orderKey])) {
                    $historyByDay[$dayKey][$orderKey] = [
                        'order_id' => $row['order_id'],
                        'order_number' => $row['order_number'],
                        'created_at' => $row['created_at'],
                        'payment_method' => $row['payment_method'],
                        'total_amount' => $row['total_amount'],
                        'order_status' => $row['order_status'],
                        'items' => [],
                    ];
                }

                $historyByDay[$dayKey][$orderKey]['items'][] = [
                    'product_name' => $row['product_name'],
                    'category_name' => $row['category_name'],
                    'qty' => $row['qty'],
                    'line_total' => $row['line_total'],
                    'main_image' => $row['main_image'],
                ];
            }
        }

        return view('templates/bsb_header', ['pageTitle' => 'My Profile'])
            . view('auth/bsb_profile', [
                'pageTitle' => 'My Profile',
                'user'      => $user,
                'activeTab' => $activeTab,
                'orders'    => $orders,
                'historyByDay' => $historyByDay,
            ])
            . view('templates/bsb_footer');
    }
}