<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('biens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_inventaire')->unique();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->date('date_acquisition')->nullable();
            $table->decimal('valeur_acquisition', 15, 2)->nullable();
            $table->char('devise', 3)->nullable();
            $table->string('etat');          // enum
            $table->string('statut');        // enum
            $table->uuid('categorie_id');
            $table->uuid('service_responsable_id');
            $table->uuid('site_id');
            $table->uuid('cree_par');        // UUID Auth (pas de FK)
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();

            $table->foreign('categorie_id')
                  ->references('id')->on('categories')->restrictOnDelete();

            $table->foreign('service_responsable_id')
                  ->references('id')->on('service_administratifs')->restrictOnDelete();

            $table->foreign('site_id')
                  ->references('id')->on('sites')->restrictOnDelete();

            $table->index('categorie_id');
            $table->index('service_responsable_id');
            $table->index('site_id');
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
