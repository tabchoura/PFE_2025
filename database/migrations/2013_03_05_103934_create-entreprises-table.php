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
        // Vérifie si la table 'entreprises' existe déjà
        if (!Schema::hasTable('entreprises')) {
            Schema::create('entreprises', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('location');
                $table->string('email')->nullable();
                $table->string('department');
                $table->string('phone');
                $table->string('description');  // Correction ici, suppression de "column:"
                $table->string('siteweb');  // Correction ici, suppression de "column:"
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprime la table si nécessaire
        Schema::dropIfExists('entreprises');
    }
};
