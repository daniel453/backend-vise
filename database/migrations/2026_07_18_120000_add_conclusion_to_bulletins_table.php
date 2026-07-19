<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Conclusión ejecutiva redactada por la IA (párrafo de 2-3 frases) para el
// boletín nacional. Se muestra en el PDF.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bulletins', function (Blueprint $table) {
            $table->text('conclusion')->nullable()->after('headline')
                ->comment('Conclusión ejecutiva redactada por la IA (nivel nacional)');
        });
    }

    public function down(): void
    {
        Schema::table('bulletins', function (Blueprint $table) {
            $table->dropColumn('conclusion');
        });
    }
};
