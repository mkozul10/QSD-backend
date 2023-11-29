<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'price',
        'gender',
        'colors_id',
        'brands_id',
        'description',
        'avg_rating',
        'total_rating'
    ];
    public function color(){
        return $this->belongsTo(Color::class, 'colors_id');
    }
    public function brand(){
        return $this->belongsTo(Brand::class,'brands_id');
    }
    public function images(){
        return $this->hasMany(Image::class,'products_id');
    }
    public function categories(){
        return $this->belongsToMany(Category::class,'categories_products', 'products_id', 'categories_id');
    }
    public function sizes(){
        return $this->belongsToMany(Size::class,'products_sizes', 'products_id', 'sizes_id')
                    ->withPivot('quantity');
    }
    public function ratings(){
        return $this->belongsToMany(User::class,'products_ratings', 'products_id', 'users_id')
                    ->withPivot(['review', 'rating', 'created_at', 'updated_at']);
    }

    public function product_sizes(){
        return $this->hasMany(ProductSize::class,'products_id');
    }
}
