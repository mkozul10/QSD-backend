<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $table = 'favorites';
    public $timestamps = true;
    protected $fillable = [
        'users_id',
        'products_id'
    ];

    public function product(){
        return $this->belongsTo(Product::class,'products_id');
    }
}