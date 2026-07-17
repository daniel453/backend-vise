<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScrapingSourceSeeder extends Seeder
{
    /**
     * Carga la matriz de fuentes (Paso 1 - Contexto) desde
     * database/seeders/data/scraping_sources.csv (group,source,coverage,domain)
     * — la misma matriz que usan los flujos de n8n para armar los boletines.
     * Se vacía y recarga completa en cada corrida, para que sea el archivo CSV
     * el que manda sobre lo que quede en la tabla.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/scraping_sources.csv');
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle); // group,source,coverage,domain

        $rows = [];
        $sortOrder = 0;
        while (($line = fgetcsv($handle)) !== false) {
            $rows[] = [
                'group' => $line[0],
                'source' => $line[1],
                'coverage' => $line[2] !== '' ? $line[2] : null,
                'domain' => $line[3] !== '' ? $line[3] : null,
                'sort_order' => $sortOrder++,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        fclose($handle);

        DB::table('scraping_sources')->truncate();

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('scraping_sources')->insert($chunk);
        }
    }
}
