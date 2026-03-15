<?php

namespace App\Models;

use CodeIgniter\Model;

class CartItemModel extends Model
{
    protected $table = 'cart_item_tbl';
    protected $primaryKey = 'cart_item_id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $useTimestamps = false;

    protected $allowedFields = [
        'cart_id',
        'product_id',
        'qty',
        'unit_price',
        'created_at',
        'updated_at',
    ];
}