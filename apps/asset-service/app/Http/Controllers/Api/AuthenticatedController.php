<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class AuthenticatedController extends Controller
{
    protected function authenticatedUserId(Request $request): string|JsonResponse
    {
        $token = $request->bearerToken();
        if (! $token) {
            return $this->problem(401, 'Authentification requise', 'Un jeton valide est requis pour accéder aux biens.');
        }

        try {
            $response = Http::timeout(5)
                ->withToken($token)
                ->acceptJson()
                ->get(rtrim(config('asset.auth_url'), '/').'/api/v1/auth/me');
        } catch (ConnectionException) {
            return $this->problem(503, 'Service indisponible', 'Le service d’authentification est temporairement indisponible.');
        }

        if ($response->unauthorized()) {
            return $this->problem(401, 'Authentification requise', 'Le jeton fourni est invalide ou expiré.');
        }

        $userId = $response->json('id');
        if (! $response->successful() || ! is_string($userId) || ! Str::isUuid($userId)) {
            return $this->problem(503, 'Service indisponible', 'Le service d’authentification n’a pas fourni un profil valide.');
        }

        return $userId;
    }

    protected function problem(int $status, string $title, string $detail): JsonResponse
    {
        return response()->json([
            'type' => 'about:blank',
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'request_id' => request()->header('X-Request-Id', (string) Str::uuid()),
        ], $status, ['Content-Type' => 'application/problem+json']);
    }
}
