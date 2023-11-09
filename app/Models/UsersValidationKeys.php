<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersValidationKeys extends Model
{
    use HasFactory;
    protected $fillable = ['validation_key', 'users_id'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
