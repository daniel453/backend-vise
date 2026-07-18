<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'scope_level', 'batch_id', 'mode', 'dispatch_date',
    'recipients_total', 'recipients_sent', 'recipients_failed', 'sent_at',
])]
class ReportDispatchLog extends Model
{
    protected function casts(): array
    {
        return [
            'dispatch_date' => 'date',
            'sent_at' => 'datetime',
        ];
    }
}
