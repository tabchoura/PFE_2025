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
            
            $table->date('date_naissance')->nullable(); // au lieu de 'age'
            $table->string('phone')->nullable();
            $table->string('cv')->nullable();
            $table->string('password');
            $table->string('lieudenaissance')->nullable(); // Correction ici, suppression de "column:"

            $table->string('cover_letter')->nullable();
            $table->date('datedepot')->nullable();
            $table->date('datevalidation')->nullable();
            $table->date('dateentretien')->nullable();
            $table->string('cin')->nullable();
            $table->string('poste')->nullable();
            $table->integer('nb_offres')->nullable();
            $table->integer('nbvalidation')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('siteweb')->nullable();
            $table->string('departement')->nullable();

            $table->string('description')->nullable();

            $table->string('localisation')->nullable();


            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->enum('role', ['candidat', 'recruteur', 'admin'])->default('candidat');
            $table->timestamps();
            $table->unsignedBigInteger('entreprise_id')->nullable();  // Ajout de la colonne entreprise_id
            $table->foreign('entreprise_id')->references('id')->on('entreprises')->onDelete('set null');  // Clé étrangère
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
