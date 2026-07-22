<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionalSeeder extends Seeder
{
    /**
     * Siembra las regionales OPERATIVAS de VISE (no las regiones naturales de
     * Colombia), tal como están definidas en el archivo maestro
     * "REGIONALES VISE.xlsx": Centro, Oriente, Norte y Occidental. El
     * departamento→regional lo resuelve DepartamentSeeder desde
     * departments.csv. Reemplaza el set completo — al borrar, el FK regional_id
     * de departaments queda en null (nullOnDelete) y DepartamentSeeder lo vuelve
     * a resolver por nombre. Los departamentos sin presencia VISE (Santander,
     * Chocó, Nariño, Córdoba, etc.) quedan con regional_id null a propósito.
     * Bogotá D.C. se cuenta dentro de Centro (junto con Cundinamarca).
     */
    public function run(): void
    {
        $regionals = ['Centro', 'Oriente', 'Norte', 'Occidental'];

        DB::table('regionals')->delete();

        DB::table('regionals')->insert(array_map(fn ($name) => [
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ], $regionals));
    }
}
