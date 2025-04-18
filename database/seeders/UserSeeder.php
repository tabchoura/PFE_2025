<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nom' => 'mariem',
            'prenom' => 'bchir',
            'email' => 'admin@example.com',
            // 'date_naissance'=>23,
            // 'phone' => '54212712', // ✅ Entre guillemets
            // 'cv' => 'cv.pdf',
            // 'lieudenaissance'=>"monastir",
            // 'cover_letter' => 'cl.pdf',
            // 'datedepot' => Carbon::create(2025, 3, 8)->format('Y-m-d'), // ✅ Format corrigé
            // 'datevalidation' => Carbon::create(2025, 3, 9)->format('Y-m-d'),
            // 'dateentretien' => Carbon::create(2025, 3, 10)->format('Y-m-d'),
            // 'cin' => '14028685',
            // 'poste' => 'accepted',
            // 'nb_offres' => 4,
            // 'nbvalidation' => 1,
            'password' => Hash::make('1234'), // ✅ Mot de passe sécurisé,
            'role'=>"admin",
        ]);
    }
}
