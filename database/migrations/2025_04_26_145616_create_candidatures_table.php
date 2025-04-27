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
            $table->string('satut')->nullable(); // Facultatif


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
