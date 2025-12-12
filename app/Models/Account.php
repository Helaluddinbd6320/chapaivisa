<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'transaction_type',
        'amount',
        'payment_method',
        'bank_name',
        'account_number',
        'mobile_banking_provider',
        'mobile_number',
        'payment_date',
        'receipt_number',
        'receipt_image',
        'reference_number',
        'status',
        'is_verified',
        'verified_by',
        'verified_at',
        'description',
        'remarks',
        // নতুন ফিল্ড যোগ করুন
        'purpose',
        'party_name',
        'party_phone',
        'category',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Accessors
    public function getTransactionTypeLabelAttribute()
    {
        return match($this->transaction_type) {
            'deposit' => 'জমা',
            'withdrawal' => 'উত্তোলন',
            'refund' => 'ফেরত',
            default => 'Unknown',
        };
    }

    public function getTransactionTypeColorAttribute()
    {
        return match($this->transaction_type) {
            'deposit' => 'success',
            'withdrawal' => 'danger',
            'refund' => 'info',
            default => 'gray',
        };
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'নগদ',
            'bank' => 'ব্যাংক ট্রান্সফার',
            'mobile_banking' => 'মোবাইল ব্যাংকিং',
            'card' => 'কার্ড',
            default => 'অন্যান্য',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'verified' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    

    // Scopes
    public function scopeDeposits($query)
    {
        return $query->where('transaction_type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('transaction_type', 'withdrawal');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    // Balance Calculation Methods
    public static function getTotalDeposits()
    {
        return self::verified()->deposits()->sum('amount');
    }

    public static function getTotalWithdrawals()
    {
        return self::verified()->withdrawals()->sum('amount');
    }

    public static function getCurrentBalance()
    {
        return self::getTotalDeposits() - self::getTotalWithdrawals();
    }

    public static function getUserDeposits($userId)
    {
        return self::where('user_id', $userId)
            ->verified()
            ->deposits()
            ->sum('amount');
    }

    public static function getUserWithdrawals($userId)
    {
        return self::where('user_id', $userId)
            ->verified()
            ->withdrawals()
            ->sum('amount');
    }

    public static function getUserBalance($userId)
    {
        return self::getUserDeposits($userId) - self::getUserWithdrawals($userId);
    }
}