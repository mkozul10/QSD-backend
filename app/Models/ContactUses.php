<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUses extends Model
{
    use HasFactory;

    protected $table = 'contact_uses';

    protected $fillable = [
        'email',
        'subject',
        'message',
        'name'
    ];
    public $timestamps = true;
}
