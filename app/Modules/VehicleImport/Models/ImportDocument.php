<?php

namespace App\Modules\VehicleImport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\VehicleImport\Enums\ImportStep;

class ImportDocument extends Model
{
    protected $table = 'import_documents';

    protected $fillable = [
        'import_id',
        'step',
        'type',
        'file_path',
        'is_verified',
    ];

    protected $casts = [
        'step' => ImportStep::class,
        'is_verified' => 'boolean',
    ];

    protected $attributes = [
        'is_verified' => false,
    ];

    public function vehicleImport(): BelongsTo
    {
        return $this->belongsTo(VehicleImport::class, 'import_id');
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'compraventa' => 'Contrato de Compraventa (Kaufvertrag)',
            'coc' => 'Certificado de Conformidad (COC)',
            'ficha_tecnica_origen' => 'Ficha Técnica Alemana (Fahrzeugbrief)',
            'tarjeta_itv_origen' => 'Tarjeta ITV/TÜV Alemana',
            'seguro_transporte' => 'Seguro de Transporte',
            'ficha_itv_es' => 'Ficha Técnica Española (ITV Importación)',
            'modelo_576' => 'Modelo 576 (IEDMT)',
            'modelo_309_300' => 'Modelo 309/300 (IVA Intracomunitario)',
            'modelo_itp' => 'Liquidación ITP',
            'justificante_ivtm' => 'Justificante Pago IVTM',
            'permiso_circulacion' => 'Permiso de Circulación',
            'otro' => 'Otro Documento',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function scopeByStep($query, ImportStep $step)
    {
        return $query->where('step', $step);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }
}