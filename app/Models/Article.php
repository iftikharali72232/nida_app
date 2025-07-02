<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Article extends Model
{
    protected $fillable = ['title', 'service_id', 'text', 'gallery_images'];
    protected $casts = [
        'gallery_images' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}