<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class ServiceVariable extends Model
{
    use HasFactory, HasApiTokens;
    // Specify the table name
    protected $table = 'service_veriables';
    protected $fillable = [
        'id',
        'variable',
        'service_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    // Inverse relationship back to Service
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
