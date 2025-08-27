<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\User;
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

        $user = User::query()->where('email', 'superadmin@example.com')->first();

        $file = new File();
        $file->name = $user->email;
        $file->is_folder = 1;
        $file->created_by = $user->id;
        $file->updated_by = $user->id;
        $file->makeRoot()->save();
    }
}
