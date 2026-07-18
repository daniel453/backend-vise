<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Fechas especiales (ej. elecciones, eventos de orden público) en las que el
// boletín nacional se envía cada 2h en vez de una vez al día.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_special_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique()->comment('Día en el que el envío es cada 2h (hora Colombia)');
            $table->string('description')->nullable()->comment('Motivo (ej. "Elecciones", "Paro nacional")');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_special_dates');
    }
};
