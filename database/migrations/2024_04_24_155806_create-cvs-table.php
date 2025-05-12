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
    {        Schema::create('cvs', function (Blueprint $table)
        {
        $table->id();
        $table->string('image')->nullable();
        $table->string('nom')->nullable();
        $table->string('prenom')->nullable();
        $table->date('date_naissance')->nullable()->nullable();
        $table->string('lieudenaissance')->nullable();
        $table->string('email')->nullable();
        $table->string('adresse')->nullable();
        $table->string('presentation')->nullable();
        $table->json('experiences')->nullable();
        $table->json(column: 'educations_formations')->nullable();
        $table->json('competences')->nullable();
        $table->json('langues')->nullable();
        $table->json('projets')->nullable();
         $table->string('cv_path')->nullable();
        $table->unsignedBigInteger('user_id')->nullable(); // Nullable si vous permettez des CVs sans utilisateur
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Lien avec la table users et suppression en cascade
        $table->timestamps();   // ⬅️ ajoute created_at & updated_at

    });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
