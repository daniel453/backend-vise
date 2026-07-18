<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Bitácora de envíos del boletín. Sirve para (1) no reenviar el mismo boletín,
// (2) controlar el "una vez al día" en días normales, y (3) auditoría.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_dispatch_logs', function (Blueprint $table) {
            $table->id();
            $table->string('scope_level')->default('national')->comment('Nivel del boletín enviado');
            $table->string('batch_id')->nullable()->comment('batch_id del boletín enviado (para no reenviarlo)');
            $table->string('mode')->nullable()->comment('"diario" o "especial_2h"');
            $table->date('dispatch_date')->comment('Fecha del envío en hora Colombia (para el control diario)');
            $table->unsignedInteger('recipients_total')->default(0);
            $table->unsignedInteger('recipients_sent')->default(0);
            $table->unsignedInteger('recipients_failed')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['scope_level', 'dispatch_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_dispatch_logs');
    }
};
