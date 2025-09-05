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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            })
            ->whereNull('deleted_at')
            ->get()
            ->filter(fn($file) => $request->user()->can('view', $file)); // Filtro i file in base a se posso vederli o meno
            //-> filtro groups

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
        $users = UserResource::collection([...$orderedUsers]);
        $filters = [
            'search_name' => $nameFilter,
            'search_owner' => $ownerFilter,
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
    {   // Salva un singolo file nella cartella dell'utente
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

    public function download(Request $request)
    {
        if ($request->user()->cannot('files.download')) {
            abort(403, 'Unauthorized action.');
        }

        $fileIds = $request->input('file_ids', []);
        if (!blank(File::whereIn('id', $fileIds)->where('created_by', '!=', Auth::id())->first())) {
            abort(403, 'Unauthorized action.');
        }

        if (count($fileIds) === 1) {
            $file = File::findOrFail($fileIds[0]);

            if ($file->is_folder) {
                if ($file->children->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No files selected',
                    ]);
                }

                $zipPath = $this->createZip($file->children);
                return response()->download(Storage::disk('public')->path($zipPath));
            }

            return response()->download(Storage::disk('local')->path($file->storage_path), $file->name);
        }

        $files = File::whereIn('id', $fileIds)->get();
        $zipPath = $this->createZip($files);

        return response()->download(Storage::disk('public')->path($zipPath), 'files.zip');
    }

    public function createZip($files)
    {   // Crea file zip
        $zipPath = 'zip/' . Str::random() . '.zip';

        if (!Storage::disk('public')->exists('zip')) {
            Storage::disk('public')->makeDirectory('zip');
        }

        $zipFile = Storage::disk('public')->path($zipPath);

        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $this->addFilesToZip($zip, $files);
            $zip->close();
        }

        return $zipPath; // ritorno path relativo
    }

    private function addFilesToZip($zip, $files, $ancestors = '')
    { // Aggiunge file alla zip
        foreach ($files as $file) {
            if ($file->is_folder) {
                $this->addFilesToZip($zip, $file->children, $ancestors . $file->name . '/');
            } else {
                $zip->addFile(Storage::path($file->storage_path), $ancestors . $file->name);
            }
        }
    }

    public function delete(Request $request)
    {   // Soft delete dei file selezionati
        if ($request->user()->cannot('delete', File::class)) {
            abort(403, 'Unauthorized action.');
        }

        $fileIds = $request->input('file_ids', []);

        if (blank($fileIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No files selected',
            ]);
        }

        foreach ($fileIds as $id) {
            $file = File::find($id);
            if ($file) {
                $file->moveToTrash();
            }
        }

        return redirect()->back();
    }

    public function edit(Request $request)
    {
        if ($request->user()->cannot('edit', File::class)) {
            abort(403, 'Unauthorized action.');
        }
    }

}
