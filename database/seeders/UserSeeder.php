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
            'name' => 'mariem',
            'last_name' => 'bchir',
            'email' => 'mariiembchir@gmail.com',
            'age'=>23,
            'phone' => '54212712', // ✅ Entre guillemets
            'cv' => 'cv.pdf',
            'cover_letter' => 'cl.pdf',
            'datedepot' => Carbon::create(2025, 3, 8)->format('Y-m-d'), // ✅ Format corrigé
            'datevalidation' => Carbon::create(2025, 3, 9)->format('Y-m-d'),
            'dateentretien' => Carbon::create(2025, 3, 10)->format('Y-m-d'),
            'cin' => '14028685',
            'poste' => 'accepted',
            'nb_offres' => 4,
            'nbvalidation' => 1,
            'password' => Hash::make('1234'), // ✅ Mot de passe sécurisé
        ]);
    }
}
