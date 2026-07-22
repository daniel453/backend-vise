<?php

namespace App\Services;

use App\Mail\NationalBulletinMail;
use App\Models\MarchBulletin;
use App\Models\MarchEvent;
use App\Models\Regional;
use App\Models\ReportDispatchLog;
use App\Support\BulletinPdfPresenter;
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

        $presenter = new BulletinPdfPresenter;

        // Cada boletín es su PROPIO PDF (adjunto aparte). El correo lleva varios
        // adjuntos en UN SOLO envío: siempre el Nacional + el/los regional(es)
        // que le correspondan al destinatario. Cada PDF se renderiza una sola vez.
        $nationalAttachment = [
            'name' => 'Boletin-Nacional.pdf',
            'data' => Pdf::loadView('boletines.pdf', $presenter->present($view))->setPaper('a4')->output(),
        ];

        // GUARDIA DE FRESCURA: el workflow regional es independiente del
        // nacional, así que una regional podría estar vieja (no corrió/falló).
        // Al destinatario NACIONAL solo se le adjuntan las regionales frescas
        // (no ensuciar con data vieja). Al destinatario de una regional SÍ se le
        // adjunta la suya aunque no sea la más fresca — su gente igual debe
        // recibir su boletín, y el encabezado del PDF trae la fecha.
        $maxAgeHours = (int) config('services.bulletin_dispatch.regional_max_age_hours', 6);
        $freshAfter = Carbon::now()->subHours($maxAgeHours);

        $regionalAttachments = [];      // clave regional_id => ['name'=>, 'data'=>]
        $freshRegionalAttachments = []; // solo las frescas, para los destinatarios nacionales
        foreach (Regional::query()->orderBy('name')->get() as $regional) {
            $regionalView = $this->reports->viewData('region', $regional->name);
            $regionalBulletin = $regionalView['bulletin'];
            if (! $regionalBulletin) {
                continue;
            }
            $attachment = [
                'name' => 'Boletin-'.str_replace(' ', '-', $regional->name).'.pdf',
                'data' => Pdf::loadView('boletines.pdf', $presenter->present($regionalView))->setPaper('a4')->output(),
            ];
            $regionalAttachments[$regional->id] = $attachment;
            if (Carbon::parse($regionalBulletin->generated_at)->gte($freshAfter)) {
                $freshRegionalAttachments[] = $attachment;
            }
        }

        // Boletín TEMÁTICO de marchas: adjunto extra para TODOS los destinatarios
        // (no depende de la regional). Se adjunta solo si hay uno reciente.
        $marchAttachment = null;
        $marchBulletin = MarchBulletin::query()->latest('generated_at')->first();
        if ($marchBulletin) {
            $marchFreshAfter = Carbon::now()->subHours((int) config('services.bulletin_dispatch.march_max_age_hours', 26));
            if (Carbon::parse($marchBulletin->generated_at)->gte($marchFreshAfter)) {
                $marchEvents = MarchEvent::query()
                    ->where('batch_id', $marchBulletin->batch_id)
                    ->orderBy('city')->orderBy('event_date')
                    ->get();
                $marchAttachment = [
                    'name' => 'Boletin-Marchas.pdf',
                    'data' => Pdf::loadView('boletines.marchas', array_merge(
                        BulletinPdfPresenter::sharedAssets(),
                        [
                            'bulletin' => $marchBulletin,
                            'events' => $marchEvents,
                            'generatedAt' => Carbon::parse($marchBulletin->generated_at),
                        ],
                    ))->setPaper('a4')->output(),
                ];
            }
        }

        $dateLabel = Carbon::parse($view['bulletin']->generated_at)->format('d/m/Y');
        $mailer = config('services.bulletin_dispatch.mailer', 'smtp');

        $total = 0;
        $sent = 0;
        $errors = [];
        foreach ($recipients as $r) {
            $total++;
            $email = is_array($r) ? ($r['email'] ?? null) : ($r->email ?? null);
            $name = is_array($r) ? ($r['name'] ?? null) : ($r->name ?? null);
            $regionalId = is_array($r) ? ($r['regional_id'] ?? null) : ($r->regional_id ?? null);
            if (! $email) {
                continue;
            }

            // Un correo, varios adjuntos: Nacional + el/los regional(es) que apliquen.
            $attachments = [$nationalAttachment];
            if ($regionalId !== null && isset($regionalAttachments[(int) $regionalId])) {
                $attachments[] = $regionalAttachments[(int) $regionalId];              // su regional
            } elseif ($regionalId === null) {
                $attachments = array_merge($attachments, $freshRegionalAttachments);   // nacional: todas las frescas
            }
            if ($marchAttachment) {
                $attachments[] = $marchAttachment;                                     // marchas: para todos
            }

            try {
                Mail::mailer($mailer)->to($email)->send(new NationalBulletinMail($attachments, $name, $dateLabel));
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
            // Regionales frescas adjuntadas a los destinatarios nacionales; cada
            // destinatario regional recibe la suya como adjunto aparte.
            'regionales_frescas' => count($freshRegionalAttachments),
            'marchas_adjuntadas' => $marchAttachment ? true : false,
        ];
    }
}
