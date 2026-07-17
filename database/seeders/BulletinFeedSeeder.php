<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BulletinFeedSeeder extends Seeder
{
    /**
     * Carga los feeds RSS del generador de boletines desde
     * database/seeders/data/bulletin_feeds.csv (label,feed_type,query,url,category).
     * Son las mismas búsquedas de Google News + feeds directos que usa el flujo
     * de n8n. Se vacía y recarga completa en cada corrida, para que sea el CSV
     * el que manda sobre lo que quede en la tabla.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/bulletin_feeds.csv');
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle); // label,feed_type,query,url,category

        $rows = [];
        $sortOrder = 0;
        while (($line = fgetcsv($handle)) !== false) {
            $rows[] = [
                'label' => $line[0],
                'feed_type' => $line[1],
                'query' => $line[2] !== '' ? $line[2] : null,
                'url' => $line[3] !== '' ? $line[3] : null,
                'category' => $line[4] !== '' ? $line[4] : null,
                'active' => true,
                'sort_order' => $sortOrder++,
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
