<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Normaliza el destinatario por regional igual que departaments: en vez del
// nombre en texto (scope), un FK regional_id -> regionals. Nulo = destinatario
// nacional. Reemplaza scope_level/scope.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_recipients', function (Blueprint $table) {
            $table->foreignId('regional_id')->nullable()->after('name')
                ->comment('Regional VISE a la que pertenece el destinatario; nulo = nacional (recibe el panorama completo)')
                ->constrained('regionals')->nullOnDelete();
        });

        // Migra los datos existentes: scope (nombre de la regional) -> regional_id.
        foreach (DB::table('report_recipients')->where('scope_level', 'region')->whereNotNull('scope')->get() as $r) {
            $id = DB::table('regionals')->where('name', $r->scope)->value('id');
            if ($id) {
                DB::table('report_recipients')->where('id', $r->id)->update(['regional_id' => $id]);
            }
        }

        Schema::table('report_recipients', function (Blueprint $table) {
            $table->dropColumn(['scope_level', 'scope']);
        });
    }

    public function down(): void
    {
        Schema::table('report_recipients', function (Blueprint $table) {
            $table->string('scope_level')->default('national')->after('name');
            $table->string('scope')->nullable()->after('scope_level');
        });

        foreach (DB::table('report_recipients')->whereNotNull('regional_id')->get() as $r) {
            $name = DB::table('regionals')->where('id', $r->regional_id)->value('name');
            DB::table('report_recipients')->where('id', $r->id)->update([
                'scope_level' => $name ? 'region' : 'national',
                'scope' => $name,
            ]);
        }

        Schema::table('report_recipients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('regional_id');
        });
    }
};
