<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rl_number',
        'owner_name',
        'owner_phone',
        'manager_name',
        'manager_phone',
        'contact_person',
        'contact_person_phone',
        'email',
        'website',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
    ];

    // Accessor for formatted phone numbers
    public function getFormattedOwnerPhoneAttribute()
    {
        return $this->formatPhoneNumber($this->owner_phone);
    }

    public function getFormattedManagerPhoneAttribute()
    {
        return $this->manager_phone ? $this->formatPhoneNumber($this->manager_phone) : null;
    }

    public function getFormattedContactPersonPhoneAttribute()
    {
        return $this->contact_person_phone ? $this->formatPhoneNumber($this->contact_person_phone) : null;
    }

    private function formatPhoneNumber($phone)
    {
        // Format BD phone numbers
        if (str_starts_with($phone, '+880')) {
            return $phone;
        } elseif (str_starts_with($phone, '01')) {
            return '+880'.substr($phone, 1);
        }

        return $phone;
    }

    // Accessor for full address
    public function getFullAddressAttribute()
    {
        $parts = [];
        if ($this->address) {
            $parts[] = $this->address;
        }
        if ($this->city) {
            $parts[] = $this->city;
        }
        if ($this->state) {
            $parts[] = $this->state;
        }
        if ($this->zip_code) {
            $parts[] = $this->zip_code;
        }
        if ($this->country) {
            $parts[] = $this->country;
        }

        return implode(', ', $parts);
    }

    // Scope for search
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('rl_number', 'like', "%{$search}%")
            ->orWhere('owner_name', 'like', "%{$search}%")
            ->orWhere('owner_phone', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('contact_person', 'like', "%{$search}%");
    }
}
