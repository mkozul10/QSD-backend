<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductSizes extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'orders_products_sizes';
    protected $fillable = [
        'orders_id',
        'products_sizes_id',
        'quantity'
    ];

    public function productSize(){
        return $this->hasMany(ProductSize::class,'products_sizes_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'orders_id');
    }
}
