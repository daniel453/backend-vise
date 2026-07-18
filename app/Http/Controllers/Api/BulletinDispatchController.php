<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NationalBulletinMail;
use App\Models\ReportRecipient;
use App\Services\BulletinReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Disparado por n8n: arma el PDF del boletín nacional y lo envía a los correos
 * activos registrados en la plataforma. n8n solo hace el POST; el backend hace
 * el PDF y el envío.
 */
class BulletinDispatchController extends Controller
{
    public function sendNational(Request $request, BulletinReportService $reports): JsonResponse
    {
        // Solo n8n (con el token compartido) puede disparar el envío masivo.
        $expected = config('services.bulletin_dispatch.token');
        $given = $request->header('X-Dispatch-Token') ?? $request->input('token');
        if (! $expected || ! is_string($given) || ! hash_equals($expected, $given)) {
            return response()->json(['ok' => false, 'error' => 'unauthorized'], 401);
        }

        $data = $reports->viewData('nacional', null);
        if (! $data['bulletin']) {
            return response()->json(['ok' => false, 'error' => 'no_bulletin'], 404);
        }

        $recipients = ReportRecipient::query()
            ->where('scope_level', 'national')
            ->where('active', true)
            ->get();

        if ($recipients->isEmpty()) {
            return response()->json(['ok' => true, 'sent' => 0, 'message' => 'sin destinatarios activos']);
        }

        // El PDF se arma una sola vez y se adjunta a todos los correos.
        $pdf = Pdf::loadView('boletines.pdf', $data)->setPaper('a4')->output();
        $dateLabel = Carbon::parse($data['bulletin']->generated_at)->format('d/m/Y');

        $sent = 0;
        $errors = [];
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new NationalBulletinMail($pdf, $recipient->name, $dateLabel));
                $sent++;
            } catch (\Throwable $e) {
                $errors[] = ['email' => $recipient->email, 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'ok' => true,
            'sent' => $sent,
            'failed' => count($errors),
            'errors' => $errors,
            'batch_id' => $data['bulletin']->batch_id,
        ]);
    }
}
