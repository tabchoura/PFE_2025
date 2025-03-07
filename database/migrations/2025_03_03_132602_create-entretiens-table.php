<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entretiens', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('statut');
            $table->date('date_entretien')->nullable();
            $table->time('time')->nullable();
            $table->timestamps();
    
            // Crée la colonne offre_id et ajoute la clé étrangère en même temps
            $table->foreignId('offre_id')->constrained('offres')->onDelete('cascade');
            $table->unsignedBigInteger('user_id'); // Clé étrangère vers la table users

            // Lier la table entretiens à la table users
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('entretiens');
    }
    
};
