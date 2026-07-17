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
        // Regionales operativas de VISE (Centro, Norte, Occidente, etc.) — el
        // nivel "región" de los boletines se arma con esta tabla, no con nombres
        // quemados en el flujo de n8n.
        Schema::create('regionals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nombre de la regional VISE (ej. "Centro", "Norte", "Occidente")');
            $table->timestamps();
        });

        Schema::table('departaments', function (Blueprint $table) {
            $table->foreignId('regional_id')->nullable()->after('name')
                ->comment('Regional VISE a la que pertenece el departamento — fuente del roll-up "región" de los boletines')
                ->constrained('regionals')->nullOnDelete();
            $table->dropColumn('region'); // se reemplaza por regional_id (antes guardaba la región natural DANE)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departaments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('regional_id');
            $table->string('region')->nullable();
        });
        Schema::dropIfExists('regionals');
    }
};
