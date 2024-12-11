<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

Route::middleware('api')->prefix("api")->group(function () {
    Route::post('/setKey', [MainController::class, 'store']);
    Route::get('/getLatest/{key}', [MainController::class, 'getLatest']);
    Route::get('/getValueAt/{key}/{timestamp}', [MainController::class, 'getValueAt']);
    Route::get('/getAll', [MainController::class, 'getAll']);
});
