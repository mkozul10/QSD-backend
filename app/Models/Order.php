<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'orders';
    protected $fillable = [
        'address',
        'city',
        'zip_code',
        'phone',
        'users_id',
        'transaction_id',
        'price',
        'status',
        'comment',
        'guest_email'
    ];

    protected $hidden = [
        'comment',
    ];

    public function products(){
        return $this->belongsToMany(ProductSize::class,'orders_products_sizes', 'orders_id', 'products_sizes_id')
                    ->withPivot('quantity');
    }

    public function orderProductSizes(){
        return $this->hasMany(OrderProductSizes::class,'orders_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'users_id');
    }
}
