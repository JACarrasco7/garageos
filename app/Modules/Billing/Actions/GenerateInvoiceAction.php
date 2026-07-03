<?php

namespace App\Modules\Billing\Actions;

use App\Modules\Billing\Models\PlatformFee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerateInvoiceAction
{
    public function execute(PlatformFee $fee): string
    {
        $account = $fee->stripeAccount;
        $user = $account->user;

        $pdf = Pdf::loadView('pdf.invoice', [
            'fee' => $fee,
            'account' => $account,
            'user' => $user,
        ]);

        $filename = "invoices/invoice-{$fee->id}-".time().'.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $fee->update(['invoice_path' => $filename]);

        return $filename;
    }
}
