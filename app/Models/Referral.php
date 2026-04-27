<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = ['affiliate_id', 'client_id', 'property_id', 'status'];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }
}
