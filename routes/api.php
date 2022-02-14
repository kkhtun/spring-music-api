<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\AudioParent_Audio_Controller;
use App\Http\Controllers\AudioParentController;
use App\Http\Controllers\Category_Audio_Controller;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CertificateController;
use App\Http\Middleware\EnsureAdminIsValid;
use App\Http\Middleware\EnsureRequestIsValid;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// Admin only routes
Route::middleware([EnsureAdminIsValid::class])->group(
    function () {
        // Admin
        Route::prefix('admins')->group(function () {
            Route::get('', [AdminController::class, 'index']);
            Route::get('/{admin}', [AdminController::class, 'show']);
            Route::patch('/{admin}', [AdminController::class, 'update']);
            Route::delete('/{admin}', [AdminController::class, 'destroy']);
        });
        Route::post('admin', [AdminController::class, 'store']);
        Route::post('admin/logout', [AdminController::class, 'logout']);

        // Category
        Route::prefix('categories')->group(function () {
            Route::post('/{category}', [CategoryController::class, 'update']);
            Route::delete('/{category}', [CategoryController::class, 'destroy']);
        });
        Route::post('category', [CategoryController::class, 'store']);

        // AudioParents
        Route::prefix('audioParents')->group(function () {
            Route::post('/{audioParent}', [AudioParentController::class, 'update']);
            Route::delete('/{audioParent}', [AudioParentController::class, 'destroy']);
        });
        Route::post('audioParent', [AudioParentController::class, 'store']);

        // Audio
        Route::prefix('audios')->group(function () {

            Route::delete('/{audio}', [AudioController::class, 'destroy']);
            Route::post('/{audio}', [AudioController::class, 'update']);
        });
        Route::post('audio', [AudioController::class, 'store']);

        // Certificates
        Route::prefix('certificates')->group(function () {
            Route::post('/{certificate}', [CertificateController::class, 'update']);
            Route::delete('/{certificate}', [CertificateController::class, 'destroy']);
        });
        Route::post('certificate', [CertificateController::class, 'store']);

        // Category_Audio
        Route::post('category_audios/{categoryId}', [Category_Audio_Controller::class, 'category_audios']);
        Route::post('audio_categories/{audioId}', [Category_Audio_Controller::class, 'audio_categories']);

        // AudioParent_Audio
        Route::post('audioParent_audios/{audioParentId}', [AudioParent_Audio_Controller::class, 'audioParent_audios']);
        Route::post('audio_audioParents/{audioId}', [AudioParent_Audio_Controller::class, 'audio_audioParents']);
    }
);


Route::middleware([EnsureRequestIsValid::class])->group(
    function () {
        // Category
        Route::prefix('categories')->group(function () {
            Route::get('', [CategoryController::class, 'index']);
            Route::get('/{category}', [CategoryController::class, 'show']);
        });

        // AudioParents
        Route::prefix('audioParents')->group(function () {
            Route::get('', [AudioParentController::class, 'index']);
            Route::get('/{audioParent}', [AudioParentController::class, 'show']);
        });

        // Audio
        Route::prefix('audios')->group(function () {
            Route::get('', [AudioController::class, 'index']);
            Route::get('/{audio}', [AudioController::class, 'show']);
        });

        // Certificates
        Route::prefix('certificates')->group(function () {
            Route::get('', [CertificateController::class, 'index']);
            Route::get('/{certificate}', [CertificateController::class, 'show']);
        });
    }
);

// No need middleware routes
Route::post('admin/login', [AdminController::class, 'login']);
