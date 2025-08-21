<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Creo le policy che regolano le autorizzazioni per le azioni sugli utenti (esclusivamente da parte di altri utenti)
     * Solo il superadmin può visualizzare, creare, modificare e cambiare lo stato degli altri utenti.
    */

    public function view(User $user) {
        return $user->can('users.manage');
    }

    public function create(User $user) {
        return $user->can('users.manage');
    }

    public function edit(User $user) {
        return $user->can('users.manage');
    }

    public function changeStatus(User $user) {
        return $user->can('users.manage');
    }

}
