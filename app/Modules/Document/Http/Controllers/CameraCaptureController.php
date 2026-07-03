<?php

namespace App\Modules\Documents\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CameraCaptureController extends Controller
{
    public function generateAuth(Request $request): JsonResponse
    {
        $request->validate([
            'document_type' => 'sometimes|string',
        ]);

        $token = Str::random(64);
        $expiresAt = now()->addMinutes(30);

        session()->put('camera_auth_'.$token, [
            'user_id' => auth()->id(),
            'expires_at' => $expiresAt,
            'document_type' => $request->input('document_type'),
        ]);

        return response()->json([
            'success' => true,
            'token' => $token,
            'expires_at' => $expiresAt->toISOString(),
        ]);
    }

    public function validateAuth(string $token): JsonResponse
    {
        $sessionData = session()->get('camera_auth_'.$token);

        if (! $sessionData) {
            return response()->json(['valid' => false, 'error' => 'Invalid token'], 401);
        }

        if ($sessionData['user_id'] !== auth()->id()) {
            return response()->json(['valid' => false, 'error' => 'Unauthorized'], 403);
        }

        if (now()->gt($sessionData['expires_at'])) {
            session()->forget('camera_auth_'.$token);

            return response()->json(['valid' => false, 'error' => 'Token expired'], 401);
        }

        return response()->json([
            'valid' => true,
            'document_type' => $sessionData['document_type'],
        ]);
    }

    public function revokeAuth(string $token): JsonResponse
    {
        session()->forget('camera_auth_'.$token);

        return response()->json(['success' => true]);
    }
}
