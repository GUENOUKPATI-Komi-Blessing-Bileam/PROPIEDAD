<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class AssetApiTest extends TestCase
{
    use RefreshDatabase;

    private const USER_ID = '8d39fb30-45c5-41b8-8ee6-221e9dcb6afd';

    private const CATEGORY_ID = 'bdbfbeae-41b8-4f16-80cf-8e0de23d3111';

    private const SERVICE_ID = '3a6bb2a4-0988-47a8-88da-12e25e0ae81f';

    private const SITE_ID = 'c7d95f2d-3bed-433a-b595-74e050ce3fd3';

    protected function setUp(): void
    {
        parent::setUp();

        config(['asset.auth_url' => 'http://auth.internal']);
        Http::fake([
            'http://auth.internal/api/v1/auth/me' => Http::response([
                'id' => self::USER_ID,
                'email' => 'manager@example.gov',
                'roles' => ['gestionnaire'],
            ]),
        ]);
    }

    public function test_references_are_available_to_authenticated_users(): void
    {
        $this->seedReferences();

        $this->withToken('valid-token')
            ->getJson('/api/v1/assets/references')
            ->assertOk()
            ->assertJsonPath('categories.0.id', self::CATEGORY_ID)
            ->assertJsonPath('services.0.id', self::SERVICE_ID)
            ->assertJsonPath('sites.0.id', self::SITE_ID);
    }

    public function test_authenticated_user_can_create_and_list_an_asset(): void
    {
        $this->seedReferences();

        $this->withToken('valid-token')
            ->postJson('/api/v1/assets', [
                'label' => 'Ordinateur de démonstration',
                'description' => 'Créé depuis le formulaire',
                'category_id' => self::CATEGORY_ID,
                'service_id' => self::SERVICE_ID,
                'site_id' => self::SITE_ID,
                'condition' => 'good',
                'acquisition_date' => '2026-11-15',
                'acquisition_value' => 250000,
                'currency' => 'XOF',
            ])
            ->assertCreated()
            ->assertJsonPath('label', 'Ordinateur de démonstration')
            ->assertJsonPath('category_id', self::CATEGORY_ID)
            ->assertJsonPath('status', 'active')
            ->assertJsonPath('inventory_code', fn ($code) => str_starts_with($code, 'INV-'.now()->format('Y').'-'));

        $this->withToken('valid-token')
            ->getJson('/api/v1/assets?q=Ordinateur')
            ->assertOk()
            ->assertJsonPath('data.0.label', 'Ordinateur de démonstration')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_asset_creation_requires_a_valid_auth_token(): void
    {
        Http::fake([
            'http://auth.internal/api/v1/auth/me' => Http::response([
                'title' => 'Unauthenticated',
            ], 401),
        ]);

        $this->withToken('invalid-token')
            ->getJson('/api/v1/assets/references')
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/problem+json');
    }

    private function seedReferences(): void
    {
        $now = now();

        DB::table('categories')->insert([
            'id' => self::CATEGORY_ID,
            'libelle' => 'Informatique',
            'actif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('service_administratifs')->insert([
            'id' => self::SERVICE_ID,
            'code' => 'TEST-DIR',
            'libelle' => 'Direction de test',
            'actif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('sites')->insert([
            'id' => self::SITE_ID,
            'code' => 'TEST-SITE',
            'libelle' => 'Site de test',
            'actif' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
