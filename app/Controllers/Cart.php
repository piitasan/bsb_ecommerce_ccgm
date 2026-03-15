<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\ProductModel;

class Cart extends BaseController
{
    private function requireUser()
    {
        if (! session('isUserLoggedIn')) {
            return redirect()->to('/signin')->with('cart_error', 'Please sign in first.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->requireUser()) {
            return $redirect;
        }

        $userId = (int) session('user_auth_id');

        $db = \Config\Database::connect();

        $items = $db->table('cart_tbl c')
            ->select('
                ci.cart_item_id,
                ci.qty,
                ci.unit_price,
                p.product_id,
                p.product_name,
                p.main_image,
                p.stock_qty,
                p.category_id,
                pc.category_name
            ')
            ->join('cart_item_tbl ci', 'ci.cart_id = c.cart_id')
            ->join('product_tbl p', 'p.product_id = ci.product_id')
            ->join('product_category_tbl pc', 'pc.category_id = p.category_id', 'left')
            ->where('c.user_id', $userId)
            ->where('p.is_active', 1)
            ->orderBy('ci.cart_item_id', 'DESC')
            ->get()
            ->getResultArray();

        $cartQtyCount = 0;
        $cartSubtotal = 0.0;
        foreach ($items as &$item) {
            $item['line_total'] = (float) $item['unit_price'] * (int) $item['qty'];
            $cartQtyCount += (int) $item['qty'];
            $cartSubtotal += (float) $item['line_total'];
        }
        unset($item);

        $data = [
            'pageTitle' => 'Cart | Byte-Sized Bakes',
            'cartItems' => $items,
            'cartQtyCount' => $cartQtyCount,
            'cartSubtotal' => $cartSubtotal,
        ];

        return view('templates/bsb_header', $data)
            . view('bsb_cart', $data)
            . view('templates/bsb_footer', $data);
    }

    public function add()
    {
        if ($redirect = $this->requireUser()) {
            return $redirect;
        }

        $userId = (int) session('user_auth_id');
        $productId = (int) $this->request->getPost('product_id');
        $qtyPosted = (int) $this->request->getPost('qty');
        $qtyPosted = max(1, $qtyPosted);

        $productModel = new ProductModel();
        $product = $productModel
            ->where('product_id', $productId)
            ->where('is_active', 1)
            ->first();

        if (! $product) {
            return redirect()->back()->with('cart_error', 'Product not found.');
        }

        $stock = (int) ($product['stock_qty'] ?? 0);
        if ($stock <= 0) {
            return redirect()->back()->with('cart_error', 'Product is out of stock.');
        }

        $qty = min($qtyPosted, $stock);

        $cartModel = new CartModel();
        $cartItemModel = new CartItemModel();

        $cart = $cartModel->where('user_id', $userId)->first();
        if (! $cart) {
            $cartModel->insert([
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $cart = $cartModel->where('user_id', $userId)->first();
        }

        $cartId = (int) $cart['cart_id'];

        $existing = $cartItemModel
            ->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $newQty = min($stock, (int) $existing['qty'] + $qty);
            $cartItemModel->update((int) $existing['cart_item_id'], [
                'qty' => $newQty,
                'unit_price' => (float) $product['price'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $cartItemModel->insert([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'qty' => $qty,
                'unit_price' => (float) $product['price'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('/cart')->with('cart_success', 'Item added to cart.');
    }

    public function updateQty()
    {
        if ($redirect = $this->requireUser()) {
            return $redirect;
        }

        $userId = (int) session('user_auth_id');
        $cartItemId = (int) $this->request->getPost('cart_item_id');
        $qty = max(1, (int) $this->request->getPost('qty'));
        $returnTo = (string) $this->request->getPost('return_to');
        $redirectTo = $returnTo === 'checkout' ? '/checkout' : '/cart';

        $db = \Config\Database::connect();

        $item = $db->table('cart_item_tbl ci')
            ->select('ci.cart_item_id, p.stock_qty')
            ->join('cart_tbl c', 'c.cart_id = ci.cart_id')
            ->join('product_tbl p', 'p.product_id = ci.product_id')
            ->where('ci.cart_item_id', $cartItemId)
            ->where('c.user_id', $userId)
            ->get()
            ->getRowArray();

        if (! $item) {
            return redirect()->to($redirectTo)->with('cart_error', 'Invalid cart item.');
        }

        $stock = max(1, (int) $item['stock_qty']);
        $qty = min($qty, $stock);

        (new CartItemModel())->update($cartItemId, [
            'qty' => $qty,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to($redirectTo)->with('cart_success', 'Quantity updated.');
    }

    public function remove()
    {
        if ($redirect = $this->requireUser()) {
            return $redirect;
        }

        $userId = (int) session('user_auth_id');
        $cartItemId = (int) $this->request->getPost('cart_item_id');

        $db = \Config\Database::connect();

        $owned = $db->table('cart_item_tbl ci')
            ->select('ci.cart_item_id')
            ->join('cart_tbl c', 'c.cart_id = ci.cart_id')
            ->where('ci.cart_item_id', $cartItemId)
            ->where('c.user_id', $userId)
            ->get()
            ->getRowArray();

        if (! $owned) {
            return redirect()->to('/cart')->with('cart_error', 'Invalid cart item.');
        }

        $cartItemModel = new CartItemModel();
        $cartItemModel->delete($cartItemId);

        return redirect()->to('/cart')->with('cart_success', 'Item removed from cart.');
    }

    public function proceed()
    {
        if ($redirect = $this->requireUser()) {
            return $redirect;
        }

        $userId = (int) session('user_auth_id');
        $selected = (array) $this->request->getPost('cart_item_ids');
        $selected = array_values(array_filter(array_map('intval', $selected)));

        if (empty($selected)) {
            return redirect()->to('/cart')->with('cart_error', 'Please check at least one item.');
        }

        $db = \Config\Database::connect();
        $validIds = $db->table('cart_item_tbl ci')
            ->select('ci.cart_item_id')
            ->join('cart_tbl c', 'c.cart_id = ci.cart_id')
            ->where('c.user_id', $userId)
            ->whereIn('ci.cart_item_id', $selected)
            ->get()
            ->getResultArray();

        $validIds = array_map(static fn($row) => (int) $row['cart_item_id'], $validIds);

        if (empty($validIds)) {
            return redirect()->to('/cart')->with('cart_error', 'No valid items selected.');
        }

        session()->set('checkout_selected_cart_item_ids', $validIds);

        return redirect()->to('/checkout');
    }

    public function checkout()
    {
        if ($redirect = $this->requireUser()) return $redirect;

        $userId = (int) session('user_auth_id');
        $selectedIds = (array) session('checkout_selected_cart_item_ids');

        if (empty($selectedIds)) {
            return redirect()->to('/cart')->with('cart_error', 'Select cart items first.');
        }

        $db = \Config\Database::connect();

        $items = $db->table('cart_item_tbl ci')
            ->select('ci.cart_item_id, ci.qty, ci.unit_price, p.product_id, p.product_name, p.main_image, p.stock_qty, pc.category_name')
            ->join('cart_tbl c', 'c.cart_id = ci.cart_id')
            ->join('product_tbl p', 'p.product_id = ci.product_id')
            ->join('product_category_tbl pc', 'pc.category_id = p.category_id', 'left')
            ->where('c.user_id', $userId)
            ->whereIn('ci.cart_item_id', $selectedIds)
            ->get()
            ->getResultArray();

        if (empty($items)) {
            return redirect()->to('/cart')->with('cart_error', 'No valid checkout items.');
        }

        $summary = [];
        $total = 0.0;
        foreach ($items as &$item) {
            $line = (float) $item['unit_price'] * (int) $item['qty'];
            $item['line_total'] = $line;
            $summary[] = [
                'label' => $item['product_name'] . ' x ' . (int) $item['qty'],
                'amount' => $line,
            ];
            $total += $line;
        }
        unset($item);

        $data = [
            'pageTitle' => 'Checkout | Byte-Sized Bakes',
            'checkoutItems' => $items,
            'summaryRows' => $summary,
            'checkoutTotal' => $total,
        ];

        return view('templates/bsb_header', $data)
            . view('bsb_checkout', $data)
            . view('templates/bsb_footer', $data);
    }

    public function placeOrder()
    {
        $etaMinutes = random_int(2, 30);
        $now = time();
        $halfAt = date('Y-m-d H:i:s', $now + (int) floor(($etaMinutes * 60) / 2));
        $dueAt  = date('Y-m-d H:i:s', $now + ($etaMinutes * 60));
        
        if ($redirect = $this->requireUser()) return $redirect;

        $userId = (int) session('user_auth_id');
        $selectedIds = (array) session('checkout_selected_cart_item_ids');
        if (empty($selectedIds)) {
            return redirect()->to('/cart')->with('cart_error', 'No selected items for checkout.');
        }

        $paymentMethod = (string) $this->request->getPost('payment_method');
        $deliveryAddress = trim((string) $this->request->getPost('delivery_address'));
        $lat = $this->request->getPost('address_lat');
        $lng = $this->request->getPost('address_lng');

        if (!in_array($paymentMethod, ['cod', 'gcash'], true)) {
            return redirect()->back()->with('cart_error', 'Select a valid payment method.');
        }
        if ($deliveryAddress === '') {
            return redirect()->back()->with('cart_error', 'Delivery address is required.');
        }

        $db = \Config\Database::connect();
        $items = $db->table('cart_item_tbl ci')
            ->select('ci.cart_item_id, ci.qty, ci.unit_price, p.product_id, p.product_name, p.stock_qty, pc.category_name')
            ->join('cart_tbl c', 'c.cart_id = ci.cart_id')
            ->join('product_tbl p', 'p.product_id = ci.product_id')
            ->join('product_category_tbl pc', 'pc.category_id = p.category_id', 'left')
            ->where('c.user_id', $userId)
            ->whereIn('ci.cart_item_id', $selectedIds)
            ->get()
            ->getResultArray();

        if (empty($items)) {
            return redirect()->to('/cart')->with('cart_error', 'No valid items to checkout.');
        }

        $total = 0.0;
        foreach ($items as $it) {
            $total += (float) $it['unit_price'] * (int) $it['qty'];
        }

        $db->transStart();

        // 1) insert with temporary unique order number
        $tempOrderNo = 'TMP-' . date('YmdHis') . '-' . mt_rand(1000, 9999);

        $db->table('order_tbl')->insert([
            'user_id' => $userId,
            'order_number' => $tempOrderNo,
            'payment_method' => $paymentMethod,
            'delivery_address' => $deliveryAddress,
            'address_lat' => $lat !== '' ? $lat : null,
            'address_lng' => $lng !== '' ? $lng : null,
            'subtotal' => $total,
            'total_amount' => $total,
            'order_status' => 'pending',
            'eta_minutes'  => $etaMinutes,
            'eta_half_at'  => $halfAt,
            'eta_due_at'   => $dueAt,
            'delivered_at' => null,
            'stock_applied'=> 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $orderId = (int) $db->insertID();

        // 2) final formatted order number:
        // BSB-0001-YYMMDD-HHMM-S-SerializedID
        $serializedId = str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $finalOrderNo = sprintf(
            'BSB-%04d-%s-%s-S-%s',
            $orderId,
            date('ymd'),
            date('Hi'),
            $serializedId
        );

        // 3) update inserted row with final number
        $db->table('order_tbl')
            ->where('order_id', $orderId)
            ->update([
                'order_number' => $finalOrderNo,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        foreach ($items as $it) {
            $line = (float) $it['unit_price'] * (int) $it['qty'];
            $db->table('order_item_tbl')->insert([
                'order_id' => $orderId,
                'product_id' => (int) $it['product_id'],
                'product_name' => (string) $it['product_name'],
                'category_name' => (string) ($it['category_name'] ?? ''),
                'unit_price' => (float) $it['unit_price'],
                'qty' => (int) $it['qty'],
                'line_total' => $line,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $db->table('cart_item_tbl')->whereIn('cart_item_id', $selectedIds)->delete();

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('cart_error', 'Checkout failed. Please try again.');
        }

        session()->remove('checkout_selected_cart_item_ids');

        return redirect()->to('/profile?tab=history')->with('cart_success', 'Order placed successfully.');
    }
}