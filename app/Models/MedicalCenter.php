<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalCenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'country',
        'city',
        'phone',
        'email',
        'address',
        'contact_person',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor for full location
    public function getLocationAttribute()
    {
        $parts = [];
        if ($this->city) $parts[] = $this->city;
        if ($this->country) $parts[] = $this->country;
        
        return implode(', ', $parts);
    }

    public function getStatusColorAttribute()
    {
        return $this->status === 'active' ? 'success' : 'danger';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('country', 'like', "%{$search}%")
            ->orWhere('city', 'like', "%{$search}%")
            ->orWhere('contact_person', 'like', "%{$search}%");
    }
}