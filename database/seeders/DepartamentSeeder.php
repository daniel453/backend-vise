<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentSeeder extends Seeder
{
    /**
     * Loads Colombia's departments from database/seeders/data/departments.csv
     * (id,name,regional) into the departaments table, resolviendo regional_id
     * contra la tabla regionals (corre DESPUÉS de RegionalSeeder). Upsert para
     * poder re-correr sin duplicar.
     */
    public function run(): void
    {
        $regionalIds = DB::table('regionals')->pluck('id', 'name'); // name => id

        $path = database_path('seeders/data/departments.csv');
        $handle = fopen($path, 'r');

        $rows = [];
        while (($line = fgetcsv($handle)) !== false) {
            $regionalName = $line[2] ?? null;
            $rows[] = [
                'id' => (int) $line[0],
                'name' => $line[1],
                'regional_id' => $regionalName ? ($regionalIds[$regionalName] ?? null) : null,
            ];
        }
        fclose($handle);

        DB::table('departaments')->upsert($rows, ['id'], ['name', 'regional_id']);
    }
}
