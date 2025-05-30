<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCandidaturesTable extends Migration
{
    public function up()
    {
        Schema::create('candidatures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('offre_id');
            $table->unsignedBigInteger('cv_id');
            
            // Nouvelles colonnes sans ->after()
            $table->json('cv_embedding')->nullable();
            $table->double('match_score', 8, 2)->nullable();
            $table->double('similarity_score', 8, 2)->nullable();
            $table->enum('status_ia', ['accepted', 'rejected'])->nullable();
            
            $table->text('message')->nullable();
            $table->enum('statut', ['enattente', 'accepter', 'entretien', 'embauche', 'refuser'])
                  ->default('enattente');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('date_entretien')->nullable();
            $table->string('lien_visio')->nullable();
            
            $table->timestamps(); // created_at et updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('candidatures');
    }
}
