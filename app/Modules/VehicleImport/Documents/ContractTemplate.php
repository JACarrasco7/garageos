<?php

namespace App\Modules\VehicleImport\Documents;

use App\Modules\VehicleImport\Models\VehicleImportOffer;

class ContractTemplate
{
    public function generateSpanish(VehicleImportOffer $offer): string
    {
        $data = [
            'offer' => $offer,
            'request' => $offer->request,
            'provider' => $offer->provider,
            'client' => $offer->request->user,
            'date' => now()->format('d/m/Y'),
        ];

        return view('documents.contract-spanish', $data)->render();
    }

    public function generateGerman(VehicleImportOffer $offer): string
    {
        $data = [
            'offer' => $offer,
            'request' => $offer->request,
            'provider' => $offer->provider,
            'client' => $offer->request->user,
            'date' => now()->format('d.m.Y'),
        ];

        return view('documents.contract-german', $data)->render();
    }
}
