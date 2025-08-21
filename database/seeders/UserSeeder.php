<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creo un utente superadmin se non esiste già
        User::firstOrCreate([
            'name' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('superadmin'),
            'email_verified_at' => now()
        ]);
    }
}
