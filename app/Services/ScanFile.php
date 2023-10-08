<?php

namespace App\Services;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Swagger\Client\Configuration;
use Swagger\Client\Api;

class ScanFile
{
    public static function scan($file) {
        if($file->getSize() > 3000000) {
            return true;
        }
        $name = md5(Carbon::now() . '_' . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        $filePath = Storage::disk('temp')->putFileAs('/',$file, $name);
        $config = Configuration::getDefaultConfiguration()->setApiKey('Apikey', '0f7f2a4c-2c61-4d51-b7be-488cf8a4427c');
        $path = Storage::disk('temp')->path($filePath);

        $apiInstance = new Api\ScanApi(
            new Client(),
            $config
        );
        $input_file = $path;
        try {
            $result = $apiInstance->scanFile($input_file);
            Storage::disk('temp')->delete($filePath);
            return $result['clean_result'];
        } catch (Exception $e) {
            echo 'Exception when calling ScanApi->scanFile: ', $e->getMessage(), PHP_EOL;
        }
    }
}
