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
        Schema::create('outbox_events', function (Blueprint $table) {
            $table->uuid('event_id')->primary();
            $table->string('type_evenement');
            $table->string('aggregate_type');
            $table->uuid('aggregate_id');
            $table->json('payload');
            $table->timestamp('cree_le')->useCurrent();
            $table->timestamp('publie_le')->nullable();
            $table->unsignedInteger('tentatives')->default(0);

            $table->index(['publie_le', 'tentatives']);
            $table->index(['aggregate_type', 'aggregate_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbox_events');
    }
};
