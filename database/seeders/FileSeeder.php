<?php

namespace Database\Seeders;

use App\Models\File;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creo una root come prima macrocartella dell'utente appena creato
        File::create([
            'name' => 'superadmin@example.com',
            'is_folder' => 1
        ])->makeRoot()
        ->save();
    }
}
