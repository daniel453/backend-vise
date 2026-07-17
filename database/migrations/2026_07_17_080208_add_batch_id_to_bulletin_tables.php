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
        Schema::table('bulletin_events', function (Blueprint $table) {
            $table->string('batch_id')->nullable()->after('id')
                ->comment('Id de la corrida que generó/actualizó este evento (id de la respuesta de la IA) — ata el evento a los boletines de esa misma corrida');
            $table->index('batch_id');
        });

        Schema::table('bulletins', function (Blueprint $table) {
            $table->string('batch_id')->nullable()->after('id')
                ->comment('Id de la corrida (id de la respuesta de la IA) — los eventos del boletín son los de security_bulletins con este mismo batch_id que caen en el scope');
            $table->index('batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulletin_events', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropColumn('batch_id');
        });
        Schema::table('bulletins', function (Blueprint $table) {
            $table->dropIndex(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};
