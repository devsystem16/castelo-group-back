<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'affiliate_id', 'referral_id', 'amount', 'commission_rate', 'status', 'paid_at', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }
}
