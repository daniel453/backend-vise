<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    /**
     * Lista todas las ciudades, agrupadas implícitamente por departamento
     * para el desplegable del frontend (ordenadas para que las ciudades de
     * cada departamento queden juntas).
     */
    public function index(): JsonResponse
    {
        $cities = City::query()
            ->join('departaments', 'departaments.id', '=', 'cities.departament_id')
            ->orderBy('departaments.name')
            ->orderBy('cities.name')
            ->get([
                'cities.id',
                'cities.name',
                'departaments.name as department_name',
            ]);

        return response()->json($cities);
    }
}
