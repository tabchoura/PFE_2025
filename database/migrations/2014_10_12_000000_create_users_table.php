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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            
            $table->date('date_naissance')->nullable(); 
            $table->string('phone')->nullable();
            $table->string('cv')->nullable();
            $table->string('password');
            $table->string('lieudenaissance')->nullable(); 

            $table->string('cover_letter')->nullable();
            $table->date('datedepot')->nullable();
            $table->date('datevalidation')->nullable();
            $table->date('dateentretien')->nullable();
            $table->string('cin')->nullable();
            $table->string('poste')->nullable();
            $table->integer('nb_offres')->nullable();
            // $table->integer('nbvalidation')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('siteweb')->nullable();
            $table->string('nomsociete')->nullable(); 


            $table->string('localisation')->nullable();


            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->enum('role', ['candidat', 'recruteur', 'admin'])->default('candidat');
            $table->timestamps();
        // ajouter automatiquement deux colonnes à ta table :created_at ,updated_at 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
