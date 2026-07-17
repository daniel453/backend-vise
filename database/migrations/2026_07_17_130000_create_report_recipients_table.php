<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique()->comment('Correo al que se envía el boletín en PDF');
            $table->string('name')->nullable()->comment('Nombre del destinatario o empresa cliente');
            $table->string('scope_level')->default('national')->comment('Nivel del boletín que recibe: national/region/department/municipality');
            $table->string('scope')->nullable()->comment('Scope específico (ej. región o dpto); nulo = nacional');
            $table->boolean('active')->default(true)->comment('Si está activo para recibir los envíos automáticos');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_recipients');
    }
};
