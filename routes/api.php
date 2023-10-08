<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\LoginController::class, 'logout']);
Route::post('/register', [App\Http\Controllers\LoginController::class, 'register']);
Route::group(['prefix' => 'file','middleware'=>['auth:sanctum']], function() {
   Route::post('/',[App\Http\Controllers\FileController::class, 'create']);
   Route::get('/', [\App\Http\Controllers\FileController::class, 'get']);
   Route::get('/recent', [\App\Http\Controllers\FileController::class, 'recent']);
   Route::get('/folder/{path}',[\App\Http\Controllers\FileController::class, 'folder'])->where('path', '.*');
   Route::post('/folder',[\App\Http\Controllers\FileController::class, 'createFileInFolder']);
   Route::post('/createFolder', [\App\Http\Controllers\FileController::class, 'createFolder']);
   Route::post('/createFolderInFolder', [\App\Http\Controllers\FileController::class, 'createFolderInFolder']);
   Route::post('/openAccess', [\App\Http\Controllers\FileController::class, 'openAccess']);
   Route::post('/closeAccess', [\App\Http\Controllers\FileController::class, 'closeAccess']);
   Route::post('/rename',[\App\Http\Controllers\FileController::class, 'renameFile']);
});
Route::get('file/{id}', [\App\Http\Controllers\FileController::class, 'openFile'])->name('fileAccess')->middleware('fileAccess');
