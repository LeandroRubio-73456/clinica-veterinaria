<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario administrativo inicial para acceder al sistema local.
        User::query()->updateOrCreate(['email' => 'admin@clinica.gob.ec'], [
            'name' => 'Rubio Leandro Admin',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // Cargar un escenario coherente para revisar la agenda y los reportes.
        $this->call(OperationalScenarioSeeder::class);
    }
}
