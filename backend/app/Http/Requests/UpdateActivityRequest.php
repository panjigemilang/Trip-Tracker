<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $trip = $this->route('trip');
        $minDate = $trip && $trip->start_date ? $trip->start_date->format('Y-m-d') : null;
        $maxDate = $trip && $trip->end_date ? $trip->end_date->format('Y-m-d') : null;

        return [
            'date' => [
                'sometimes',
                'required',
                'date',
                $minDate ? 'after_or_equal:' . $minDate : '',
                $maxDate ? 'before_or_equal:' . $maxDate : '',
            ],
            'time' => ['sometimes', 'required', 'date_format:H:i'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
