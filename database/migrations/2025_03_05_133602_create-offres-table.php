<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('description');
            $table->float('salary')->nullable();
            $table->unsignedBigInteger('user_id'); // Définition de la colonne user_id
            
            $table->foreign('user_id')  // Définition de la clé étrangère
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offres'); // Supprime la table lors du rollback
    }
};
