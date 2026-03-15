<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductCategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Shop extends BaseController
{
    public function index(): string
    {
        helper(['url']);

        $productModel = new ProductModel();
        $categoryModel = new ProductCategoryModel();

        $q = trim((string) $this->request->getGet('q'));
        $sort = (string) ($this->request->getGet('sort') ?? 'latest');

        $selectedCategories = (array) ($this->request->getGet('category') ?? []);
        $selectedCategories = array_values(array_filter(array_map('intval', $selectedCategories)));

        $builder = $productModel
            ->select('product_tbl.*, product_category_tbl.category_name')
            ->join('product_category_tbl', 'product_category_tbl.category_id = product_tbl.category_id', 'left')
            ->where('product_tbl.is_active', 1);

        if ($q !== '') {
            $builder->groupStart()
                ->like('product_tbl.product_name', $q)
                ->orLike('product_tbl.short_description', $q)
                ->orLike('product_category_tbl.category_name', $q)
                ->groupEnd();
        }

        if (! empty($selectedCategories)) {
            $builder->whereIn('product_tbl.category_id', $selectedCategories);
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
            case 'rating_desc':
                $builder->orderBy('product_tbl.avg_rating', 'DESC');
                break;
            default:
                $builder->orderBy('product_tbl.product_id', 'DESC');
                break;
        }

        $products = $builder->findAll();
        $categories = $categoryModel
            ->where('is_active', 1)
            ->orderBy('category_name', 'ASC')
            ->findAll();

        $data = [
            'pageTitle' => 'Shop | Byte-Sized Bakes',
            'products' => $products,
            'categories' => $categories,
            'resultCount' => count($products),
            'filters' => [
                'q' => $q,
                'sort' => $sort,
                'category' => $selectedCategories,
            ],
        ];

        return view('templates/bsb_header', $data)
            . view('bsb_shop', $data)
            . view('templates/bsb_footer', $data);
    }

    public function detail(string $slugOrId): string
    {
        helper(['url']);

        $productModel = new ProductModel();

        $builder = $productModel
            ->select('product_tbl.*, product_category_tbl.category_name')
            ->join('product_category_tbl', 'product_category_tbl.category_id = product_tbl.category_id', 'left')
            ->where('product_tbl.is_active', 1);

        if (ctype_digit($slugOrId)) {
            $builder->groupStart()
                ->where('product_tbl.product_slug', $slugOrId)
                ->orWhere('product_tbl.product_id', (int) $slugOrId)
                ->groupEnd();
        } else {
            $builder->where('product_tbl.product_slug', $slugOrId);
        }

        $product = $builder->first();

        if (! $product) {
            throw PageNotFoundException::forPageNotFound('Product not found.');
        }

        $mainImage = ! empty($product['main_image'])
            ? $product['main_image']
            : 'assets/placeholder/bsb_product_default.png';

        // Placeholder gallery for now
        $galleryImages = [
            $mainImage,
            $mainImage,
            'assets/placeholder/bsb_product_default.png',
            'assets/placeholder/bsb_product_default.png',
            'assets/placeholder/bsb_product_default.png',
            'assets/placeholder/bsb_product_default.png',
        ];

        $data = [
            'pageTitle' => ($product['product_name'] ?? 'Product') . ' | Byte-Sized Bakes',
            'product' => $product,
            'galleryImages' => $galleryImages,
        ];

        return view('templates/bsb_header', $data)
            . view('bsb_product_detail', $data)
            . view('templates/bsb_footer', $data);
    }
}