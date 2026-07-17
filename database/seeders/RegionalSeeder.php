<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionalSeeder extends Seeder
{
    /**
     * Siembra las 6 regiones naturales de Colombia (nombres propios): Caribe,
     * Andina, Pacífica, Orinoquía, Amazonía e Insular. Reemplaza el set
     * completo — al borrar, el FK regional_id de departaments queda en null
     * (nullOnDelete) y DepartamentSeeder lo vuelve a resolver por nombre.
     */
    public function run(): void
    {
        $regionals = ['Caribe', 'Andina', 'Pacífica', 'Orinoquía', 'Amazonía', 'Insular'];

        DB::table('regionals')->delete();

        DB::table('regionals')->insert(array_map(fn ($name) => [
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ], $regionals));
    }
}
