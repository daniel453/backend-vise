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
        Schema::create('assessment_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('item_id')->comment('Ítem al que pertenece la foto (ej. "2.1" o "perimeter_front")');
            $table->string('path')->comment('Ruta relativa dentro de storage/app/public');
            $table->unsignedInteger('sort_order')->default(0)->comment('Orden de la foto dentro del ítem');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_photos');
    }
};
