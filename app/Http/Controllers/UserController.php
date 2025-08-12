<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Mostra i gli utenti
    public function index(Request $request) {
        if ($request->user()->cannot('view', User::class)) {
            abort(403, 'Unauthorized action.');
        }
    }

    // Creazione di un nuovo utente
    public function create(Request $request) {
        if ($request->user()->cannot('create', User::class)) {
            abort(403, 'Unauthorized action.');
        }

    }

    // Modifica dati di un utente
    public function edit(Request $request) {
        if ($request->user()->cannot('edit', User::class)) {
            abort(403, 'Unauthorized action.');
        }

    }

    // Gestione status di un utente (attivo/inattivo)
    public function changeStatus(Request $request) {
        if ($request->user()->cannot('changeStatus', User::class)) {
            abort(403, 'Unauthorized action.');
        }

        //modifica campo active
        /**
         * $user->is_active = false;
         * $user->save();
        */
    }

}
