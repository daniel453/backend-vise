<?php

namespace App\Repositories;

use App\Models\ReportDispatchLog;

/**
 * Acceso a datos del registro de envíos (report_dispatch_logs). Los modos
 * 'prueba', 'manual' y 'test' NO cuentan para dedup ni frecuencia.
 */
class ReportDispatchLogRepository
{
    private const NO_CUENTAN = ['prueba', 'manual', 'test'];

    /** ¿Ya se envió el boletín nacional de esta corrida (mismo batch)? */
    public function nationalAlreadySentForBatch(string $batch): bool
    {
        return ReportDispatchLog::query()
            ->where('scope_level', 'national')->where('batch_id', $batch)
            ->whereNotIn('mode', self::NO_CUENTAN)
            ->exists();
    }

    /** ¿Ya se envió el nacional hoy (envío diario)? */
    public function nationalAlreadySentToday(string $today): bool
    {
        return ReportDispatchLog::query()
            ->where('scope_level', 'national')
            ->whereDate('dispatch_date', $today)
            ->whereNotIn('mode', self::NO_CUENTAN)
            ->exists();
    }

    /** Último envío nacional real de hoy (para el intervalo de fechas especiales). */
    public function lastNationalToday(string $today): ?ReportDispatchLog
    {
        return ReportDispatchLog::query()
            ->where('scope_level', 'national')
            ->whereDate('dispatch_date', $today)
            ->whereNotIn('mode', self::NO_CUENTAN)
            ->latest('sent_at')->first();
    }

    public function log(array $data): void
    {
        ReportDispatchLog::query()->create($data);
    }
}
