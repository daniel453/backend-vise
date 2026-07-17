<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['assessment_id', 'item_id', 'selected_option', 'notes', 'identified_text', 'other_value', 'ai_verification', 'source'])]
class AssessmentItem extends Model
{
    /**
     * Evaluación a la que pertenece este ítem.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
