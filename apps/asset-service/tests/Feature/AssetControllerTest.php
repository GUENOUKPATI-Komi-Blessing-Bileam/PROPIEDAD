<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AssetControllerTest extends TestCase
{
    use RefreshDatabase;

    private const CATEGORY_ID = 'bdbfbeae-41b8-4f16-80cf-8e0de23d3111';

    private const SERVICE_ID = '3a6bb2a4-0988-47a8-88da-12e25e0ae81f';

    private const SITE_ID = 'c7d95f2d-3bed-433a-b595-74e050ce3fd3';

    public function test_valid_submission_is_saved_and_displayed_in_the_asset_list(): void
    {
        $this->seedReferences();

        $response = $this->post(route('assets.store'), [
            'label' => 'Ordinateur de démonstration',
            'description' => 'Enregistré depuis le formulaire',
            'category_id' => self::CATEGORY_ID,
            'service' => 'Direction de test',
            'site' => 'Site de test',
            'condition' => 'good',
            'acquisition_date' => '2026-11-15',
            'acquisition_value' => '250000',
            'currency' => 'XOF',
        ]);

        $response->assertRedirect(route('assets.index'));
        $this->assertDatabaseHas('biens', [
            'libelle' => 'Ordinateur de démonstration',
            'description' => 'Enregistré depuis le formulaire',
            'categorie_id' => self::CATEGORY_ID,
            'service_responsable_id' => self::SERVICE_ID,
            'site_id' => self::SITE_ID,
            'etat' => 'good',
        ]);

        $asset = DB::table('biens')->where('libelle', 'Ordinateur de démonstration')->first();
        $this->assertDatabaseHas('outbox_events', [
            'aggregate_type' => 'asset',
            'aggregate_id' => $asset->id,
            'type_evenement' => 'patrimoine.bien.cree',
        ]);

        $this->get(route('assets.index'))
            ->assertOk()
            ->assertSee('Ordinateur de démonstration')
            ->assertSee($asset->code_inventaire);
    }

    public function test_invalid_submission_does_not_create_an_asset(): void
    {
        $response = $this->from(route('assets.create'))
            ->post(route('assets.store'), []);

        $response->assertRedirect(route('assets.create'))
            ->assertSessionHasErrors(['label', 'category_id', 'service', 'site', 'condition']);
        $this->assertDatabaseCount('biens', 0);
        $this->assertDatabaseCount('outbox_events', 0);
    }

    public function test_asset_form_uses_text_inputs_for_service_and_site(): void
    {
        $this->seedReferences();

        $this->get(route('assets.create'))
            ->assertOk()
            ->assertSee('type="text" name="service" list="service-options"', false)
            ->assertSee('type="text" name="site" list="site-options"', false)
            ->assertSee('<option value="Direction de test">', false)
            ->assertSee('<option value="Site de test">', false);
    }

    public function test_asset_labels_are_escaped_in_the_registry(): void
    {
        $this->seedReferences();

        $this->post(route('assets.store'), [
            'label' => '<script>alert(1)</script>',
            'category_id' => self::CATEGORY_ID,
            'service' => 'Direction de test',
            'site' => 'Site de test',
            'condition' => 'good',
        ])->assertRedirect(route('assets.index'));

        $this->get(route('assets.index'))
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>', false);
    }

    public function test_unknown_service_or_site_names_are_rejected(): void
    {
        $this->seedReferences();

        $this->from(route('assets.create'))
            ->post(route('assets.store'), [
                'label' => 'Ordinateur de démonstration',
                'category_id' => self::CATEGORY_ID,
                'service' => 'Service inconnu',
                'site' => 'Site inconnu',
                'condition' => 'good',
            ])
            ->assertRedirect(route('assets.create'))
            ->assertSessionHasErrors(['service', 'site']);

        $this->assertDatabaseCount('biens', 0);
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
