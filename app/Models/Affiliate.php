<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'cedula', 'whatsapp', 'email',
        'bank_name', 'account_number', 'account_type', 'description',
        'referral_code', 'status', 'commission_rate',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Affiliate $affiliate) {
            if (empty($affiliate->referral_code)) {
                $affiliate->referral_code = strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function pendingCommissionsTotal(): float
    {
        return (float) $this->commissions()->where('status', 'pending')->sum('amount');
    }

    public function paidCommissionsTotal(): float
    {
        return (float) $this->commissions()->where('status', 'paid')->sum('amount');
    }
}
