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
        Schema::table('departaments', function (Blueprint $table) {
            $table->string('region')->nullable()->after('name')
                ->comment('Región natural del departamento (Andina, Caribe, Pacífica, Orinoquía, Amazonía) — usada para agrupar los boletines de seguridad y vial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departaments', function (Blueprint $table) {
            $table->dropColumn('region');
        });
    }
};
