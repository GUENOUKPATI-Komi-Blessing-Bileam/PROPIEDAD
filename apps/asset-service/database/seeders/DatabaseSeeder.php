<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! User::query()->where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $now = now();

        DB::table('categories')->updateOrInsert(
            ['id' => 'bdbfbeae-41b8-4f16-80cf-8e0de23d3111'],
            ['libelle' => 'Équipements informatiques', 'description' => 'Référentiel local de démonstration', 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
        );
        DB::table('service_administratifs')->updateOrInsert(
            ['id' => '3a6bb2a4-0988-47a8-88da-12e25e0ae81f'],
            ['code' => 'DEMO-DIR', 'libelle' => 'Direction de démonstration', 'actif' => true, 'parent_id' => null, 'created_at' => $now, 'updated_at' => $now],
        );
        DB::table('sites')->updateOrInsert(
            ['id' => 'c7d95f2d-3bed-433a-b595-74e050ce3fd3'],
            ['code' => 'DEMO-SITE', 'libelle' => 'Site principal de démonstration', 'adresse' => null, 'actif' => true, 'created_at' => $now, 'updated_at' => $now],
        );
    }
}
