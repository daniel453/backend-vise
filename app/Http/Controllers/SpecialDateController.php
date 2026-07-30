<?php

namespace App\Http\Controllers;

use App\Models\DispatchSpecialDate;
use App\Repositories\DispatchSpecialDateRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestión (sin login, MVP) de las fechas especiales en las que el boletín
 * nacional se envía cada 2h en vez de una vez al día.
 */
class SpecialDateController extends Controller
{
    public function __construct(private readonly DispatchSpecialDateRepository $dates) {}

    public function index(): View
    {
        $dates = $this->dates->allOrdered();

        return view('boletines.fechas', compact('dates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);
        $this->dates->upsert($data['date'], $data['description'] ?? null);

        return redirect()->route('fechas')->with('ok', 'Fecha especial guardada.');
    }

    public function destroy(DispatchSpecialDate $fecha): RedirectResponse
    {
        $this->dates->delete($fecha);

        return redirect()->route('fechas')->with('ok', 'Fecha eliminada.');
    }
}
