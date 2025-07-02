<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $table = 'service_orders';

    protected $fillable = [
        'service_id',
        'variables_json',
        'service_cost',
        'service_date',
        'tax',
        'discount',
        'customer_id',
        'status'
    ];

    protected $casts = [
        'variables_json' => 'array',
    ];
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

}
