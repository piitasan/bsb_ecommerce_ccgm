<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'product_tbl';
    protected $primaryKey       = 'product_id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'product_name',
        'product_slug',
        'category_id',
        'price',
        'stock_qty',
        'main_image',
        'short_description',
        'detailed_description',
        'avg_rating',
        'rating_count',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}