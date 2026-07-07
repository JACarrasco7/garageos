<?php

namespace App\Modules\Vehicle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'file_path',
        'category',
        'caption',
        'sort_order',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'file_size' => 'integer',
    ];

    protected $appends = ['url', 'thumbnail_url'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getUrlAttribute(): string
    {
        return asset($this->file_path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if (! str_starts_with($this->file_path, 'http')) {
            $path = pathinfo($this->file_path, PATHINFO_DIRNAME);
            $filename = pathinfo($this->file_path, PATHINFO_FILENAME);
            $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);

            return asset("{$path}/{$filename}-thumb.{$extension}");
        }

        return $this->file_path;
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public function scopeMain($query)
    {
        return $query->where('category', 'principal')->first();
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function isMain(): bool
    {
        return $this->category === 'principal';
    }
}
