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
        Schema::table('assessment_photos', function (Blueprint $table) {
            $table->decimal('gps_lat', 10, 7)->nullable()->comment('Latitud real del evaluador al tomar esta foto');
            $table->decimal('gps_lng', 10, 7)->nullable()->comment('Longitud real del evaluador al tomar esta foto');
            $table->unsignedInteger('gps_distance_m')->nullable()->comment('Distancia en metros entre el evaluador y el punto sugerido al tomar la foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessment_photos', function (Blueprint $table) {
            $table->dropColumn(['gps_lat', 'gps_lng', 'gps_distance_m']);
        });
    }
};
