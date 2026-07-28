<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * La plataforma ya no genera el formulario de identificación de riesgos
 * (evaluaciones). Se eliminan sus tablas. El orden respeta las llaves foráneas:
 * primero las hijas (fotos e items apuntan a assessments), luego assessments, y
 * por último cities (que ya no la usa nada).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('assessment_photos');
        Schema::dropIfExists('assessment_items');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('cities');
    }

    public function down(): void
    {
        // Irreversible: las migraciones de creación se eliminaron junto con la
        // funcionalidad del formulario.
    }
};
