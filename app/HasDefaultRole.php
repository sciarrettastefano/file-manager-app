<?php

namespace App;
use Spatie\Permission\Models\Role;

trait HasDefaultRole
{
    protected static function bootHasDefaultRole()
    {
        static::created(function ($user) {
            $defaultRole = Role::firstOrCreate(['name' => 'user']);
            if ($defaultRole) {
                $user->assignRole($defaultRole);
            }
        });
    }
}
