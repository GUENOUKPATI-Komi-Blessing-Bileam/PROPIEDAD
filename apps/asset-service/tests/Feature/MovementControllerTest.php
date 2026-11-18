<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MovementControllerTest extends TestCase
{
    use RefreshDatabase;

    private const ASSET_ID = '3d39fb30-45c5-41b8-8ee6-221e9dcb6afd';

    private const CATEGORY_ID = 'bdbfbeae-41b8-4f16-80cf-8e0de23d3111';

    private const SOURCE_SERVICE_ID = '3a6bb2a4-0988-47a8-88da-12e25e0ae81f';

    private const DESTINATION_SERVICE_ID = '54834a94-e19c-4c50-96c8-282404697933';

    private const SOURCE_SITE_ID = 'c7d95f2d-3bed-433a-b595-74e050ce3fd3';

    private const DESTINATION_SITE_ID = '7c52a8b4-9ed1-4f3a-8d15-b466a71295aa';

    public function test_valid_submission_is_saved_and_displayed_in_the_movement_list(): void
    {
        $this->seedReferences();
        $this->seedAsset();

        $response = $this->post(route('movements.store'), [
            'bien_id' => self::ASSET_ID,
            'type' => 'Transfert',
            'date_effet' => '2026-11-20',
            'service_destination_id' => self::DESTINATION_SERVICE_ID,
            'site_destination_id' => self::DESTINATION_SITE_ID,
            'destinataire' => 'Équipe de destination',
            'motif' => 'Réorganisation du service',
        ]);

        $response->assertRedirect(route('movements.index'));
        $this->assertDatabaseHas('mouvements', [
            'bien_id' => self::ASSET_ID,
            'type' => 'Transfert',
            'statut' => 'pending',
            'date_effet' => '2026-11-20 00:00:00',
            'service_origine_id' => self::SOURCE_SERVICE_ID,
            'site_origine_id' => self::SOURCE_SITE_ID,
            'service_destination_id' => self::DESTINATION_SERVICE_ID,
            'site_destination_id' => self::DESTINATION_SITE_ID,
            'destinataire' => 'Équipe de destination',
            'motif' => 'Réorganisation du service',
        ]);

        $this->get(route('movements.index'))
            ->assertOk()
            ->assertSee('INV-2026-TEST0001')
            ->assertSee('Ordinateur de test')
            ->assertSee('Direction de destination')
            ->assertSee('Site de destination')
            ->assertSee('En attente de validation');
    }

    public function test_invalid_submission_does_not_create_a_movement(): void
    {
        $response = $this->from(route('movements.create'))
            ->post(route('movements.store'), []);

        $response->assertRedirect(route('movements.create'))
            ->assertSessionHasErrors([
                'bien_id',
                'type',
                'date_effet',
                'service_destination_id',
                'site_destination_id',
                'motif',
            ]);
        $this->assertDatabaseCount('mouvements', 0);
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
            ['id' => self::SOURCE_SERVICE_ID, 'code' => 'SOURCE', 'libelle' => 'Direction source', 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => self::DESTINATION_SERVICE_ID, 'code' => 'DEST', 'libelle' => 'Direction de destination', 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('sites')->insert([
            ['id' => self::SOURCE_SITE_ID, 'code' => 'SITE-SOURCE', 'libelle' => 'Site source', 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
            ['id' => self::DESTINATION_SITE_ID, 'code' => 'SITE-DEST', 'libelle' => 'Site de destination', 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedAsset(): void
    {
        $now = now();

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
            'service_responsable_id' => self::SOURCE_SERVICE_ID,
            'site_id' => self::SOURCE_SITE_ID,
            'cree_par' => '00000000-0000-4000-8000-000000000001',
            'version' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
