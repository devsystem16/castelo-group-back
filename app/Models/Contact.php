<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'message', 'property_id', 'read'];

    protected $casts = ['read' => 'boolean'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
