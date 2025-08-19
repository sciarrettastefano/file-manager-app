<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Mostra gli utenti
    public function index(Request $request) {
        // Controllo dei permessi
        if ($request->user()->cannot('view', User::class)) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::query()
            ->with('roles')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'superadmin');
            })->get();

        return Inertia::render('Users', [
            'users' => UserResource::collection($users)
        ]);
    }

    // Creazione di un nuovo utente
    public function create(StoreUserRequest $request) {
        if ($request->user()->cannot('create', User::class)) {
            abort(403, 'Unauthorized action.');
        }
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

    }

    // Modifica dati di un utente (nome, email e ruolo)
    public function edit(EditUserRequest $request, User $user) {
        if ($request->user()->cannot('edit', User::class)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->syncRoles($data['role']);

        $user->save();
    }

    // Gestione status di un utente (attivo/inattivo)
    public function changeStatus(ChangeStatusRequest $request, User $user) {
        if ($request->user()->cannot('changeStatus', User::class)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        $user->is_active = $data['active'];
        $user->save();
    }

}
