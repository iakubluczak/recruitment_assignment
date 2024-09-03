<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['created_at', 'updated_at'];

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

    public function getBirthdateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d.m.Y');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function scopeWithLastPurchaseDate(Builder $query): Builder
    {
        return $query->addSelect(
            DB::raw('MAX(purchases.purchase_date) as last_purchase_date')
        )
            ->leftJoin('purchases', 'users.id', '=', 'purchases.user_id')
            ->groupBy('users.id');
    }

    public function scopeOrderByBirthday(Builder $query): Builder
    {
        return $query->orderByRaw('strftime(\'%m\', birthdate) ASC, strftime(\'%d\', birthdate) ASC');
    }

    public function scopeHavingBirthdayThisWeek(Builder $query): Builder
    {
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = now()->endOfWeek(Carbon::SUNDAY);
    
        return $query->whereBetween(
            DB::raw('strftime(\'%m-%d\', birthdate)'), 
            [
                $startOfWeek->format('m-d'), 
                $endOfWeek->format('m-d')
            ]
        );
    }
}
