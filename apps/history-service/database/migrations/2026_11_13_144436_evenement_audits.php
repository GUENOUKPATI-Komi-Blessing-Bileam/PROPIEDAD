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
        Schema::create('evenement_audits', function (Blueprint $table) {
            $table->uuid('event_id')->primary();
            $table->string('service_source');
            $table->string('aggregate_type');
            $table->uuid('aggregate_id');
            $table->string('type_evenement');
            $table->uuid('acteur_id')->nullable();          // UUID Auth (pas de FK)
            $table->dateTime('date_evenement');
            $table->uuid('correlation_id')->nullable();
            $table->json('payload');
            $table->timestamp('recu_le')->useCurrent();

            // Index utiles pour les recherches d'audit
            $table->index(['service_source', 'aggregate_type', 'aggregate_id']);
            $table->index('type_evenement');
            $table->index('date_evenement');
            $table->index('acteur_id');
            $table->index('correlation_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::DropIfExists('evenement_audits');
    }
};
