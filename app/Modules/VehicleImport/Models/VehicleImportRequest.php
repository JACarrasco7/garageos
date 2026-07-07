<?php

namespace App\Modules\VehicleImport\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleImportRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand',
        'model',
        'year',
        'fuel_type',
        'mileage',
        'budget_min',
        'budget_max',
        'url_link',
        'description',
        'status',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(VehicleImportOffer::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }
}
