<?php

namespace App\Services;

use App\Mail\NationalBulletinMail;
use App\Models\ReportDispatchLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Arma el PDF del boletín nacional y lo envía a una lista de destinatarios,
 * usando el mailer configurado (smtp / roundrobin con failover) y dejando
 * registro en report_dispatch_logs. Lo comparten el disparo de n8n y los
 * botones de prueba/envío manual de la web.
 */
class BulletinDispatcher
{
    public function __construct(private BulletinReportService $reports) {}

    /**
     * @param  iterable  $recipients  objetos/arrays con 'email' y (opcional) 'name'
     * @return array{ok:bool, error?:string, sent?:int, failed?:int, total?:int, errors?:array, batch_id?:mixed}
     */
    public function sendNational(iterable $recipients, string $mode): array
    {
        $view = $this->reports->viewData('nacional', null);
        if (! $view['bulletin']) {
            return ['ok' => false, 'error' => 'no_bulletin'];
        }

        $pdf = Pdf::loadView('boletines.pdf', $view)->setPaper('a4')->output();
        $dateLabel = Carbon::parse($view['bulletin']->generated_at)->format('d/m/Y');
        $mailer = config('services.bulletin_dispatch.mailer', 'smtp');

        $total = 0;
        $sent = 0;
        $errors = [];
        foreach ($recipients as $r) {
            $total++;
            $email = is_array($r) ? ($r['email'] ?? null) : ($r->email ?? null);
            $name = is_array($r) ? ($r['name'] ?? null) : ($r->name ?? null);
            if (! $email) {
                continue;
            }
            try {
                Mail::mailer($mailer)->to($email)->send(new NationalBulletinMail($pdf, $name, $dateLabel));
                $sent++;
            } catch (\Throwable $e) {
                $errors[] = ['email' => $email, 'error' => $e->getMessage()];
            }
        }

        ReportDispatchLog::query()->create([
            'scope_level' => 'national',
            'batch_id' => $view['bulletin']->batch_id,
            'mode' => $mode,
            'dispatch_date' => Carbon::now(config('services.bulletin_dispatch.timezone', 'America/Bogota'))->toDateString(),
            'recipients_total' => $total,
            'recipients_sent' => $sent,
            'recipients_failed' => count($errors),
            'sent_at' => now(),
        ]);

        return [
            'ok' => true,
            'total' => $total,
            'sent' => $sent,
            'failed' => count($errors),
            'errors' => $errors,
            'batch_id' => $view['bulletin']->batch_id,
        ];
    }
}
