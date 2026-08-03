<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vanilo\Order\Models\Order;

class MercadoLibreOrder extends Model
{
    protected $table = 'mercadolibre_orders';

    protected $fillable = [
        'order_id',
        'ml_order_id',
        'ml_buyer_id',
        'ml_buyer_nickname',
        'ml_status',
        'ml_shipping_id',
        'ml_payment_id',
        'raw_data',
        'synced_at',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'synced_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->ml_status) {
            'paid', 'confirmed' => 'Pagado',
            'payment_required' => 'Pendiente de Pago',
            'cancelled' => 'Cancelado',
            default => ucfirst((string) $this->ml_status),
        };
    }
}
