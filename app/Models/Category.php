<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Category extends Model
{
    use HasApiTokens, HasFactory;
    protected $fillable = [
        "name",
        "name_ar",
        "image",
        "description_ar",
        "description",
        'status',
        'admin_choice',
    ];
    protected $hidden = [
    ];
    public function services()
    {
        return $this->hasMany(Service::class);
    }

}
