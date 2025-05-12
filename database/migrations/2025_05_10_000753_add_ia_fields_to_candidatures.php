<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            // Embedding du texte du CV
            $table->json('cv_embedding')->nullable()->after('cv_id');
            // Score de similarité cosinus (0.0 à 1.0)
            $table->float('match_score')->nullable()->after('cv_embedding');
            $table->float('similarity_score')->nullable()->after('cv_embedding');

            // Décision automatique IA : accepted ou rejected
            $table->enum('status_ia', ['accepted','rejected'])
                  ->nullable()
                  ->after('match_score');
                  
        });
    }

    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropColumn(['cv_embedding','match_score','status_ia']);
        });
    }
};
