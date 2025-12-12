<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone1',
        'phone2',
        'address',
        'photo',
        'reference',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function visas()
    {
        return $this->hasMany(\App\Models\Visa::class);
    }

    public function accounts()
    {
        return $this->hasMany(\App\Models\Account::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // শুধুমাত্র Admin/Super Admin Role এর ইউজাররা ফিলামেন্টে প্রবেশ করতে পারবে
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Get the current balance attribute
     */
    public function getCurrentBalanceAttribute()
    {
        $debit = $this->visas->sum('visa_cost');
        $credit = $this->accounts->sum(fn ($acc) => $acc->transaction_type === 'deposit' ? $acc->amount : 0);
        $withdrawals = $this->accounts->sum(fn ($acc) => in_array($acc->transaction_type, ['withdrawal', 'refund']) ? $acc->amount : 0);

        return $credit - ($debit + $withdrawals);
    }

    /**
     * Get formatted balance with status
     */
    public function getFormattedBalanceAttribute(): string
    {
        $balance = $this->current_balance;
        $formatted = number_format($balance, 2);

        return $balance >= 0
            ? "{$formatted} BDT (Credit)"
            : "{$formatted} BDT (Due)";
    }
}
