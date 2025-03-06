<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EntretienSeeder extends Seeder
{
    public function run()
    {
        DB::table('entretiens')->insert([
            [
                'name' => 'Entretien 1',
                'user_id' => 1, // Vérifie que cet utilisateur existe dans la table users
                'date_entretien' => '2025-03-10',
                'statut' => 'accepted',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'time' => '04:00:00'
            ]
        ]);
    }
}
