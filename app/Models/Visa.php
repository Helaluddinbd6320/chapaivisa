<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'passenger_image',
        'passport',
        'phone_1',
        'phone_2',
        'user_id',
        'agent_id',
        'takamul_category',
        'takamul',
        'tasheer',
        'ttc',
        'bmet',
        'iqama',
        'embassy',
        'pc_ref',
        'visa_type',
        'medical_center_id',
        'medical_status',
        'medical_date',
        'mofa_number',
        'agency_id',
        'visa_number',
        'visa_id_number',
        'visa_date',
        'visa_condition',
        'passport_image',
        'slip_image',
        'visa_image',
        'slip_url',
        'report',
        'visa_cost',
    ];

    protected $casts = [
        'medical_date' => 'date',
        'visa_date' => 'date',
        'visa_cost' => 'decimal:2',
        'takamul' => 'string',
    ];

    // Relationships
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medicalCenter(): BelongsTo
    {
        return $this->belongsTo(MedicalCenter::class, 'medical_center_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    // Accessors
    public function getAgentNameAttribute()
    {
        return $this->agent?->name;
    }

    public function getMedicalNameAttribute()
    {
        return $this->medicalCenter?->name;
    }

    public function getAgencyNameAttribute()
    {
        return $this->agency?->name;
    }

    public function getStatusColorAttribute()
    {
        return match ($this->report) {
            'pending' => 'warning',
            'approved' => 'success',
            'completed' => 'primary',
            default => 'gray',
        };
    }

    public function getMedicalStatusColorAttribute()
    {
        if (!$this->medical_status) {
            return 'gray';
        }

        return match (strtolower($this->medical_status)) {
            'passed', 'yes', 'fit', 'clear' => 'success',
            'failed', 'no', 'unfit' => 'danger',
            'pending' => 'warning',
            default => 'info',
        };
    }

    public function getPhoneNumbersAttribute()
    {
        $phones = [];
        if ($this->phone_1) $phones[] = $this->phone_1;
        if ($this->phone_2) $phones[] = $this->phone_2;
        return implode(', ', $phones);
    }

    // Scopes
    public function scopePending($query) { return $query->where('report', 'pending'); }
    public function scopeApproved($query) { return $query->where('report', 'approved'); }
    public function scopeCompleted($query) { return $query->where('report', 'completed'); }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('passport', 'like', "%{$search}%")
            ->orWhere('phone_1', 'like', "%{$search}%")
            ->orWhere('visa_number', 'like', "%{$search}%")
            ->orWhere('mofa_number', 'like', "%{$search}%")
            ->orWhereHas('agency', fn($q)=> $q->where('name', 'like', "%{$search}%"))
            ->orWhereHas('agent', fn($q)=> $q->where('name', 'like', "%{$search}%"));
    }

    public function scopeByVisaType($query, $type) { return $query->where('visa_type', $type); }
    public function scopeByAgency($query, $agencyId) { return $query->where('agency_id', $agencyId); }

    // updated_at only changes if visa_cost changes
    public function save(array $options = [])
    {
        if (!array_key_exists('visa_cost', $this->getDirty())) {
            $this->timestamps = false;
        } else {
            $this->timestamps = true;
        }

        parent::save($options);
        $this->timestamps = true;
    }

    // Sorting scopes
    public function scopeVisaCostZeroFirst($query)
    {
        return $query->orderByRaw("CASE WHEN visa_cost = 0 THEN 0 ELSE 1 END ASC");
    }

    public function scopeLatestUpdated($query)
    {
        return $query->orderByDesc('updated_at');
    }
}
