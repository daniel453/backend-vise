<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Marca de destinatario de PRUEBA. Con {"mode":"test"} en el disparo, el correo
// se envía YA solo a los que tengan test = true, sin afectar el envío programado.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_recipients', function (Blueprint $table) {
            $table->boolean('test')->default(false)->after('active')
                ->comment('Recibe los envíos de prueba (mode=test): correo inmediato solo a estos, sin contar para el envío programado');
        });
    }

    public function down(): void
    {
        Schema::table('report_recipients', function (Blueprint $table) {
            $table->dropColumn('test');
        });
    }
};
