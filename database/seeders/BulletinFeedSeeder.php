<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BulletinFeedSeeder extends Seeder
{
    /**
     * Carga las búsquedas por TEMA (Google News) desde
     * database/seeders/data/bulletin_feeds.csv (label,feed_type,query,url,category,active,sort_order).
     * El workflow híbrido las usa junto con el RSS propio de scraping_sources.
     * Se vacía y recarga completa en cada corrida.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/bulletin_feeds.csv');
        $handle = fopen($path, 'r');
        fgetcsv($handle); // header

        $rows = [];
        while (($line = fgetcsv($handle)) !== false) {
            $rows[] = [
                'label' => $line[0],
                'feed_type' => $line[1] !== '' ? $line[1] : 'google_news',
                'query' => ($line[2] ?? '') !== '' ? $line[2] : null,
                'url' => ($line[3] ?? '') !== '' ? $line[3] : null,
                'category' => ($line[4] ?? '') !== '' ? $line[4] : null,
                'active' => ! isset($line[5]) || strtolower($line[5]) !== 'false',
                'sort_order' => (int) ($line[6] ?? 0),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        fclose($handle);

        DB::table('bulletin_feeds')->truncate();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('bulletin_feeds')->insert($chunk);
        }
    }
}
