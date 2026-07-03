<?php

namespace App\Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'product_name',
        'product_sku',
        'affiliate_url',
        'image_url',
        'price',
        'currency',
        'metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeBySku($query, string $sku)
    {
        return $query->where('product_sku', $sku);
    }

    public function getFormattedPrice(): string
    {
        return $this->price ? number_format($this->price, 2).' '.$this->currency : 'Precio no disponible';
    }
}
