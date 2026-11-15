<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_login_read_profile_and_revoke_token(): void
    {
        $utilisateur = Utilisateur::query()->create([
            'id' => '8d39fb30-45c5-41b8-8ee6-221e9dcb6afd',
            'nom' => 'Gestionnaire de test',
            'email' => 'manager@example.gov',
            'mot_de_passe_hash' => Hash::make('correct-horse-battery'),
            'actif' => true,
        ]);
        $role = Role::query()->create([
            'id' => '6ab3ed88-5c0c-411b-96f5-c443b2062d68',
            'code' => 'gestionnaire',
            'libelle' => 'Gestionnaire',
            'actif' => true,
        ]);
        $utilisateur->roles()->attach($role->id);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'manager@example.gov',
            'password' => 'correct-horse-battery',
        ])->assertOk()
            ->assertJsonPath('user.id', $utilisateur->id)
            ->assertJsonPath('user.roles.0', 'gestionnaire')
            ->assertJsonPath('expires_after_seconds', 3600);

        $token = $login->json('access_token');

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('email', 'manager@example.gov');

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertNoContent();

        Auth::forgetGuards();
        $this->withToken($token)->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_inactive_or_invalid_user_cannot_login(): void
    {
        Utilisateur::query()->create([
            'id' => 'c99bd08a-86da-46a5-9d6d-71d36be23a22',
            'nom' => 'Compte suspendu',
            'email' => 'inactive@example.gov',
            'mot_de_passe_hash' => Hash::make('correct-horse-battery'),
            'actif' => false,
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'inactive@example.gov',
            'password' => 'correct-horse-battery',
        ])->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/problem+json');

        $this->postJson('/api/v1/auth/login', [
            'email' => 'not-an-email',
            'password' => 'x',
        ])->assertUnprocessable();
    }
}