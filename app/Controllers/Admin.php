<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\ProductCategoryModel;

class Admin extends BaseController
{
    public function login()
    {
        helper(['form', 'url']);
        $session = session();

        if ($this->request->is('post')) {
            $rules = [
                'uname' => 'required|min_length[3]|max_length[50]',
                'pword' => 'required|min_length[6]|max_length[255]',
            ];

            if (! $this->validate($rules)) {
                return view('admin/bsb_admin', [
                    'pageTitle'  => 'BSB Management System',
                    'validation' => $this->validator,
                ]);
            }

            $uname = trim((string) $this->request->getPost('uname'));
            $pword = trim((string) $this->request->getPost('pword'));

            $userModel = new UserModel();
            $user = $userModel
                ->where('uname', $uname)
                ->where('role', 'admin')
                ->first();

            $passwordValid = $user
                && (password_verify($pword, $user['pword']) || $pword === $user['pword']);

            if (! $user || ! $passwordValid) {
                return view('admin/bsb_admin', [
                    'pageTitle'   => 'BSB Management System',
                    'login_error' => 'Invalid username or password.',
                ]);
            }

            $session->set([
                'auth_user_id' => $user['user_id'],
                'auth_name'    => trim(($user['fname'] ?? '') . ' ' . ($user['lname'] ?? '')),
                'auth_uname'   => $user['uname'],
                'auth_role'    => $user['role'],
                'isLoggedIn'   => true,
            ]);

            return redirect()->to('/admin/dashboard');
        }

        return view('admin/bsb_admin', [
            'pageTitle' => 'BSB Management System',
        ]);
    }

    public function dashboard()
    {
        if (! session('isLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        $productModel = new ProductModel();
        $categoryModel = new ProductCategoryModel();
        $userModel = new UserModel();

        $q = trim((string) $this->request->getGet('q'));
        $categoryId = (int) ($this->request->getGet('category_id') ?? 0);
        $sort = (string) ($this->request->getGet('sort') ?? 'latest');

        $builder = $productModel
            ->select('product_tbl.*, product_category_tbl.category_name')
            ->join('product_category_tbl', 'product_category_tbl.category_id = product_tbl.category_id', 'left')
            ->where('product_tbl.is_active', 1);

        if ($q !== '') {
            $builder->groupStart()
                ->like('product_tbl.product_name', $q)
                ->orLike('product_tbl.short_description', $q)
                ->groupEnd();
        }

        if ($categoryId > 0) {
            $builder->where('product_tbl.category_id', $categoryId);
        }

        switch ($sort) {
            case 'name_asc':
                $builder->orderBy('product_tbl.product_name', 'ASC');
                break;
            case 'price_asc':
                $builder->orderBy('product_tbl.price', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('product_tbl.price', 'DESC');
                break;
            default:
                $builder->orderBy('product_tbl.product_id', 'DESC');
                break;
        }

        $products = $builder->findAll();
        $categories = $categoryModel->where('is_active', 1)->orderBy('category_name', 'ASC')->findAll();

        $db = \Config\Database::connect();
        $dbStatus = $db->connID ? 'Online' : 'Offline';

        return view('admin/bsb_dashboard', [
            'pageTitle'        => 'BSB Management System Dashboard',
            'products'         => $products,
            'categories'       => $categories,
            'productsCount'    => $productModel->where('is_active', 1)->countAllResults(),
            'registeredUsers'  => $userModel->countAllResults(),
            'dbStatus'         => $dbStatus,
            'filters'          => [
                'q' => $q,
                'category_id' => $categoryId,
                'sort' => $sort,
            ],
            'validation'       => session('validation'),
            'product_success'  => session('product_success'),
            'product_error'    => session('product_error'),
        ]);
    }

    public function createProduct()
    {
        if (! session('isLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        helper(['form', 'url', 'text']);

        $rules = [
            'product_name' => 'required|min_length[3]|max_length[150]',
            'price' => 'required|decimal|greater_than_equal_to[0]',
            'stock_qty' => 'required|integer|greater_than_equal_to[0]',
            'category_id' => 'required|integer',
            'short_description' => 'permit_empty|max_length[500]',
            'detailed_description' => 'permit_empty|max_length[10000]',
            'image' => 'permit_empty|is_image[image]|max_size[image,5120]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/dashboard#manage-product')
                ->withInput()
                ->with('validation', $this->validator)
                ->with('product_error', 'Please fix the form errors.');
        }

        $productModel = new ProductModel();

        $productName = trim((string) $this->request->getPost('product_name'));
        $slugBase = url_title($productName, '-', true);
        $slug = $slugBase;
        $counter = 2;

        while ($productModel->where('product_slug', $slug)->first()) {
            $slug = $slugBase . '-' . $counter;
            $counter++;
        }

        $defaultImagePath = 'assets/placeholder/bsb_product_default.png';
        $mainImagePath = $defaultImagePath;

        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && $image->getError() !== UPLOAD_ERR_NO_FILE) {
            $uploadDir = FCPATH . 'uploads/products';
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newImageName = $image->getRandomName();
            $image->move($uploadDir, $newImageName);
            $mainImagePath = 'uploads/products/' . $newImageName;
        }

        $inserted = $productModel->insert([
            'product_name' => $productName,
            'product_slug' => $slug,
            'category_id' => (int) $this->request->getPost('category_id'),
            'price' => (float) $this->request->getPost('price'),
            'stock_qty' => (int) $this->request->getPost('stock_qty'),
            'main_image' => $mainImagePath,
            'short_description' => trim((string) $this->request->getPost('short_description')),
            'detailed_description' => trim((string) $this->request->getPost('detailed_description')),
            'created_by' => (int) session('auth_user_id'),
            'updated_by' => (int) session('auth_user_id'),
        ]);

        if (! $inserted) {
            return redirect()->to('/admin/dashboard#manage-product')
                ->withInput()
                ->with('product_error', 'Failed to save product. Please check DB constraints.');
        }

        return redirect()->to('/admin/dashboard#product-list')
            ->with('product_success', 'Product added successfully.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }

    public function updateProduct(int $productId)
    {
        if (! session('isLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        helper(['form', 'url']);

        $productModel = new ProductModel();
        $product = $productModel->find($productId);

        if (! $product) {
            return redirect()->to('/admin/dashboard#product-list')
                ->with('product_error', 'Product not found.');
        }

        $rules = [
            'product_name' => 'required|min_length[3]|max_length[150]',
            'price' => 'required|decimal|greater_than_equal_to[0]',
            'stock_qty' => 'required|integer|greater_than_equal_to[0]',
            'category_id' => 'required|integer',
            'short_description' => 'permit_empty|max_length[500]',
            'detailed_description' => 'permit_empty|max_length[10000]',
            'image' => 'permit_empty|is_image[image]|max_size[image,5120]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->to('/admin/dashboard#product-list')
                ->with('product_error', 'Invalid update data.');
        }

        $mainImagePath = $product['main_image'] ?: 'assets/placeholder/bsb_product_default.png';
        $image = $this->request->getFile('image');

        if ($image && $image->isValid() && $image->getError() !== UPLOAD_ERR_NO_FILE) {
            $uploadDir = FCPATH . 'uploads/products';
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newImageName = $image->getRandomName();
            $image->move($uploadDir, $newImageName);
            $mainImagePath = 'uploads/products/' . $newImageName;
        }

        $productName = trim((string) $this->request->getPost('product_name'));

        $productModel->update($productId, [
            'product_name' => $productName,
            'category_id' => (int) $this->request->getPost('category_id'),
            'price' => (float) $this->request->getPost('price'),
            'stock_qty' => (int) $this->request->getPost('stock_qty'),
            'main_image' => $mainImagePath,
            'short_description' => trim((string) $this->request->getPost('short_description')),
            'detailed_description' => trim((string) $this->request->getPost('detailed_description')),
            'updated_by' => (int) session('auth_user_id'),
        ]);

        return redirect()->to('/admin/dashboard#product-list')
            ->with('product_success', 'Product updated successfully.');
    }

    public function deleteProduct(int $productId)
    {
        if (! session('isLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        $productModel = new ProductModel();
        $product = $productModel->find($productId);

        if (! $product) {
            return redirect()->to('/admin/dashboard#product-list')
                ->with('product_error', 'Product not found.');
        }

        // soft-like delete via is_active flag
        $productModel->update($productId, [
            'is_active' => 0,
            'updated_by' => (int) session('auth_user_id'),
        ]);

        return redirect()->to('/admin/dashboard#product-list')
            ->with('product_success', 'Product deleted successfully.');
    }
}