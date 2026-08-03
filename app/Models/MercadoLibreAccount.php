<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MercadoLibreAccount extends Model
{
    protected $table = 'mercadolibre_accounts';

    protected $fillable = [
        'user_id',
        'ml_user_id',
        'nickname',
        'access_token',
        'refresh_token',
        'expires_at',
        'site_id',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected' && !empty($this->access_token);
    }

    public function isTokenExpired(): bool
    {
        if (!$this->expires_at) {
            return true;
        }

        // Expired if less than 5 minutes remaining
        return Carbon::now()->addMinutes(5)->greaterThanOrEqualTo($this->expires_at);
    }

    public static function defaultAccount(): ?self
    {
        return self::where('status', 'connected')->first() ?: self::first();
    }
}
