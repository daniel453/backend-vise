<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Loads Colombia's municipalities from database/seeders/data/cities.csv
     * (id,name,departament_id) into the cities table. Uses upsert so it is
     * safe to re-run without duplicating or erroring on existing rows.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/cities.csv');
        $handle = fopen($path, 'r');

        $rows = [];
        while (($line = fgetcsv($handle)) !== false) {
            $rows[] = [
                'id' => (int) $line[0],
                'name' => $line[1],
                'departament_id' => (int) $line[2],
            ];
        }
        fclose($handle);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cities')->upsert($chunk, ['id'], ['name', 'departament_id']);
        }
    }
}
