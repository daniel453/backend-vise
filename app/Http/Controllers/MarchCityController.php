<?php

namespace App\Http\Controllers;

use App\Models\MarchCity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestión (sin login, MVP) de las ciudades que monitorea el boletín de marchas.
 * El workflow de n8n lee las activas para saber dónde buscar.
 */
class MarchCityController extends Controller
{
    public function index(): View
    {
        $cities = MarchCity::query()
            ->orderByDesc('active')->orderBy('sort_order')->orderBy('name')
            ->get();

        return view('boletines.marchas-ciudades', compact('cities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        MarchCity::query()->updateOrCreate(
            ['name' => trim($data['name'])],
            ['active' => true, 'sort_order' => (int) MarchCity::query()->max('sort_order') + 1],
        );

        return redirect()->route('marchas.ciudades')->with('ok', 'Ciudad guardada.');
    }

    public function toggle(MarchCity $city): RedirectResponse
    {
        $city->update(['active' => ! $city->active]);

        return redirect()->route('marchas.ciudades');
    }

    public function destroy(MarchCity $city): RedirectResponse
    {
        $city->delete();

        return redirect()->route('marchas.ciudades')->with('ok', 'Ciudad eliminada.');
    }
}
