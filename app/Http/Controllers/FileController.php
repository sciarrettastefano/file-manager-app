<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileController extends Controller
{
    public function index(Request $request, string $folder = null)
    {   // Visualizzazione file
        // Logica da implementare correttamente
        /*if ($request->user()->cannot('view', File::class)) {
            abort(403, 'Unauthorized action.');
        }*/

        // Quando definisco folder o getRoot con la query, devo cambiare a firstOrFail

        // Stabilisco folder
        if ($folder) {
            $folder = File::query()
                ->where('path', $folder)
                ->first();
        }
        if (!$folder) {
            $folder = $this->getRoot();
        }

        $files = File::query()->get();

        // Filtro i file in base a se posso vederli o meno
        $files = $files->filter(fn ($file) => $request->user()->can('view', $file));
        $files = FileResource::collection($files);
        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);
        $folder = new FileResource($folder);

        return Inertia::render('Files', compact('files', 'folder', 'ancestors'));
    }

    public function createFolder(StoreFolderRequest $request)
    {   // Chiunque ha il permesso di creare cartelle
        $data = $request->validated();
        $parent = $request->parent;

        if (!$parent) {
            $parent = $this->getRoot(); //se il parent non è dato, allora prendiamo la root
        }

        $file = new File();
        $file->is_folder = 1;
        $file->name = $data['name'];

        $parent->appendNode($file); //metodo di nestedSet che annida $file al suo $parent
    }

    public function store(StoreFileRequest $request)
    {   // Chiunque ha il permesso di caricare file
        $data = $request->validated();
        $parent = $request->parent_id;
        $user = $request->user();
        $fileTree = $request->file_tree;

        if (!$parent) {
            $parent = $this->getRoot();
        }

        if (!empty($fileTree)) {
            $this->saveFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {
                $this->saveFile($file, $user, $parent);
            }
        }
    }

    public function saveFileTree($fileTree, $parent, $user)
    {   // Salva direttamente l'intera struttura ad albero di una cartella
        foreach ($fileTree as $name => $file) {
            if (is_array($file)) {
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user);
            } else {
                $this->saveFile($file, $user, $parent);
            }
        }
    }

    private function saveFile($file, $user, $parent): void
    {   // Salva un sngolo file nella cartella dell'utente
        $path = $file->store('/files' . $user->id);

        $model = new File();
        $model->storage_path = $path;
        $model->is_folder = false;
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();

        $parent->appendNode($model);
    }

    private function getRoot()
    {   // Restituisce la root
        return File::query()->whereIsRoot()->where('created_by', Auth::id())->first();
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
