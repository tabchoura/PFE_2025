<?php

namespace Database\Seeders;

use App\Models\Offre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OffreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Offre::create([
            'type'        => 'tt',
            'user_id'     => 1, // Assurez-vous que cet utilisateur existe dans la table users
            'description' => 'dev web',
            'salary'      => 1000.1,
        ]);
    }
}
