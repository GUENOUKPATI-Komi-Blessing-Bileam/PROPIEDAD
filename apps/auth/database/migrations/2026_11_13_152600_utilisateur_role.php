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
        Schema::create('utilisateur_role', function (Blueprint $table) {
            $table->uuid('utilisateur_id');
            $table->uuid('role_id');
            $table->timestamp('attribue_le')->useCurrent();
            $table->uuid('attribue_par')->nullable(); // UUID de l'utilisateur qui a attribué le rôle

            // Clé primaire composite
            $table->primary(['utilisateur_id', 'role_id']);

            $table->foreign('utilisateur_id')
                  ->references('id')
                  ->on('utilisateurs')
                  ->cascadeOnDelete();

            $table->foreign('role_id')
                  ->references('id')
                  ->on('roles')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::DropIfExists('utilisateur_role');
    }
};
