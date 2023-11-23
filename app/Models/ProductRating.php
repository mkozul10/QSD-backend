<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'products_ratings';
    protected $fillable = [
        'users_id',
        'products_id',
        'review',
        'rating'
    ];
}
