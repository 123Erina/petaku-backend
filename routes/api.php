<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BasemapController;
use App\Http\Controllers\Api\CallCenterController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CCTVController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\FiberOptikController;
use App\Http\Controllers\Api\GoogleBusinessController;
use App\Http\Controllers\Api\OpdController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RoadLayerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\GuestMapController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

// Basemap
Route::get('/import-basemap', [BasemapController::class, 'import']);
Route::get('/basemap/{jenis}', [BasemapController::class, 'get']);

// Fiber
Route::get('/import-fiber', [FiberOptikController::class, 'import']);
Route::get('/fiber-optik/{jalur}', [FiberOptikController::class, 'getFiber']);

// Call Center
Route::get('/import-call-center', [CallCenterController::class, 'import']);
Route::get('/call-center/{kategori}', [CallCenterController::class, 'getCallCenter']);

// CCTV
Route::get('/import-cctv', [CCTVController::class, 'import']);
Route::get('/cctv', [CCTVController::class, 'index']);

// Google Business
Route::get('/import-google-business', [GoogleBusinessController::class, 'import']);
Route::get('/google-business/{kategori}', [GoogleBusinessController::class, 'getBusiness']);

// Report
Route::get('/import-report', [ReportController::class, 'import']);
Route::get('/report/{kategori}', [ReportController::class, 'getReport']);

// Road Layer
Route::get('/import-road-layer', [RoadLayerController::class, 'import']);
Route::get('/road-layer/{jenis}', [RoadLayerController::class, 'get']);

// Guest Maps
Route::get('/guest/maps/getPlaces/{category}', [GuestMapController::class, 'getPlaces']);

// Places
Route::get('/places', [PlaceController::class, 'index']);
Route::get('/places/{id}', [PlaceController::class, 'show']);
Route::get('/places/category/{category}', [PlaceController::class, 'getPlaces']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);

// OPD
Route::get('/home/opds', [OpdController::class, 'index']);
Route::get('/opds', [OpdController::class, 'index']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('users', UserController::class);

    Route::apiResource('opds', OpdController::class)->except(['index']);

    Route::post('/places', [PlaceController::class, 'store']);
    Route::put('/places/{id}', [PlaceController::class, 'update']);
    Route::delete('/places/{id}', [PlaceController::class, 'destroy']);
});
