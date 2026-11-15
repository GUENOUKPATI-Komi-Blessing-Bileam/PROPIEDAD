<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $utilisateur = Utilisateur::query()
            ->where('email', $credentials['email'])
            ->where('actif', true)
            ->first();

        if (! $utilisateur || ! Hash::check($credentials['password'], $utilisateur->mot_de_passe_hash)) {
            return $this->problem(401, 'Identifiants invalides', 'Adresse e-mail ou mot de passe incorrect.');
        }

        $tokenTtl = (int) config('auth.api_token_ttl', 3600);
        $expiresAt = now()->addSeconds($tokenTtl);
        $token = $utilisateur->createToken('api', ['*'], $expiresAt);

        return response()->json([
            'access_token' => $token->plainTextToken,
            'expires_after_seconds' => $tokenTtl,
            'user' => $this->userPayload($utilisateur),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->userPayload($request->user()->load('roles')));
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->noContent();
    }

    private function userPayload(Utilisateur $utilisateur): array
    {
        return [
            'id' => $utilisateur->id,
            'name' => $utilisateur->nom,
            'email' => $utilisateur->email,
            'roles' => $utilisateur->roles()->pluck('roles.code')->all(),
        ];
    }

    private function problem(int $status, string $title, string $detail): JsonResponse
    {
        return response()->json([
            'type' => 'about:blank',
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'request_id' => request()->header('X-Request-Id', (string) str()->uuid()),
        ], $status, ['Content-Type' => 'application/problem+json']);
    }
}