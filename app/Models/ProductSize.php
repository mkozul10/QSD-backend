<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'products_sizes';
    protected $fillable = [
        'products_id',
        'sizes_id',
        'quantity'
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'products_id');
    }

    public function orderProductSizes(){
        return $this->belongsTo(OrderProductSizes::class,'products_sizes_id');
    }
}
