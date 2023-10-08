<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFolderInFolderRequest;
use App\Http\Requests\CreateFolderRequest;
use App\Http\Requests\FileRequest;
use App\Http\Requests\FolderRequest;
use App\Http\Requests\RenameFileRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\FolderResource;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Services\GetUserFilesSize;
use App\Services\UploadFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\Input;


class FileController extends Controller
{
    public function create(FileRequest $request) {
        $data = $request->validated();
        foreach ($data['file'] as $file) {
            UploadFile::upload($file);
        }
        return response()->json(['success' => 'Файлы загружены!']);
    }
    public function recent(Request $request) {
        $files = File::userFiles()->whereNull('folder_id')->orderBy('created_at','desc')->get();


        $folder = Folder::where('user_id',Auth::user()->id)->whereNull('folder_id')->orderBy('created_at','desc')->get();

        return response()->json(['files' => $files, 'folder' => $folder]);
    }
    public function get(Request $request) {

        $files = File::userFiles()->whereNull('folder_id')->get();
        $folder = Folder::where('user_id',Auth::user()->id)->whereNull('folder_id')->with('files')->get();
        return response()->json(['files' => FileResource::collection($files), 'folder'=>FolderResource::collection($folder)]);

    }
    public function createFileInFolder(FolderRequest $request) {
        $data = $request->validated();
        $folder = Folder::where('id',$data['id'])->first();
        foreach ($data['file'] as $file) {
            UploadFile::uploadInFolder($file,$folder);
        }
        return response()->json(['success' => 'Файлы загружены!']);
    }
    public function folder($path) {
        $folder = Folder::where('user_id',Auth::user()->id)->where('path',$path)->with('files')->with('folders')->get();
        return FolderResource::collection($folder);
    }
    public function createFolder(CreateFolderRequest $request) {
        $data = $request->validated();
        $folder = Folder::create([
            'name' => $data['name'],
            'path' => $data['name'],
            'user_id' => Auth::user()->id,
        ]);
        return response()->json(['success' => 'Папка создана','folder' => $folder]);
    }
    public function createFolderInFolder(CreateFolderInFolderRequest $request) {
        $data = $request->validated();
        $folder = Folder::where('user_id',Auth::user()->id)->where('id',$data['folder_id'])->first();
        $new_folder = Folder::create([
            'path' => $folder->path . '/' . $data['name'],
            'name' => $data['name'],
            'user_id' => Auth::user()->id,
            'folder_id' => $folder->id,
        ]);
        return response()->json(['succes' => 'Папка создана', 'folder' => $new_folder]);
    }
    public function openFile(Request $request, $id) {
        $file = File::where('id', $id)->first();

        return FileResource::make($file);
    }
    public function openAccess(Request $request) {
        $file = File::where('id',$request->id)->first();
        $file->access = 'PUBLIC';
        $file->save();
        return response()->json(['success' => 'Файл открыт','url' => 'http://127.0.0.1:8000/f/' . $file->id]);
    }
    public function closeAccess(Request $request) {
        $file = File::where('id',$request->id)->first();
        $file->access = 'PRIVATE';
        $file->save();
        return response()->json(['success' => 'Файл закрыт']);
    }
    public function renameFile(RenameFileRequest $request) {
        $data = $request->validated();
        if(File::where('name',$data['name'])->first()) {
            return response()->json(['Файл с таким именем уже есть']);
        }
        $file = File::where('id',$data['id'])->first();
        $file->name = $data['name'];
        $file->save();
        return response()->json(['success' => 'Файл переименован']);
    }


}
