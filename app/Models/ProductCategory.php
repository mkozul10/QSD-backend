<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'categories_products';
    protected $fillable = [
        'products_id',
        'categories_id'
    ];
}
