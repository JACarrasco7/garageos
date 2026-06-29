<?php

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ServicePack extends Model
{
    protected $fillable = [
        'maintenance_type',
        'name',
        'items',
        'affiliate_link_ids',
    ];

    protected $casts = [
        'items' => 'json',
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

        return \App\Modules\Marketplace\Models\AffiliateLink::whereIn('id', $this->affiliate_link_ids)
            ->active()
            ->get();
    }
}
