<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Un destinatario puede pertenecer a VARIAS regionales. Pasa de un solo
 * report_recipients.regional_id a una pivote muchos-a-muchos. Sin regionales
 * asociadas = destinatario nacional (recibe nacional + todas las regionales frescas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regional_report_recipient', function (Blueprint $table) {
            $table->foreignId('report_recipient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('regional_id')->constrained('regionals')->cascadeOnDelete();
            $table->primary(['report_recipient_id', 'regional_id']);
        });

        // Migra el regional_id existente a la pivote.
        if (Schema::hasColumn('report_recipients', 'regional_id')) {
            foreach (DB::table('report_recipients')->whereNotNull('regional_id')->get() as $r) {
                DB::table('regional_report_recipient')->insertOrIgnore([
                    'report_recipient_id' => $r->id,
                    'regional_id' => $r->regional_id,
                ]);
            }

            Schema::table('report_recipients', function (Blueprint $table) {
                $table->dropConstrainedForeignId('regional_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('report_recipients', function (Blueprint $table) {
            $table->foreignId('regional_id')->nullable()->after('name')
                ->constrained('regionals')->nullOnDelete();
        });

        // Restaura la PRIMERA regional de cada destinatario al campo único.
        foreach (DB::table('regional_report_recipient')->orderBy('regional_id')->get()->groupBy('report_recipient_id') as $rid => $rows) {
            DB::table('report_recipients')->where('id', $rid)->update(['regional_id' => $rows->first()->regional_id]);
        }

        Schema::dropIfExists('regional_report_recipient');
    }
};
