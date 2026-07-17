<?php

use App\Http\Controllers\Api\AiValidationController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\BulletinController;
use App\Http\Controllers\Api\BulletinEventController;
use App\Http\Controllers\Api\ScrapingSourceController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/cities', [CityController::class, 'index']);

    Route::get('/assessments', [AssessmentController::class, 'index']);
    Route::post('/assessments', [AssessmentController::class, 'store']);
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show']);

    Route::post('/ai/validate-vulnerability', [AiValidationController::class, 'validateVulnerability']);
    Route::post('/ai/validate-consistency', [AiValidationController::class, 'validateConsistency']);
    Route::post('/ai/suggest-risks', [AiValidationController::class, 'suggestRisks']);

    Route::get('/scraping-sources', [ScrapingSourceController::class, 'index']);
    Route::get('/scraping-sources/national-media-domains', [ScrapingSourceController::class, 'nationalMediaDomains']);

    // Boletines generados (uno por scope) y sus eventos — los lee el HTML/tablero.
    Route::get('/bulletins', [BulletinController::class, 'index']);
    Route::get('/bulletins/{bulletin}', [BulletinController::class, 'show']);
    Route::get('/bulletin-events', [BulletinEventController::class, 'index']);
});
