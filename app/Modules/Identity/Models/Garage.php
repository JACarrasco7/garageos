<?php

namespace App\Modules\Identity\Models;

use App\Models\User;
use App\Modules\Vehicle\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Garage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    public function vehicleCount(): int
    {
        return $this->vehicles()->count();
    }

    public function activeVehicles(): HasMany
    {
        return $this->vehicles()->where('is_active', true);
    }
}
