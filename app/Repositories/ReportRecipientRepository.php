<?php

namespace App\Repositories;

use App\Models\ReportRecipient;
use Illuminate\Database\Eloquent\Collection;

/** Acceso a datos de los destinatarios del boletín (report_recipients + pivote). */
class ReportRecipientRepository
{
    /** Todos, con sus regionales, para la pantalla de administración. */
    public function allWithRegionals(): Collection
    {
        return ReportRecipient::query()
            ->with('regionals')
            ->orderByDesc('active')->orderBy('email')
            ->get();
    }

    /** Activos (con regionales) — a quién se le envía el boletín. */
    public function activeWithRegionals(): Collection
    {
        return ReportRecipient::query()->with('regionals')->where('active', true)->get();
    }

    /** Marcados como prueba (test=true) — para el envío de prueba. */
    public function testRecipients(): Collection
    {
        return ReportRecipient::query()->where('test', true)->get();
    }

    /** Crea/actualiza por email (activo) y sincroniza sus regionales. */
    public function upsertByEmail(string $email, ?string $name, array $regionalIds): ReportRecipient
    {
        $recipient = ReportRecipient::query()->updateOrCreate(
            ['email' => mb_strtolower($email)],
            ['name' => $name, 'active' => true],
        );
        $recipient->regionals()->sync($regionalIds);

        return $recipient;
    }

    public function toggleActive(ReportRecipient $recipient): void
    {
        $recipient->update(['active' => ! $recipient->active]);
    }

    public function delete(ReportRecipient $recipient): void
    {
        $recipient->delete();
    }
}
