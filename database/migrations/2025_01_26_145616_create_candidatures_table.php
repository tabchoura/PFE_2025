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
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offre_id')->constrained('offres')->onDelete('cascade'); // Clé étrangère vers offres
            $table->foreignId('cv_id')->constrained('cvs')->onDelete('cascade');       // Clé étrangère vers cvs
            $table->text('message')->nullable(); // Facultatif
            $table->enum('statut', ['enattente','accepter','entretien','embauche','refuser'])
            ->default('enattente');
            $table->unsignedBigInteger('user_id'); // Assurez-vous que user_id est non nullable
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Clé étrangère vers users
            $table->timestamp('date_entretien')->nullable();

            $table->timestamps(); // created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
