<?php

namespace App\Http\Controllers;

use App\Models\ReportRecipient;
use App\Repositories\RegionalRepository;
use App\Repositories\ReportRecipientRepository;
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
    public function __construct(
        private readonly ReportRecipientRepository $recipients,
        private readonly RegionalRepository $regionals,
    ) {}

    public function index(): View
    {
        $recipients = $this->recipients->allWithRegionals();
        $regionals = $this->regionals->allOrdered();

        return view('boletines.destinatarios', compact('recipients', 'regionals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            // Vacío = destinatario nacional; una o varias regionales = Nacional + esas regionales.
            'regional_ids' => ['nullable', 'array'],
            'regional_ids.*' => ['integer', Rule::exists('regionals', 'id')],
        ]);

        $this->recipients->upsertByEmail($data['email'], $data['name'] ?? null, $data['regional_ids'] ?? []);

        return redirect()->route('destinatarios')->with('ok', 'Destinatario guardado.');
    }

    public function toggle(ReportRecipient $recipient): RedirectResponse
    {
        $this->recipients->toggleActive($recipient);

        return redirect()->route('destinatarios');
    }

    public function destroy(ReportRecipient $recipient): RedirectResponse
    {
        $this->recipients->delete($recipient);

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
        $recipients = $this->recipients->activeWithRegionals();

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
