<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('index');

Route::group(['middleware' => ['auth', 'verified']], function() {

    Route::get('/', function () {
        return view('index');
    })->name('index');
    Route::get('/setting', function () {
        return view('setting');
    })->name('setting');
    Route::get('/file', function () {
        return view('welcome');
    })->name('welcome');

    Route::get('/c', [\App\Http\Controllers\Controller::class, 'index']);
    Route::post('/upload', [\App\Http\Controllers\FileUploader::class, 'upload'])->name('file.store');
    Route::post('/compare', [\App\Http\Controllers\FileUploader::class, 'compare'])->name('file.compare');
    Route::get('/unique', [\App\Http\Controllers\ApiController::class, 'unique']);
    Route::post('/setting', [\App\Http\Controllers\FileUploader::class, 'setting'])->name('settings');
    Route::get('/change', [\App\Http\Controllers\FileUploader::class, 'change'])->name('change');
    Route::get('/cutImages/{hash}/{count}', [\App\Http\Controllers\FileUploader::class, 'cutImageArray'])->name('cutImage');
    Route::get('/123', [\App\Http\Controllers\ApiController::class, 'index'])->name('cutImage');
});


require __DIR__.'/auth.php';
