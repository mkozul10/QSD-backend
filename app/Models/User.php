<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       /*
        'name',
        'surname',
        'email',
        'password',
        'roles_id'
        */
        
        'name',
        'surname',
        'email',
        'password',
        'created_at',
        'updated_at',
        'address',
        'city',
        'zip_code',
        'phone',
        'roles_id',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class,'roles_id');
    }

    public function validationKeys(){
        return $this->hasMany(UsersValidationKeys::class,'users_id');
    }

    public function favorites(){
        return $this->belongsToMany(Product::class, 'favorites', 'users_id', 'products_id')
                    ->with(['color', 'brand', 'images', 'categories', 'sizes'])
                    ->withPivot(['created_at', 'updated_at']);
    }
    public static function columns()
    {
        $tableName = with(new static)->getTable();
        return Schema::getColumnListing($tableName);
    }
}