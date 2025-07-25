<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Shop extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        "name",
        "user_id",
        "fvrt",
        "visted",
        "shop_id",
    ];
    protected $hidden = [
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
