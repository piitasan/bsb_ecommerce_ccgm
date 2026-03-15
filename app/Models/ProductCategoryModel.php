<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductCategoryModel extends Model
{
    protected $table            = 'product_category_tbl';
    protected $primaryKey       = 'category_id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'category_name',
        'category_slug',
        'is_active',
        'created_at',
        'updated_at',
    ];
}