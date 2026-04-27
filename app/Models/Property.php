<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'type', 'province', 'canton',
        'price', 'area_m2', 'status', 'soil_type', 'access_services',
        'legal_documents', 'latitude', 'longitude', 'published', 'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area_m2' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'published' => 'boolean',
        'views' => 'integer',
    ];

    public function media()
    {
        return $this->hasMany(PropertyMedia::class)->orderBy('order');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
