<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creo i ruoli
        $roles = [
            'superadmin',
            'admin',
            'user',
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role],
                ['guard_name' => 'web']
            );
        }

        // Assegno il ruolo superadmin all'utente creato tramite UserSeeder
        $user = User::firstOrCreate([
            'email' => 'superadmin@example.com',
            'name' => 'superadmin',
            'password' => Hash::make('superadmin'),
            'email_verified_at' => now()
        ]);
        $user->syncRoles('superadmin');

        // Creo i permessi
        $permissions = [
            'files.view',
            'files.edit',
            'files.share',
            'files.delete',
            'files.download',
            'users.manage',
            'groups.manage',
            'groups.users.add',
            'groups.users.remove',
            'groups.files.add',
            'groups.files.edit',
            'groups.files.remove',
            'groups.files.share',
            'tag.manage',
            'versions.delete',
            'versions.download'
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Assegno tutti i permessi al ruolo superadmin
        foreach ($permissions as $permission) {
            $role = Role::findByName('superadmin');
            $role->givePermissionTo($permission);
        }

        // Assegno all'admin tutti i permessi tranne quelli sugli utenti
        foreach ($permissions as $permission) {
            if ($permission !== 'users.manage') {
                $role = Role::findByName('admin');
                $role->givePermissionTo($permission);
            }
        }

    }
}
