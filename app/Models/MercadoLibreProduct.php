<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vanilo\Product\Models\Product;

class MercadoLibreProduct extends Model
{
    protected $table = 'mercadolibre_products';

    protected $fillable = [
        'product_id',
        'ml_item_id',
        'ml_category_id',
        'title',
        'price',
        'available_quantity',
        'status',
        'permalink',
        'listing_type_id',
        'condition',
        'last_synced_at',
        'sync_error',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'available_quantity' => 'integer',
        'last_synced_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'badge bg-success',
            'paused' => 'badge bg-warning',
            'closed' => 'badge bg-danger',
            default => 'badge bg-secondary',
        };
    }
}
