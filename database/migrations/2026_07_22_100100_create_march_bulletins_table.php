<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Encabezado del boletín TEMÁTICO de marchas (una fila por corrida del workflow
// de marchas). Sus marchas = march_events con el mismo batch_id.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('march_bulletins', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->nullable()->comment('Ata el boletín con sus march_events de la misma corrida');
            $table->string('headline')->nullable()->comment('Titular del día (ej. "JORNADA DE PARO EN 4 CIUDADES")');
            $table->text('conclusion')->nullable()->comment('Párrafo ejecutivo del panorama de marchas del día');
            $table->unsignedInteger('total_marches')->default(0);
            $table->unsignedInteger('cities_affected')->default(0);
            $table->text('recommendation')->nullable()->comment('Recomendación operativa/logística general');
            $table->timestamp('generated_at')->comment('Fecha/hora que reporta el boletín (Colombia)');
            $table->timestamps();

            $table->index('generated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('march_bulletins');
    }
};
