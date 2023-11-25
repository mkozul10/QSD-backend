<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'images';
    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'products_id',
        'link',
        'hash'
    ];

    protected $hidden = [
        'hash'
    ];
    
    public function product(){
        return $this->belongsTo(Product::class,'products_id');
    }
}
