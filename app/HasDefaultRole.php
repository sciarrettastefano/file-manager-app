<?php

namespace App;
use Spatie\Permission\Models\Role;

trait HasDefaultRole
{
    protected static function bootHasDefaultRole()
    {
        // Assegno di base il ruolo di "user" a ogni nuovo utente creato
        static::created(function ($user) {
            $defaultRole = Role::firstOrCreate(['name' => 'user']);
            if ($defaultRole) {
                $user->assignRole($defaultRole);
            }
        });
    }
}
