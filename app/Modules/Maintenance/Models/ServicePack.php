<?php

namespace App\Modules\Maintenance\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePack extends Model
{
    protected $fillable = [
        'maintenance_type',
        'name',
        'items',
    ];

    protected $casts = [
        'items' => 'json',
    ];
}
