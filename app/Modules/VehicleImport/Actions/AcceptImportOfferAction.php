<?php

namespace App\Modules\VehicleImport\Actions;

use App\Modules\VehicleImport\Enums\ImportStep;
use App\Modules\VehicleImport\Models\VehicleImport;
use App\Modules\VehicleImport\Models\VehicleImportOffer;
use Illuminate\Support\Facades\DB;

class AcceptImportOfferAction
{
    public function execute(VehicleImportOffer $offer): VehicleImport
    {
        return DB::transaction(function () use ($offer) {
            $offer->update(['status' => 'accepted']);

            $request = $offer->request;
            $request->update(['status' => 'closed']);

            $import = VehicleImport::create([
                'user_id' => $request->user_id,
                'vehicle_import_offer_id' => $offer->id,
                'brand' => $request->brand,
                'model' => $request->model,
                'year' => $request->year,
                'origin_country' => 'DE',
                'current_step' => ImportStep::PURCHASE,
                'status' => 'pending',
            ]);

            return $import;
        });
    }
}
