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
        $table->unsignedBigInteger('user_id')->nullable(); 
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
        $table->timestamps();   

    });

        
    }
public function down()
{
    Schema::table('cvs', function (Blueprint $table) {
        $table->dropForeign(['user_id']); // supprime la contrainte FK
    });
    Schema::dropIfExists('cvs');
}

    /**
     * Reverse the migrations.
     */
  
};
