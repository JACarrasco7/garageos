<?php

namespace App\Modules\VehicleImport\Models;

use App\Models\User;
use App\Modules\Billing\Models\PaymentIntent;
use App\Modules\Vehicle\Models\Vehicle;
use App\Modules\VehicleImport\Enums\ImportStep;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VehicleImport extends Model
{
    use HasFactory;

    protected $table = 'vehicle_imports';

    protected $fillable = [
        'user_id',
        'importer_id',
        'vehicle_id',
        'listing_id',
        'vehicle_import_offer_id',
        'plate_original',
        'vin',
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
        'delivery_confirmed_at',
        'current_step',
        'needs_homologation',
        'status',
        'rejection_reason',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function importer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'importer_id');
    }

    protected $casts = [
        'year' => 'integer',
        'engine_cc' => 'integer',
        'power_kw' => 'integer',
        'co2_emissions' => 'integer',
        'purchase_date' => 'date',
        'arrival_date' => 'date',
        'itv_deadline' => 'date',
        'delivery_confirmed_at' => 'datetime',
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

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function verification()
    {
        return $this->hasOne(VehicleVerification::class);
    }

    public function transportEvents(): HasMany
    {
        return $this->hasMany(TransportEvent::class);
    }

    public function contract(): HasOne
    {
        return $this->hasOne(ImportContract::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(VehicleImportOffer::class, 'vehicle_import_offer_id');
    }

    public function importDocuments(): HasMany
    {
        return $this->hasMany(ImportDocument::class, 'import_id');
    }

    public function temporaryPlates(): HasMany
    {
        return $this->hasMany(TemporaryPlate::class, 'import_id');
    }

    public function paymentMilestones(): HasMany
    {
        return $this->hasMany(ImportPaymentMilestone::class);
    }

    public function paymentIntents(): HasMany
    {
        return $this->hasMany(PaymentIntent::class);
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
            if (! $documents->contains('type', $docType)) {
                return false;
            }
        }

        return true;
    }

    public function canAdvanceToStep(ImportStep $step): bool
    {
        $previous = $step->getPrevious();

        if (! $previous) {
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

    public function syncToGarage($import): void
    {
        $vehicle = Vehicle::updateOrCreate(
            [
                'plate' => $import->plate_new ?? $import->plate_original,
            ],
            [
                'garage_id' => $import->user->garages()->first()?->id,
                'brand' => $import->brand,
                'model' => $import->model,
                'year' => $import->year,
                'engine_cc' => $import->engine_cc,
                'power_kw' => $import->power_kw,
                'imported_from' => $import->origin_country,
                'import_date' => $import->purchase_date,
            ]
        );

        $import->update([
            'vehicle_id' => $vehicle->id,
        ]);
    }

    public function validateVIN(): bool
    {
        if (!$this->vin) {
            return false;
        }

        return strlen($this->vin) === 17;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'processing' => 'En proceso',
            'completed' => 'Completado',
            'rejected' => 'Rechazado',
            default => 'Desconocido',
        };
    }

    public function calculateImportTaxes(float $purchasePrice): array
    {
        $co2 = $this->co2_emissions ?? 120;
        $age = now()->year - $this->year;

        $depreciationCoeff = match (true) {
            $age < 1 => 0.84,
            $age < 2 => 0.67,
            $age < 3 => 0.56,
            $age < 4 => 0.47,
            $age < 5 => 0.39,
            $age < 6 => 0.33,
            $age < 7 => 0.28,
            $age < 8 => 0.24,
            $age < 9 => 0.18,
            $age < 10 => 0.14,
            default => 0.10,
        };

        $taxRate = match (true) {
            $co2 <= 120 => 0.00,
            $co2 <= 159 => 0.0475,
            $co2 <= 199 => 0.0975,
            default => 0.1475,
        };

        $catalogPrice = $purchasePrice / $depreciationCoeff;
        $iedmt = $catalogPrice * $taxRate;
        $itpRate = 0.10;
        $itp = $purchasePrice * $itpRate;

        return [
            'catalog_value' => round($catalogPrice, 2),
            'iedmt' => round($iedmt, 2),
            'itp_estimate' => round($itp, 2),
            'co2_rate' => $taxRate,
            'depreciation_coefficient' => $depreciationCoeff,
        ];
    }
}
