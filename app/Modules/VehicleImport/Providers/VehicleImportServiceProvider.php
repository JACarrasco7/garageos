<?php

namespace App\Modules\VehicleImport\Providers;

use App\Modules\VehicleImport\Http\Controllers\DocumentVerificationController;
use App\Modules\VehicleImport\Http\Controllers\ImportDocumentController;
use App\Modules\VehicleImport\Http\Controllers\ImportPaymentController;
use App\Modules\VehicleImport\Http\Controllers\PublicVehiclePortfolioController;
use App\Modules\VehicleImport\Http\Controllers\PublicVerificationController;
use App\Modules\VehicleImport\Http\Controllers\TransportEventController;
use App\Modules\VehicleImport\Http\Controllers\VehicleImportController;
use App\Modules\VehicleImport\Http\Controllers\VehicleImportOfferController;
use App\Modules\VehicleImport\Http\Controllers\VehicleImportRequestController;
use App\Modules\VehicleImport\Http\Controllers\VehicleVerificationController;
use App\Modules\VehicleImport\Http\Controllers\VinDecoderController;
use App\Modules\VehicleImport\Models\VehicleImport;
use App\Modules\VehicleImport\Policies\VehicleImportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class VehicleImportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(
            VehicleImport::class,
            VehicleImportPolicy::class
        );

        Route::middleware(['web', 'auth'])->group(function () {
            // Rutas existentes del proceso de importación
            Route::get('/imports', [VehicleImportController::class, 'index'])->name('imports.index');
            Route::get('/imports/create', [VehicleImportController::class, 'create'])->name('imports.create');
            Route::post('/imports', [VehicleImportController::class, 'store'])->name('imports.store');
            Route::get('/imports/{import}', [VehicleImportController::class, 'show'])->name('imports.show');
            Route::get('/imports/{import}/wizard', [VehicleImportController::class, 'wizard'])->name('import.wizard');
            Route::post('/imports/{import}/upload', [VehicleImportController::class, 'uploadDocument'])->name('import.upload');
            Route::patch('/imports/{import}/step', [VehicleImportController::class, 'updateStep'])->name('import.update-step');
            Route::post('/imports/{import}/process', [VehicleImportController::class, 'process'])->name('imports.process');
            Route::delete('/imports/documents/{document}', [ImportDocumentController::class, 'destroy'])->name('import.documents.destroy');
            Route::get('/imports/{import}/valuation', [VehicleImportController::class, 'valuation'])->name('import.valuation');
            Route::post('/imports/{import}/certificate', [VehicleImportController::class, 'generateCertificate'])->name('import.certificate');
            Route::post('/imports/{import}/confirm-delivery', [VehicleImportController::class, 'confirmDelivery'])->name('import.confirm-delivery');
            Route::get('/imports/{import}/providers', [VehicleImportController::class, 'suggestProviders'])->name('import.providers');

            // Verificación y Certificación GarageOS
            Route::get('/imports/{import}/verify', [VehicleVerificationController::class, 'index'])->name('import.verify');
            Route::patch('/imports/{import}/verify', [VehicleVerificationController::class, 'update'])->name('import.verify.update');
            Route::post('/imports/{import}/verify/report', [VehicleVerificationController::class, 'generateReport'])->name('import.verify.report');
            Route::post('/imports/{import}/verify/detailed', [VehicleVerificationController::class, 'generateDetailedReport'])->name('import.verify.detailed');

            // Hitos de pago para importaciones
            Route::post('/imports/{import}/payments/milestone', [ImportPaymentController::class, 'createMilestone'])->name('import.payments.milestone.create');
            Route::post('/imports/milestones/{milestone}/release', [ImportPaymentController::class, 'releaseMilestone'])->name('import.payments.milestone.release');

            // Eventos de tracking logístico
            Route::get('/imports/{import}/tracking', [TransportEventController::class, 'index'])->name('import.tracking.index');
            Route::post('/imports/{import}/tracking', [TransportEventController::class, 'store'])->name('import.tracking.store');

            // VIN Decoder API
            Route::post('/vin/decode', [VinDecoderController::class, 'decode'])->name('vin.decode');

            // Rutas para el sourcing (solicitudes y ofertas)
            Route::get('/vehicle-import/requests', [VehicleImportRequestController::class, 'index'])->name('vehicle-import.requests.index');
            Route::get('/vehicle-import/requests/create', [VehicleImportRequestController::class, 'create'])->name('vehicle-import.requests.create');
            Route::post('/vehicle-import/requests', [VehicleImportRequestController::class, 'store'])->name('vehicle-import.requests.store');
            Route::get('/vehicle-import/requests/{request}', [VehicleImportRequestController::class, 'show'])->name('vehicle-import.requests.show');

            Route::get('/vehicle-import/offers', [VehicleImportOfferController::class, 'index'])->name('vehicle-import.offers.index');
            Route::post('/vehicle-import/offers', [VehicleImportOfferController::class, 'store'])->name('vehicle-import.offers.store');
            Route::post('/vehicle-import/offers/{offer}/accept', [VehicleImportOfferController::class, 'accept'])->name('vehicle-import.offers.accept');

            // Verificación de documentos
            Route::post('/verify/id', [DocumentVerificationController::class, 'validateId'])->name('verify.id');
            Route::post('/verify/document', [DocumentVerificationController::class, 'verifyDocument'])->name('verify.document');
            Route::post('/verify/contract', [DocumentVerificationController::class, 'verifyContract'])->name('verify.contract');
        });

        // Rutas públicas
        Route::get('/vehicle-import/portfolio/{user}', [PublicVehiclePortfolioController::class, 'show'])->name('vehicle-import.portfolio.show');
        Route::get('/verify/{certificateId}', [PublicVerificationController::class, 'show'])->name('verification.show');
    }
}
