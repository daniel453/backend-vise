<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentRequest extends FormRequest
{
    /**
     * Indica si el usuario está autorizado a hacer esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación que aplican a la petición.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'responsible_party' => ['required', 'string', 'max:255'],
            'start_time' => ['required'],
            'end_time' => ['nullable'],
            'gps_lat' => ['nullable', 'numeric'],
            'gps_lng' => ['nullable', 'numeric'],
            'gps_accuracy_m' => ['nullable', 'integer'],
            'gps_distance_m' => ['nullable', 'integer'],
            'general_notes' => ['nullable', 'string'],
            'conclusions' => ['nullable', 'string'],

            'items' => ['required', 'array'],
            'items.*.item_id' => ['required', 'string'],
            'items.*.selected_option' => ['nullable', 'string'],
            'items.*.notes' => ['nullable', 'string'],
            'items.*.identified_text' => ['nullable', 'string'],
            'items.*.other_value' => ['nullable', 'string'],
            'items.*.ai_verification' => ['nullable', 'string'],
            'items.*.source' => ['nullable', 'string', 'in:user,ai'],
            'items.*.photos' => ['nullable', 'array'],
            'items.*.photos.*.data' => ['required', 'string'], // base64 data URL, decoded server-side
            'items.*.photos.*.gps_lat' => ['nullable', 'numeric'],
            'items.*.photos.*.gps_lng' => ['nullable', 'numeric'],
            'items.*.photos.*.gps_distance_m' => ['nullable', 'integer'],
        ];
    }
}
