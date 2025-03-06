<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntrepriseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Entreprise::create([
            'name'       => 'Elyos',
            'location'   => 'monastir',
            'email'      => 'elyos@gmail.com',
            'department' => 'IT',
            'phone'      => "73000000",
        ]);
    }
}
