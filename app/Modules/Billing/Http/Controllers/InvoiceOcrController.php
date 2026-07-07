<?php

namespace App\Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Services\InvoiceOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvoiceOcrController extends Controller
{
    public function __construct(
        protected InvoiceOcrService $ocrService
    ) {}

    public function create()
    {
        return Inertia::render('Billing/InvoiceScan');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $path = Storage::put('temp/ocr', $validated['invoice']);
        $fullPath = Storage::path($path);

        $data = $this->ocrService->extractInvoiceData($fullPath);

        Storage::delete($path);

        return back()->with([
            'extracted_data' => $data,
            'invoice_path' => $validated['invoice']->store('invoices'),
        ]);
    }
}
