<?php

namespace App\Http\Controllers;

use App\Models\MarchCity;
use App\Repositories\MarchCityRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestión (sin login, MVP) de las ciudades que monitorea el boletín de marchas.
 * El workflow de n8n lee las activas para saber dónde buscar.
 */
class MarchCityController extends Controller
{
    public function __construct(private readonly MarchCityRepository $cities) {}

    public function index(): View
    {
        $cities = $this->cities->allOrdered();

        return view('boletines.marchas-ciudades', compact('cities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $this->cities->upsertByName($data['name']);

        return redirect()->route('marchas.ciudades')->with('ok', 'Ciudad guardada.');
    }

    public function toggle(MarchCity $city): RedirectResponse
    {
        $this->cities->toggleActive($city);

        return redirect()->route('marchas.ciudades');
    }

    public function destroy(MarchCity $city): RedirectResponse
    {
        $this->cities->delete($city);

        return redirect()->route('marchas.ciudades')->with('ok', 'Ciudad eliminada.');
    }
}
