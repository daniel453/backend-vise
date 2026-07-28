<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BulletinController;
use App\Http\Controllers\Api\BulletinDispatchController;
use App\Http\Controllers\Api\BulletinEventController;
use App\Http\Controllers\Api\ScrapingSourceController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

// Disparo del envío del boletín nacional por correo (lo llama n8n con el token
// compartido X-Dispatch-Token). El backend arma el PDF y envía a la DB.
Route::post('/boletines/enviar-nacional', [BulletinDispatchController::class, 'sendNational']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/scraping-sources', [ScrapingSourceController::class, 'index']);
    Route::get('/scraping-sources/national-media-domains', [ScrapingSourceController::class, 'nationalMediaDomains']);

    // Boletines generados (uno por scope) y sus eventos — los lee el HTML/tablero.
    Route::get('/bulletins', [BulletinController::class, 'index']);
    Route::get('/bulletins/{bulletin}', [BulletinController::class, 'show']);
    Route::get('/bulletin-events', [BulletinEventController::class, 'index']);
});
