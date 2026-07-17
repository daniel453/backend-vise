<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RSS providers, especially Google News, can return redirect URLs longer
     * than 255 characters. Titles and source names also originate outside the
     * application, so they must not make a bulletin run fail.
     */
    public function up(): void
    {
        Schema::table('bulletin_events', function (Blueprint $table) {
            $table->text('title')->change();
            $table->text('media_outlet')->nullable()->change();
            $table->text('source_url')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bulletin_events', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('media_outlet')->nullable()->change();
            $table->string('source_url')->change();
        });
    }
};
