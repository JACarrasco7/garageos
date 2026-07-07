<?php

namespace App\Modules\VehicleImport\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Billing\Actions\CreatePaymentIntentAction;
use App\Modules\VehicleImport\Models\ImportPaymentMilestone;
use App\Modules\VehicleImport\Models\VehicleImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportPaymentController extends Controller
{
    public function __construct(
        protected CreatePaymentIntentAction $createPaymentIntentAction
    ) {}

    /**
     * Crea un nuevo hito de pago para una importación.
     */
    public function createMilestone(Request $request, VehicleImport $vehicleImport): JsonResponse
    {
        $this->authorize('update', $vehicleImport);

        $validated = $request->validate([
            'milestone' => ['required', 'in:H1_reserva,H2_compra,H3_entrega'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($vehicleImport, $validated) {
            // 1. Crear el hito en la DB
            $milestone = ImportPaymentMilestone::create([
                'vehicle_import_id' => $vehicleImport->id,
                'milestone' => $validated['milestone'],
                'amount' => $validated['amount'],
                'status' => 'pending',
            ]);

            // 2. Crear el PaymentIntent en Stripe
            $paymentIntent = $this->createPaymentIntentAction->execute(
                amount: $validated['amount'],
                currency: 'EUR',
                description: "Pago Hito {$validated['milestone']} - Importación #{$vehicleImport->id}",
                sellerId: $vehicleImport->importer_id ?? $vehicleImport->user_id, // Fallback si no hay importador aún
                metadata: [
                    'vehicle_import_id' => (string) $vehicleImport->id,
                    'milestone' => $validated['milestone'],
                ],
                vehicleImportId: $vehicleImport->id
            );

            // 3. Vincular el PaymentIntent al hito
            $milestone->update([
                'payment_intent_id' => $paymentIntent->id,
            ]);

            return response()->json([
                'milestone' => $milestone,
                'payment_intent' => $paymentIntent,
            ]);
        });
    }

    /**
     * Marca un hito como pagado (llamado por el Webhook de Stripe).
     */
    public function markAsPaid(Request $request): JsonResponse
    {
        $paymentIntentId = $request->input('payment_intent_id');

        $milestone = ImportPaymentMilestone::where('payment_intent_id', $paymentIntentId)->firstOrFail();

        $milestone->update([
            'status' => 'paid',
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Libera el dinero al importador (solo para H1 y H2).
     * H3 se libera cuando delivery_confirmed_at está establecido.
     */
    public function releaseMilestone(Request $request, ImportPaymentMilestone $milestone): JsonResponse
    {
        $this->authorize('update', $milestone->vehicleImport);

        if (in_array($milestone->milestone, ['H1_reserva', 'H2_compra'])) {
            return DB::transaction(function () use ($milestone) {
                $milestone->update([
                    'status' => 'released',
                    'released_at' => now(),
                ]);

                return response()->json(['success' => true, 'message' => 'Fondos liberados al importador.']);
            });
        }

        if ($milestone->milestone === 'H3_entrega') {
            $import = $milestone->vehicleImport;
            if (! $import->delivery_confirmed_at) {
                return response()->json(['error' => 'La entrega debe estar confirmada antes de liberar el hito H3.'], 400);
            }
            if (empty($import->plate_new)) {
                return response()->json(['error' => 'La matrícula definitiva es obligatoria para liberar el hito H3.'], 400);
            }
            if (empty($import->itv_deadline)) {
                return response()->json(['error' => 'La fecha límite de ITV es obligatoria para liberar el hito H3.'], 400);
            }

            return DB::transaction(function () use ($milestone) {
                $milestone->update([
                    'status' => 'released',
                    'released_at' => now(),
                ]);

                return response()->json(['success' => true, 'message' => 'Fondos liberados al importador.']);
            });
        }

        return response()->json(['error' => 'Hito desconocido.'], 400);
    }
}
