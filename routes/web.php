<?php

use App\Http\Controllers\BulletinPageController;
use App\Http\Controllers\ReportRecipientController;
use Illuminate\Support\Facades\Route;

// Sitio público de boletines de seguridad (sin login). Cada visitante entra,
// navega el scope que quiere y ve/descarga ese boletín.
Route::get('/', [BulletinPageController::class, 'home'])->name('home');
Route::get('/boletin/{level}/{scope?}/pdf', [BulletinPageController::class, 'pdf'])->name('boletin.pdf');
Route::get('/boletin/{level}/{scope?}', [BulletinPageController::class, 'show'])->name('boletin');

// Destinatarios del reporte nacional (a quién le llega el PDF por correo).
Route::get('/destinatarios', [ReportRecipientController::class, 'index'])->name('destinatarios');
Route::post('/destinatarios', [ReportRecipientController::class, 'store'])->name('destinatarios.store');
Route::patch('/destinatarios/{recipient}', [ReportRecipientController::class, 'toggle'])->name('destinatarios.toggle');
Route::delete('/destinatarios/{recipient}', [ReportRecipientController::class, 'destroy'])->name('destinatarios.destroy');
