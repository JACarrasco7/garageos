<?php

namespace App\Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Vehicle\Exports\UserDataExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DataExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function export(): JsonResponse
    {
        $user = auth()->user();
        $filename = "user-data-{$user->id}-".now()->format('Y-m-d').'.xlsx';

        Excel::store(new UserDataExport($user->id), $filename, 'private');

        return response()->json([
            'url' => route('data-export.download', ['file' => $filename]),
            'expires_at' => now()->addHours(24)->toIso8601String(),
        ]);
    }

    public function download(string $file)
    {
        $user = auth()->user();
        if (! Storage::disk('private')->exists($file) || ! str_contains($file, "user-data-{$user->id}-")) {
            abort(404);
        }

        return Storage::disk('private')->download($file);
    }

    public function deleteAccount(): JsonResponse
    {
        $user = auth()->user();

        $user->garages()->each(fn ($garage) => $garage->vehicles()->each(fn ($vehicle) => $vehicle->documents()->delete()));
        $user->garages()->each(fn ($garage) => $garage->vehicles()->delete());
        $user->garages()->delete();
        $user->notifications()->delete();
        $user->forceDelete();

        auth()->logout();

        return response()->json(['message' => 'Cuenta eliminada correctamente']);
    }
}
