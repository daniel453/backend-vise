<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use App\Models\ReportRecipient;
use App\Services\BulletinDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gestión (sin login, MVP) de los correos a los que se envía el boletín
 * nacional en PDF. El workflow de n8n lee esta tabla para saber a quién enviar.
 */
class ReportRecipientController extends Controller
{
    public function index(): View
    {
        // Todos los destinatarios (nacionales primero, luego por regional).
        $recipients = ReportRecipient::query()
            ->with('regional')
            ->orderByRaw('regional_id IS NOT NULL')   // nacionales (null) primero
            ->orderBy('regional_id')
            ->orderByDesc('active')->orderBy('email')
            ->get();

        $regionals = Regional::query()->orderBy('name')->get();

        return view('boletines.destinatarios', compact('recipients', 'regionals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            // Vacío = destinatario nacional; una regional = recibe Nacional + su regional.
            'regional_id' => ['nullable', 'integer', Rule::exists('regionals', 'id')],
        ]);

        ReportRecipient::query()->updateOrCreate(
            ['email' => mb_strtolower($data['email'])],
            [
                'name' => $data['name'] ?? null,
                'regional_id' => $data['regional_id'] ?? null,
                'active' => true,
            ],
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

    /** Envía el boletín nacional a UN correo de prueba (para verificar el SMTP). */
    public function sendTest(Request $request, BulletinDispatcher $dispatcher): RedirectResponse
    {
        $data = $request->validate(['test_email' => 'required|email']);

        $result = $dispatcher->sendNational([['email' => $data['test_email'], 'name' => 'Prueba']], 'prueba');

        if (! ($result['ok'] ?? false)) {
            return back()->withErrors(['test_email' => 'Aún no hay un boletín nacional generado para enviar.']);
        }
        if (($result['failed'] ?? 0) > 0) {
            return back()->withErrors(['test_email' => 'Falló el envío: '.($result['errors'][0]['error'] ?? 'error desconocido')]);
        }

        return back()->with('ok', 'Correo de prueba enviado a '.$data['test_email'].'.');
    }

    /** Envía el boletín nacional a TODOS los destinatarios activos, AHORA (manual). */
    public function sendNow(BulletinDispatcher $dispatcher): RedirectResponse
    {
        $recipients = ReportRecipient::query()
            ->where('active', true)
            ->get();

        if ($recipients->isEmpty()) {
            return back()->withErrors(['email' => 'No hay destinatarios activos.']);
        }

        $result = $dispatcher->sendNational($recipients, 'manual');

        if (! ($result['ok'] ?? false)) {
            return back()->withErrors(['email' => 'Aún no hay un boletín nacional generado para enviar.']);
        }

        $msg = "Enviado a {$result['sent']} de {$result['total']} destinatarios.";
        if (($result['failed'] ?? 0) > 0) {
            $msg .= " {$result['failed']} fallaron.";
        }

        return back()->with('ok', $msg);
    }
}
