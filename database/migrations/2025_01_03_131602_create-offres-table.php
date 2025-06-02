<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     */
    public function up(): void
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id();
            // $table->string('type')->nullable();
            $table->string('salaire')->nullable();
            $table->string('titre')->nullable();
            $table->longText('description');

            $table->string('details')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

        
                  
            $table->timestamps();
        });
    }

    /**
     * Annule les migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offres'); 
    }
};
