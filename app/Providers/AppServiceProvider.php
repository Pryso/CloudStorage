<?php

namespace App\Providers;

use App\Models\File;
use App\Services\GetUserFilesSize;
use App\Services\ScanFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('max_uploaded_file_size', function ($attribute, $value, $parameters, $validator) {

            $total_size = array_reduce($value, function ( $sum, $item ) {
                // each item is UploadedFile Object
                $sum += filesize($item->path());
                return $sum;
            });

            // $parameters[0] in kilobytes
            return $total_size < $parameters[0] * 1024;

        });
        Validator::extend('file_exist', function ($attribute, $value, $parameters, $validator) {
            if(File::where('user_id',Auth::user()->id)->where('full_name', $value->getClientOriginalName())->whereNull('folder_id')->first()) {
                return false;
            }
            return true;
        });
        Validator::extend('antivirus', function ($attribute, $value, $parameters, $validator) {

            return ScanFile::scan($value);
        });
        Validator::extend('user_limit_size', function ($attribute, $value, $parameters, $validator) {

            $total_size = array_reduce($value, function ( $sum, $item ) {
                // each item is UploadedFile Object
                $sum += filesize($item->path());
                return $sum;
            });
            $max_size = Auth::user()->max_size;
            $user_size = GetUserFilesSize::getSizes(Storage::disk('public')->path('files/' . Auth::user()->id));
            $fullSize = $total_size + $user_size;
            // $parameters[0] in kilobytes

            return $fullSize < $max_size;

        });
    }
}
