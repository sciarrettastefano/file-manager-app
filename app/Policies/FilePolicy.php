<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy
{
    /**
     * Creo la policy per autorizzazioni per azioni su file compiute dagli utenti.
     * Ogni utente ha ogni diritto su tutti i file di cui è proprietario (e creatore).
     * In generale un utente può agire sui file nei modi di cui gli sono stati dati i permessi.
     */

    public function view(User $user, File $file) {
        return $user->can('files.view') || $user->id == $file->created_by;
    }

    public function edit(User $user, File $file) {
        return $user->can('files.edit') || $user->id == $file->created_by;
    }

    public function share(User $user, File $file) {
        return $user->can('files.share') || $user->id == $file->created_by;
    }

    public function delete(User $user, File $file) {
        return $user->can('files.delete') || $user->id == $file->created_by;
    }

    public function download(User $user, File $file) {
        return $user->can('files.download') || $user->id == $file->created_by;
    }
}
