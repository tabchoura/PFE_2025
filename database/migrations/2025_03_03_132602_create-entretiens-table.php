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

            /* --- lien vers la candidature concernée --- */
            $table->foreignId('candidature_id')
                  ->constrained('candidatures')
                  ->onDelete('cascade');

            /* --- date + heure au même champ (timestamp) --- */
            $table->timestamp('date_entretien');

            /* --- statut de l’entretien (planifié, réalisé…) --- */
            $table->string('statut')->default('planifié');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entretiens');
    }
};
