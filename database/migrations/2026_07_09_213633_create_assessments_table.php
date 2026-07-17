<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('Evaluador que realizó la visita')
                ->constrained()->cascadeOnDelete();
            $table->date('date')->comment('Fecha de la inspección');
            $table->string('city')->comment('Ciudad donde está el inmueble');
            $table->string('address')->comment('Dirección del inmueble evaluado');
            $table->string('responsible_party')->comment('Responsable del proyecto (texto libre)');
            $table->time('start_time')->comment('Hora de inicio de la visita');
            $table->time('end_time')->nullable()->comment('Hora de finalización de la visita');
            $table->decimal('gps_lat', 10, 7)->nullable()->comment('Latitud capturada por GPS del evaluador');
            $table->decimal('gps_lng', 10, 7)->nullable()->comment('Longitud capturada por GPS del evaluador');
            $table->unsignedInteger('gps_accuracy_m')->nullable()->comment('Precisión del GPS en metros');
            $table->unsignedInteger('gps_distance_m')->nullable()->comment('Distancia entre el GPS capturado y la dirección declarada, en metros');
            $table->text('general_notes')->nullable()->comment('Observaciones generales (Sección 8)');
            $table->text('conclusions')->nullable()->comment('Conclusión preliminar del análisis (Sección 9)');
            $table->string('status')->default('submitted')->comment('Estado de la evaluación (ej. submitted, draft)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
