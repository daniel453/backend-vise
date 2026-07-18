<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scraping_sources', function (Blueprint $table) {
            $table->string('rss_url')->nullable()->after('domain')
                ->comment('URL del RSS propio de la fuente confiable; el conector RSS consume SOLO estos feeds para que en el boletín solo entren dominios de esta tabla');
        });
    }

    public function down(): void
    {
        Schema::table('scraping_sources', function (Blueprint $table) {
            $table->dropColumn('rss_url');
        });
    }
};
