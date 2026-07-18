<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NationalBulletinMail;
use App\Models\DispatchSpecialDate;
use App\Models\ReportDispatchLog;
use App\Models\ReportRecipient;
use App\Services\BulletinReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Disparado por n8n: arma el PDF del boletín nacional y lo envía a los correos
 * activos. n8n hace el POST cada 2h; el BACKEND decide si enviar:
 *   - Fecha especial (dispatch_special_dates) -> envía cada 2h.
 *   - Día normal -> envía una sola vez al día, a partir de la hora configurada.
 * Reparte los correos entre varias cuentas (mailer 'roundrobin') con failover.
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
        $batchId = $data['bulletin']->batch_id;

        // No reenviar el mismo boletín (mismo batch_id).
        if ($batchId && ReportDispatchLog::query()->where('scope_level', 'national')->where('batch_id', $batchId)->exists()) {
            return response()->json(['ok' => true, 'sent' => 0, 'skipped' => 'boletin ya enviado']);
        }

        // --- Decisión de frecuencia (hora Colombia) ---
        $tz = config('services.bulletin_dispatch.timezone', 'America/Bogota');
        $now = Carbon::now($tz);
        $today = $now->toDateString();

        $isSpecial = DispatchSpecialDate::query()->whereDate('date', $today)->exists();

        if (! $isSpecial) {
            // Día normal: una vez al día, a partir de la hora configurada.
            $dailyHour = (int) config('services.bulletin_dispatch.daily_hour', 8);

            $alreadyToday = ReportDispatchLog::query()
                ->where('scope_level', 'national')
                ->whereDate('dispatch_date', $today)
                ->exists();

            if ($alreadyToday) {
                return response()->json(['ok' => true, 'sent' => 0, 'skipped' => 'ya se envió hoy (envío diario)']);
            }
            if ($now->hour < $dailyHour) {
                return response()->json(['ok' => true, 'sent' => 0, 'skipped' => "aún no es la hora diaria ({$dailyHour}h Colombia)"]);
            }
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

        // Mailer configurable: 'smtp' (una cuenta) o 'roundrobin' (varias cuentas
        // con balanceo + failover). Cada correo va por la cuenta que toque.
        $mailer = config('services.bulletin_dispatch.mailer', 'smtp');

        $sent = 0;
        $errors = [];
        foreach ($recipients as $recipient) {
            try {
                Mail::mailer($mailer)->to($recipient->email)->send(new NationalBulletinMail($pdf, $recipient->name, $dateLabel));
                $sent++;
            } catch (\Throwable $e) {
                $errors[] = ['email' => $recipient->email, 'error' => $e->getMessage()];
            }
        }

        ReportDispatchLog::query()->create([
            'scope_level' => 'national',
            'batch_id' => $batchId,
            'mode' => $isSpecial ? 'especial_2h' : 'diario',
            'dispatch_date' => $today,
            'recipients_total' => $recipients->count(),
            'recipients_sent' => $sent,
            'recipients_failed' => count($errors),
            'sent_at' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'mode' => $isSpecial ? 'especial_2h' : 'diario',
            'sent' => $sent,
            'failed' => count($errors),
            'errors' => $errors,
            'batch_id' => $batchId,
        ]);
    }
}
