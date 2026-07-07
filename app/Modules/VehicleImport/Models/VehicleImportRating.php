<?php

namespace App\Modules\VehicleImport\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleImportRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_import_offer_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(VehicleImportOffer::class, 'vehicle_import_offer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
