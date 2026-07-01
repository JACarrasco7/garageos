<?php

namespace App\Modules\VehicleImport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\VehicleImport\Enums\ImportStep;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleImport extends Model
{
    protected $table = 'vehicle_imports';

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'listing_id',
        'plate_original',
        'plate_new',
        'brand',
        'model',
        'year',
        'engine_cc',
        'power_kw',
        'co2_emissions',
        'origin_country',
        'purchase_date',
        'arrival_date',
        'itv_deadline',
        'current_step',
        'needs_homologation',
        'status',
        'documents',
        'rejection_reason',
    ];

    protected $casts = [
        'year' => 'integer',
        'engine_cc' => 'integer',
        'power_kw' => 'integer',
        'co2_emissions' => 'integer',
        'purchase_date' => 'date',
        'arrival_date' => 'date',
        'itv_deadline' => 'date',
        'needs_homologation' => 'boolean',
        'documents' => 'array',
        'current_step' => ImportStep::class,
    ];

    protected $attributes = [
        'origin_country' => 'DE',
        'current_step' => ImportStep::PURCHASE,
        'needs_homologation' => false,
        'status' => 'pending',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Vehicle\Models\Vehicle::class);
    }

    public function importDocuments(): HasMany
    {
        return $this->hasMany(ImportDocument::class, 'import_id');
    }

    public function temporaryPlates(): HasMany
    {
        return $this->hasMany(TemporaryPlate::class, 'import_id');
    }

    public function calculateItvDeadline(): void
    {
        if ($this->arrival_date) {
            $this->itv_deadline = $this->arrival_date->addDays(30);
        }
    }

    public function isStepCompleted(ImportStep $step): bool
    {
        $documents = $this->importDocuments()
            ->where('step', $step->value)
            ->where('is_verified', true)
            ->get();

        $requiredDocs = $step->getRequiredDocuments();

        foreach ($requiredDocs as $docType) {
            if (!$documents->contains('type', $docType)) {
                return false;
            }
        }

        return true;
    }

    public function canAdvanceToStep(ImportStep $step): bool
    {
        $previous = $step->getPrevious();

        if (!$previous) {
            return true;
        }

        return $this->isStepCompleted($previous);
    }

    public function getCurrentStepOrder(): int
    {
        return $this->current_step->getOrder();
    }

    public function getProgressPercentage(): int
    {
        $order = $this->getCurrentStepOrder();
        $totalSteps = 6;

        return intdiv(($order - 1) * 100, $totalSteps);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'rejected')
            ->where('status', '!=', 'completed');
    }

    public function scopeByStep($query, ImportStep $step)
    {
        return $query->where('current_step', $step);
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending')
            ->orWhere('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('current_step', ImportStep::COMPLETED);
    }
}

namespace App\Modules\VehicleImport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleImport extends Model
{
    protected $fillable = [
        'user_id',
        'plate_original',
        'plate_new',
        'brand',
        'model',
        'year',
        'engine_cc',
        'power_kw',
        'status',
        'documents',
        'rejection_reason',
    ];

    protected $casts = [
        'documents' => 'array',
        'year' => 'integer',
        'engine_cc' => 'integer',
        'power_kw' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Vehicle\Models\Vehicle::class);
    }
}
