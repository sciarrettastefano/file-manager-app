<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\UserResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileController extends Controller
{
    public function index(Request $request, ?File $folder = null)
    {   // Visualizzazione file
        $nameFilter = $request->input('search_name', null);
        $ownerFilter = $request->input('search_owner', null);

        $folderFilter = !blank($folder) ? $folder : $this->getRoot($ownerFilter ?? null);

        $files = File::query()
            ->where('parent_id', $folderFilter?->id)
            ->when(!blank($nameFilter), function ($q) use ($nameFilter) {
                $q->where('name', 'like', "%$nameFilter%");
            })
            ->when(!blank($ownerFilter), function ($q) use ($ownerFilter) {
                $q->whereRelation('user', 'email', $ownerFilter);
            })->get()->filter(fn($file) => $request->user()->can('view', $file)); // Filtro i file in base a se posso vederli o meno
            // filtro groups

        /* ???????? Logica controllo autorizzazioni sufficiente ???????? */

        $files = FileResource::collection($files);
        $ancestors = FileResource::collection([...$folderFilter->ancestors, $folderFilter]);
        $folder = new FileResource($folderFilter);
        $orderedUsers = User::query()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'superadmin');
            })
            ->where('id', '!=', Auth::id())
            ->orderBy('email', 'asc')
            ->get();
        $users = UserResource::collection([Auth::user(), ...$orderedUsers]);
        $filters = [
            'search_name' => $nameFilter,
            'search_owner' => $ownerFilter ?? Auth::user()->email,
        ];

        return Inertia::render('Files', compact('files', 'folder', 'ancestors', 'filters', 'users'));
    }

    public function createFolder(StoreFolderRequest $request)
    {   // Chiunque ha il permesso di creare cartelle
        $data = $request->validated();
        $parentId = $data['parent_id'];
        $parent = File::query()->where('id', $parentId)->first();

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
        $parentId = $data['parent_id'];
        $parent = File::query()->where('id', $parentId)->first();
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

    private function getRoot(?String $userEmail = null)
    {   // Restituisce la root
        if (!blank($userEmail)) {
            $user = User::query()->where('email', $userEmail)->firstOrFail();
        } else {
            $user = null;
        }

        $root = File::query()
            ->whereIsRoot()
            ->when(!blank($user), function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->when(blank($user), function ($q) {
                $q->where('created_by', Auth::id());
            })
            ->firstOrFail();
        return $root;
    }

    public function edit(Request $request)
    {
        if ($request->user()->cannot('edit', File::class)) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function delete(Request $request)
    {
        if ($request->user()->cannot('delete', File::class)) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function download(Request $request)
    {
        if ($request->user()->cannot('download', File::class)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
