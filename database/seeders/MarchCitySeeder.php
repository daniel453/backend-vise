<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Ciudades iniciales del boletín de marchas. Upsert por nombre para poder
 * re-correr sin duplicar; después se administran desde la web.
 */
class MarchCitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Bucaramanga'];

        $rows = [];
        foreach ($cities as $i => $name) {
            $rows[] = [
                'name' => $name,
                'active' => true,
                'sort_order' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('march_cities')->upsert($rows, ['name'], ['active', 'sort_order', 'updated_at']);
    }
}
