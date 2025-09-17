<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Import Hash facade

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'organizador', // Explicitly set role for clarity
        ]);

        // Create Super Admin User
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@copagol.com.pe',
            'password' => Hash::make('Quicano1992$$$'), // Hash the password
            'role' => 'admin',
        ]);

        $this->call([
            CampeonatoSeeder::class,
            EquipoSeeder::class,
        ]);
    }
}