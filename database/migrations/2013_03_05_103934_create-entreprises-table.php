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
                $table->string('nomentreprise')->nullable();
                $table->string('lieu');
                $table->string('email')->nullable();
                $table->string('departement');
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
