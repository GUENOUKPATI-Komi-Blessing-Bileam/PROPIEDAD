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
        Schema::create('ligne_inventaires', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('campagne_id');
            $table->uuid('bien_id');
            $table->string('resultat');              // enum
            $table->uuid('site_observe_id')->nullable();
            $table->string('etat_observe')->nullable(); // enum
            $table->text('commentaire')->nullable();
            $table->uuid('observe_par')->nullable(); // UUID Auth
            $table->dateTime('observe_le')->nullable();
            $table->timestamps();

            $table->foreign('campagne_id')
                  ->references('id')->on('campagne_inventaires')->cascadeOnDelete();

            $table->foreign('bien_id')
                  ->references('id')->on('biens')->restrictOnDelete();

            $table->foreign('site_observe_id')
                  ->references('id')->on('sites')->nullOnDelete();

            // Contrainte d’unicité demandée dans le MCD
            $table->unique(['campagne_id', 'bien_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_inventaires');
    }
};
