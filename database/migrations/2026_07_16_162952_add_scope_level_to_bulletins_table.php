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
        Schema::table('bulletins', function (Blueprint $table) {
            $table->string('scope_level')->default('national')->after('id')
                ->comment('Nivel del boletín: "municipality", "department", "region" o "national" — de lo particular a lo general');
            $table->string('department')->nullable()->after('scope')
                ->comment('Departamento al que pertenece este scope (para municipality/department); null en region/national');
            $table->string('region')->nullable()->after('department')
                ->comment('Región natural a la que pertenece este scope; null en national');

            $table->index(['scope_level', 'scope', 'generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulletins', function (Blueprint $table) {
            $table->dropIndex(['scope_level', 'scope', 'generated_at']);
            $table->dropColumn(['scope_level', 'department', 'region']);
        });
    }
};
