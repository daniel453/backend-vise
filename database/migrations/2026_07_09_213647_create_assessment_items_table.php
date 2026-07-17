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
        Schema::create('assessment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('item_id')->comment('Identificador del ítem del checklist (ej. "2.1", "5.1")');
            $table->string('selected_option')->nullable()->comment('Opción elegida en el desplegable');
            $table->text('notes')->nullable()->comment('Observación u descripción detallada del ítem');
            $table->string('identified_text')->nullable()->comment('Texto libre: vulnerabilidad/riesgo identificado (Secciones 5 y 6)');
            $table->string('other_value')->nullable()->comment('Especificación cuando se elige la opción "Otro"');
            $table->string('ai_verification')->nullable()->comment('Resultado de la validación por IA (ej. Aprobado, Pendiente de revisar)');
            $table->timestamps();

            $table->unique(['assessment_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_items');
    }
};
