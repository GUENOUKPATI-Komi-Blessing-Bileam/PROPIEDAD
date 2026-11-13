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
        Schema::create('mouvements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');          // enum
            $table->string('statut');        // enum
            $table->dateTime('date_effet');
            $table->text('motif');
            $table->uuid('bien_id');
            $table->uuid('service_origine_id')->nullable();
            $table->uuid('site_origine_id')->nullable();
            $table->uuid('service_destination_id')->nullable();
            $table->uuid('site_destination_id')->nullable();
            $table->uuid('soumis_par');      // UUID Auth
            $table->uuid('valide_par')->nullable(); // UUID Auth
            $table->timestamps();

            $table->foreign('bien_id')
                  ->references('id')->on('biens')->restrictOnDelete();

            $table->foreign('service_origine_id')
                  ->references('id')->on('service_administratifs')->nullOnDelete();

            $table->foreign('site_origine_id')
                  ->references('id')->on('sites')->nullOnDelete();

            $table->foreign('service_destination_id')
                  ->references('id')->on('service_administratifs')->nullOnDelete();

            $table->foreign('site_destination_id')
                  ->references('id')->on('sites')->nullOnDelete();

            $table->index('bien_id');
            $table->index('statut');
            $table->index('date_effet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};
