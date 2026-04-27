<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMedia extends Model
{
    protected $fillable = ['property_id', 'media_type', 'is_cover', 'url', 'filename', 'order'];

    protected $casts = ['is_cover' => 'boolean'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
