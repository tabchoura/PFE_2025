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
        // Utilisation de Carbon pour formater la date
        User::create([
            'nom' => 'candidat',
            'prenom' => 'candidat',
            'email' => 'candidat@example.com',
            // Utilisation correcte de Carbon pour formater la date
            'date_naissance' => Carbon::createFromFormat('d-m-Y', '24-09-2001')->format('Y-m-d'),
            'phone' => '54212712', // ✅ Entre guillemets
            // 'cv' => 'cv.pdf',
            'lieudenaissance' => "monastir",
            // 'cover_letter' => 'cl.pdf',
            // 'datedepot' => Carbon::create(2025, 3, 8)->format('Y-m-d'), // ✅ Format corrigé
            // 'datevalidation' => Carbon::create(2025, 3, 9)->format('Y-m-d'),
            // 'dateentretien' => Carbon::create(2025, 3, 10)->format('Y-m-d'),
            'cin' => '14028685',
            // 'poste' => 'accepted',
            // 'nb_offres' => 4,
            // 'nbvalidation' => 1,
            'password' => Hash::make('Candidat1234.'), // ✅ Mot de passe sécurisé
            'role' => "candidat",
        ]);

        User::create([
            'nom' => 'recruteur',
            'prenom' => 'recruteur',
            'email' => 'recruteur@example.com',
            // Utilisation correcte de Carbon pour formater la date
            'date_naissance' => Carbon::createFromFormat('d-m-Y', '24-09-2001')->format('Y-m-d'),
            'phone' => '54212712', // ✅ Entre guillemets
            // 'cv' => 'cv.pdf',
            'lieudenaissance' => "monastir",
            // 'cover_letter' => 'cl.pdf',
            // 'datedepot' => Carbon::create(2025, 3, 8)->format('Y-m-d'), // ✅ Format corrigé
            // 'datevalidation' => Carbon::create(2025, 3, 9)->format('Y-m-d'),
            // 'dateentretien' => Carbon::create(2025, 3, 10)->format('Y-m-d'),
            'cin' => '14028685',
            // 'poste' => 'accepted',
            // 'nb_offres' => 4,
            // 'nbvalidation' => 1,
            'localisation' => "monastir",
            'description' => 'aaa',
            'nomsociete' => "fsm",
            'siteweb' => "fsm.tn",
            'password' => Hash::make('Recruteur1234.'), // ✅ Mot de passe sécurisé
            'departement' => "info",
            'role' => "admin",
        ]);
    }
}
