<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\MassChangeStatusRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\AccountCreationMail;
use App\Mail\StatusChangeMail;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

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

        // Creo una root come prima macrocartella dell'utente appena creato
        $file = new File();
        $file->name = $user->email;
        $file->is_folder = 1;
        $file->makeRoot()->save();

        Mail::to($data['email'])->send(new AccountCreationMail($user));

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

        Mail::to($user->email)->send(new StatusChangeMail($user, $user->is_active));
    }

    // Gestione massiva status utenti (attivo/inattivo)
    public function massChangeStatus(MassChangeStatusRequest $request) {
        if ($request->user()->cannot('changeStatus', User::class)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();

        $users = User::whereIn('id', $data['ids'])->get();

        foreach($users as $user) {
            if ($user->is_active != $data['status']) {
                $user->is_active = $data['status'];
                $user->save();

                Mail::to($user->email)->send(new StatusChangeMail($user, $user->is_active));
            }
        }
    }

}
