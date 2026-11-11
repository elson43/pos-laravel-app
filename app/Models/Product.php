<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'barcode',
        'name',
        'description',
        'stock',
        'price',
        'cost_price',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock < 10;
    }

    public function getMarginAmountAttribute(): float
    {
        return (float) ($this->price - $this->cost_price);
    }

    public function getMarginPercentAttribute(): float
    {
        if ($this->price <= 0) {
            return 0;
        }
        return round((($this->price - $this->cost_price) / $this->price) * 100, 1);
    }
}
