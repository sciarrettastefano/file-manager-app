<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FileController extends Controller
{
    public function index(Request $request) {
        if ($request->user()->cannot('view', File::class)) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Files');
    }

    public function create(Request $request) {
        /* Qualunque utente ha permesso di creare file */
    }

    public function edit(Request $request) {
        if ($request->user()->cannot('edit', File::class)) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function delete(Request $request) {
        if ($request->user()->cannot('delete', File::class)) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function download(Request $request) {
        if ($request->user()->cannot('download', File::class)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
