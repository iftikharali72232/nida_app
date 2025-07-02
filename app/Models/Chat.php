<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;


class Chat extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'customer_id',
        'text',
        'images',
        'audios',
        'is_admin',
        'is_read'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        
    ];
    public function customer()
    {
        return $this->belongsTo(User::class);
    }

}
