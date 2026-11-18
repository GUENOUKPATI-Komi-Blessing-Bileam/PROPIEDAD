<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private const USER_ID = '8d39fb30-45c5-41b8-8ee6-221e9dcb6afd';

    public function test_authenticated_users_can_create_and_use_categories_in_asset_references(): void
    {
        $this->fakeAuthentication();

        $response = $this->withToken('valid-token')->postJson('/api/v1/categories', [
            'label' => 'Équipements médicaux',
            'description' => 'Matériel des centres de santé',
        ]);

        $response->assertCreated()
            ->assertJsonPath('label', 'Équipements médicaux')
            ->assertJsonPath('description', 'Matériel des centres de santé')
            ->assertJsonPath('status', 'Active');
        $this->assertDatabaseHas('categories', [
            'id' => $response->json('id'),
            'libelle' => 'Équipements médicaux',
            'description' => 'Matériel des centres de santé',
            'actif' => true,
        ]);

        $this->withToken('valid-token')
            ->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonPath('data.0.label', 'Équipements médicaux');
        $this->withToken('valid-token')
            ->getJson('/api/v1/assets/references')
            ->assertOk()
            ->assertJsonPath('categories.0.label', 'Équipements médicaux');
    }

    public function test_category_endpoints_require_a_bearer_token(): void
    {
        $this->getJson('/api/v1/categories')
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_category_creation_requires_a_label(): void
    {
        $this->fakeAuthentication();

        $this->withToken('valid-token')
            ->postJson('/api/v1/categories', [])
            ->assertInvalid([
                'label' => 'Le nom de la catégorie est obligatoire.',
            ]);
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_category_creation_rejects_duplicate_labels(): void
    {
        DB::table('categories')->insert([
            'id' => (string) Str::uuid(),
            'libelle' => 'Mobilier',
            'actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->fakeAuthentication();

        $this->withToken('valid-token')
            ->postJson('/api/v1/categories', ['label' => 'Mobilier'])
            ->assertInvalid([
                'label' => 'Une catégorie portant ce nom existe déjà.',
            ]);
        $this->assertDatabaseCount('categories', 1);
    }

    private function fakeAuthentication(): void
    {
        config(['asset.auth_url' => 'http://auth.internal']);
        Http::preventStrayRequests();
        Http::fake([
            'http://auth.internal/api/v1/auth/me' => Http::response([
                'id' => self::USER_ID,
                'email' => 'manager@example.gov',
                'roles' => ['gestionnaire'],
            ]),
        ]);
    }
}
