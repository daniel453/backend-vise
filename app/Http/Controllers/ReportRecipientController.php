<?php

namespace App\Http\Controllers;

use App\Models\ReportRecipient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestión (sin login, MVP) de los correos a los que se envía el boletín
 * nacional en PDF. El workflow de n8n lee esta tabla para saber a quién enviar.
 */
class ReportRecipientController extends Controller
{
    public function index(): View
    {
        $recipients = ReportRecipient::query()
            ->where('scope_level', 'national')
            ->orderByDesc('active')->orderBy('email')
            ->get();

        return view('boletines.destinatarios', compact('recipients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        ReportRecipient::query()->updateOrCreate(
            ['email' => mb_strtolower($data['email'])],
            ['name' => $data['name'] ?? null, 'scope_level' => 'national', 'active' => true],
        );

        return redirect()->route('destinatarios')->with('ok', 'Destinatario guardado.');
    }

    public function toggle(ReportRecipient $recipient): RedirectResponse
    {
        $recipient->update(['active' => ! $recipient->active]);

        return redirect()->route('destinatarios');
    }

    public function destroy(ReportRecipient $recipient): RedirectResponse
    {
        $recipient->delete();

        return redirect()->route('destinatarios')->with('ok', 'Destinatario eliminado.');
    }
}
