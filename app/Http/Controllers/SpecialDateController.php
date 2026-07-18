<?php

namespace App\Http\Controllers;

use App\Models\DispatchSpecialDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestión (sin login, MVP) de las fechas especiales en las que el boletín
 * nacional se envía cada 2h en vez de una vez al día.
 */
class SpecialDateController extends Controller
{
    public function index(): View
    {
        $dates = DispatchSpecialDate::query()
            ->orderBy('date')
            ->get();

        return view('boletines.fechas', compact('dates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        DispatchSpecialDate::query()->updateOrCreate(
            ['date' => $data['date']],
            ['description' => $data['description'] ?? null],
        );

        return redirect()->route('fechas')->with('ok', 'Fecha especial guardada.');
    }

    public function destroy(DispatchSpecialDate $fecha): RedirectResponse
    {
        $fecha->delete();

        return redirect()->route('fechas')->with('ok', 'Fecha eliminada.');
    }
}
