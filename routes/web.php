<?php

use App\Http\Controllers\BulletinPageController;
use Illuminate\Support\Facades\Route;

// Sitio público de boletines de seguridad (sin login). Cada visitante entra,
// busca/navega el scope que quiere y ve ese boletín.
Route::get('/', [BulletinPageController::class, 'home'])->name('home');
Route::get('/buscar', [BulletinPageController::class, 'search'])->name('boletin.buscar');
Route::get('/boletin/{level}/{scope?}', [BulletinPageController::class, 'show'])->name('boletin');
