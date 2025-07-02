<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class WalletHistory extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'wallet_id',
        'amount',
        'is_deposite',
        'is_expanse',
        'description',
        'service_id',
        'is_read'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
    // Relationship to the Wallet model
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    // Relationship to the Service model
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
