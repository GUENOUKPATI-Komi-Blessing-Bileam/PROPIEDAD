<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MovementApiTest extends TestCase
{
    use RefreshDatabase;

    private const USER_ID = '8d39fb30-45c5-41b8-8ee6-221e9dcb6afd';

    private const ASSET_ID = '3d39fb30-45c5-41b8-8ee6-221e9dcb6afd';

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

    public function test_authenticated_submission_is_saved_and_returned_in_the_movement_list(): void
    {
        $this->seedAsset();

        $response = $this->withToken('valid-token')->postJson('/api/v1/movements', [
            'asset_id' => self::ASSET_ID,
            'type' => 'transfer',
            'destination_service_id' => self::SERVICE_ID,
            'destination_site_id' => self::SITE_ID,
            'effective_at' => '2026-11-20T14:30:00.000Z',
            'reason' => 'Réorganisation du service',
        ]);

        $response->assertCreated()
            ->assertJsonPath('asset_id', self::ASSET_ID)
            ->assertJsonPath('asset_code', 'INV-2026-TEST0001')
            ->assertJsonPath('asset_label', 'Ordinateur de test')
            ->assertJsonPath('type', 'transfer')
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('requested_by', self::USER_ID);
        $this->assertDatabaseHas('mouvements', [
            'bien_id' => self::ASSET_ID,
            'type' => 'transfer',
            'statut' => 'pending',
            'date_effet' => '2026-11-20 14:30:00',
            'motif' => 'Réorganisation du service',
            'soumis_par' => self::USER_ID,
        ]);

        $movementId = $response->json('id');
        $this->assertDatabaseHas('outbox_events', [
            'aggregate_type' => 'movement',
            'aggregate_id' => $movementId,
            'type_evenement' => 'patrimoine.mouvement.soumis',
        ]);

        $this->withToken('valid-token')
            ->getJson('/api/v1/movements?status=pending')
            ->assertOk()
            ->assertJsonPath('data.0.id', $movementId)
            ->assertJsonPath('data.0.asset_id', self::ASSET_ID)
            ->assertJsonPath('data.0.asset_code', 'INV-2026-TEST0001')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_invalid_submission_does_not_create_a_movement(): void
    {
        $this->seedAsset();

        $this->withToken('valid-token')
            ->postJson('/api/v1/movements', [
                'asset_id' => self::ASSET_ID,
                'type' => 'unknown',
                'effective_at' => 'not-a-date',
                'reason' => '',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'type',
                'effective_at',
                'reason',
            ]);

        $this->assertDatabaseCount('mouvements', 0);
        $this->assertDatabaseCount('outbox_events', 0);
    }

    public function test_movement_submission_requires_authentication(): void
    {
        $this->postJson('/api/v1/movements', [])
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/problem+json');
    }

    private function seedAsset(): void
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
        DB::table('biens')->insert([
            'id' => self::ASSET_ID,
            'code_inventaire' => 'INV-2026-TEST0001',
            'libelle' => 'Ordinateur de test',
            'description' => null,
            'date_acquisition' => null,
            'valeur_acquisition' => null,
            'devise' => null,
            'etat' => 'good',
            'statut' => 'active',
            'categorie_id' => self::CATEGORY_ID,
            'service_responsable_id' => self::SERVICE_ID,
            'site_id' => self::SITE_ID,
            'cree_par' => self::USER_ID,
            'version' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
