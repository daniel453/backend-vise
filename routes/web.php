<?php

use App\Http\Controllers\BulletinPageController;
use App\Http\Controllers\MarchCityController;
use App\Http\Controllers\ReportRecipientController;
use App\Http\Controllers\SpecialDateController;
use Illuminate\Support\Facades\Route;

// Sitio público de boletines de seguridad (sin login). Cada visitante entra,
// navega el scope que quiere y ve/descarga ese boletín.
Route::get('/', [BulletinPageController::class, 'home'])->name('home');
Route::get('/boletin-pdf/{level}/{scope?}', [BulletinPageController::class, 'pdf'])->name('boletin.pdf');
Route::get('/boletin/{level}/{scope?}', [BulletinPageController::class, 'show'])->name('boletin');

// Destinatarios del reporte nacional (a quién le llega el PDF por correo).
Route::get('/destinatarios', [ReportRecipientController::class, 'index'])->name('destinatarios');
Route::post('/destinatarios', [ReportRecipientController::class, 'store'])->name('destinatarios.store');
Route::patch('/destinatarios/{recipient}', [ReportRecipientController::class, 'toggle'])->name('destinatarios.toggle');
Route::delete('/destinatarios/{recipient}', [ReportRecipientController::class, 'destroy'])->name('destinatarios.destroy');
Route::post('/destinatarios/enviar-prueba', [ReportRecipientController::class, 'sendTest'])->name('destinatarios.prueba');
Route::post('/destinatarios/enviar-ahora', [ReportRecipientController::class, 'sendNow'])->name('destinatarios.enviar');

// Fechas especiales (envío cada 2h en vez de diario).
Route::get('/fechas-especiales', [SpecialDateController::class, 'index'])->name('fechas');
Route::post('/fechas-especiales', [SpecialDateController::class, 'store'])->name('fechas.store');
Route::delete('/fechas-especiales/{fecha}', [SpecialDateController::class, 'destroy'])->name('fechas.destroy');

// Ciudades que monitorea el boletín de marchas (las lee el workflow n8n).
Route::get('/marchas-ciudades', [MarchCityController::class, 'index'])->name('marchas.ciudades');
Route::post('/marchas-ciudades', [MarchCityController::class, 'store'])->name('marchas.ciudades.store');
Route::patch('/marchas-ciudades/{city}', [MarchCityController::class, 'toggle'])->name('marchas.ciudades.toggle');
Route::delete('/marchas-ciudades/{city}', [MarchCityController::class, 'destroy'])->name('marchas.ciudades.destroy');
