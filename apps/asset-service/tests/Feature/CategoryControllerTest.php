<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_created_categories_are_listed_and_available_to_asset_consumers(): void
    {
        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Aucune catégorie n’a encore été créée.')
            ->assertSee(route('categories.store'), false);

        $response = $this->post(route('categories.store'), [
            'libelle' => 'Matériel médical',
            'description' => 'Équipements de santé',
        ]);

        $response->assertRedirect(route('categories.index'))
            ->assertSessionHas('status', 'La catégorie « Matériel médical » a été créée.');
        $category = DB::table('categories')->where('libelle', 'Matériel médical')->first();
        $this->assertNotNull($category);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'libelle' => 'Matériel médical',
            'description' => 'Équipements de santé',
            'actif' => true,
        ]);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Matériel médical')
            ->assertSee('Équipements de santé');
        $this->get(route('assets.create'))
            ->assertOk()
            ->assertSee('Matériel médical');

        Http::preventStrayRequests();
        config(['asset.auth_url' => 'http://auth.internal']);
        Http::fake([
            'http://auth.internal/api/v1/auth/me' => Http::response([
                'id' => '8d39fb30-45c5-41b8-8ee6-221e9dcb6afd',
                'email' => 'manager@example.gov',
                'roles' => ['gestionnaire'],
            ]),
        ]);

        $this->withToken('valid-token')
            ->getJson('/api/v1/assets/references')
            ->assertOk()
            ->assertJsonPath('categories.0.id', $category->id)
            ->assertJsonPath('categories.0.label', 'Matériel médical');
    }

    public function test_category_name_is_required(): void
    {
        $response = $this->post(route('categories.store'), []);

        $response->assertInvalid([
            'libelle' => 'Le nom de la catégorie est obligatoire.',
        ]);
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_duplicate_category_name_is_rejected(): void
    {
        DB::table('categories')->insert([
            'id' => (string) Str::uuid(),
            'libelle' => 'Mobilier',
            'actif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post(route('categories.store'), ['libelle' => 'Mobilier']);

        $response->assertInvalid([
            'libelle' => 'Une catégorie portant ce nom existe déjà.',
        ]);
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_category_text_is_escaped_in_the_list_and_asset_form(): void
    {
        $this->post(route('categories.store'), [
            'libelle' => '<script>alert(1)</script>',
            'description' => '<img src=x onerror=alert(1)>',
        ])->assertRedirect(route('categories.index'));

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
        $this->get(route('assets.create'))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
