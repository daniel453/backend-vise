<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Some RSS items are national or do not identify a Colombian department.
     * Keeping them unlocated is safer than assigning an invented department.
     */
    public function up(): void
    {
        Schema::table('bulletin_events', function (Blueprint $table) {
            $table->string('department')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bulletin_events', function (Blueprint $table) {
            $table->string('department')->nullable(false)->change();
        });
    }
};
