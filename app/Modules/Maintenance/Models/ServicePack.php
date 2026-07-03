<?php

namespace App\Modules\Maintenance\Models;

use App\Modules\Marketplace\Models\AffiliateLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ServicePack extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_type',
        'name',
        'items',
        'affiliate_link_ids',
    ];

    protected $casts = [
        'items' => 'array',
        'affiliate_link_ids' => 'array',
    ];

    /**
     * Get the affiliate links associated with this service pack.
     */
    public function getAffiliateLinks(): Collection
    {
        if (empty($this->affiliate_link_ids)) {
            return collect();
        }

        return AffiliateLink::whereIn('id', $this->affiliate_link_ids)
            ->active()
            ->get();
    }
}
