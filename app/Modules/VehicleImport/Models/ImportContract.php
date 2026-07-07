<?php

namespace App\Modules\VehicleImport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_import_id',
        'seller_full_name',
        'seller_document_id',
        'seller_address',
        'seller_city',
        'seller_country',
        'seller_email',
        'seller_phone',
        'buyer_full_name',
        'buyer_document_id',
        'buyer_address',
        'buyer_city',
        'buyer_country',
        'buyer_email',
        'buyer_phone',
        'agreed_price',
        'currency',
        'contract_date',
        'status',
        'pdf_path',
        'signed_at',
    ];

    public function vehicleImport(): BelongsTo
    {
        return $this->belongsTo(VehicleImport::class);
    }
}
