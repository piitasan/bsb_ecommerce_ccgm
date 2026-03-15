<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user_tbl';
    protected $primaryKey       = 'user_id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['role', 'fname', 'lname', 'uname', 'pword', 'email', 'created_at'];
    protected $useAutoIncrement = true;
    protected $useTimestamps    = false;
}