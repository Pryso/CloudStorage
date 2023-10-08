<?php

namespace App\Services;

use App\Models\File;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadFile
{
    public static function upload($file) {
        $name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        if(Storage::disk('private')->has($name)) {
            $name = $name . \Illuminate\Support\Str::random(1);
        }
        if (exif_imagetype($file)) {
            $filePath = Storage::disk('public')->putFileAs('/files/' . Auth::user()->id,$file, $name);
        } else {
            $filePath = Storage::disk('private')->putFileAs('/files/' . Auth::user()->id,$file, $name);
        }
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if(!$extension) {
            $extension = $file->getClientOriginalExtension();
        }

        $size = $file->getSize();
        $user = User::where('id',Auth::user()->id)->first();
        $user->folder_used =+ $size;
        $user->save();
        $fileM = File::create([
            'id' => File::generateId(),
            'extension' => $extension,
            'path' => $filePath,
            'url' => url('/storage/' . $filePath),
            'user_id' => Auth::user()->id,
            'size' => $size,
            'full_name' => $file->getClientOriginalName(),
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
        ]);
    }
    public static function uploadInFolder($file,$folder) {
        $name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        if(Storage::disk('private')->has($name)) {
            $name = $name . \Illuminate\Support\Str::random(1);
        }
        $filePath = Storage::disk('private')->putFileAs('/files/' . Auth::user()->id . '/' . $folder->path,$file, $name);

        if (exif_imagetype($file)) {
            $filePath = Storage::disk('public')->putFileAs('/files/' . Auth::user()->id . '/' . $folder->path,$file, $name);
        }
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        if(!$extension) {
            $extension = $file->getClientOriginalExtension();
        }

        $size = $file->getSize();
        $user = User::where('id',Auth::user()->id)->first();
        $user->folder_used =+ $size;
        $user->save();
        $fileM = File::create([
            'id' => File::generateId(),
            'extension' => $extension,
            'path' => $filePath,
            'url' => url('/storage/' . $filePath),
            'user_id' => Auth::user()->id,
            'size' => $size,
            'full_name' => $file->getClientOriginalName(),
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'folder_id' => $folder->id,
        ]);
    }
}
