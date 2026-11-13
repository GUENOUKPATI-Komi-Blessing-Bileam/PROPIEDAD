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
        Schema::create('campagne_inventaires', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('libelle');
            $table->string('statut');        // enum
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->uuid('service_id')->nullable();
            $table->uuid('site_id')->nullable();
            $table->uuid('cree_par');        // UUID Auth
            $table->timestamps();

            $table->foreign('service_id')
                  ->references('id')->on('service_administratifs')->nullOnDelete();

            $table->foreign('site_id')
                  ->references('id')->on('sites')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campagne_inventaires');
    }
};
