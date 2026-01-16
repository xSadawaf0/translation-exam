<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\JwtController;

Route::post('/token', [JwtController::class, 'issue']);


use App\Http\Controllers\TranslationController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\LocaleController;

Route::middleware(['cors', 'jwt'])->group(function () {
    Route::get('/translations', [TranslationController::class, 'index']);
    Route::post('/translations', [TranslationController::class, 'store']);
    Route::get('/translations/{id}', [TranslationController::class, 'show']);
    Route::put('/translations/{id}', [TranslationController::class, 'update']);

    Route::get('/export/json', [TranslationController::class, 'export']);
    
    // Tag management
    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/tags', [TagController::class, 'store']);
    
    // Locale management
    Route::get('/locales', [LocaleController::class, 'index']);
    Route::post('/locales', [LocaleController::class, 'store']);
});
