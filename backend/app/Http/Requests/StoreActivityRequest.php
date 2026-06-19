<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
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
                'required', 
                'date',
                $minDate ? 'after_or_equal:' . $minDate : '',
                $maxDate ? 'before_or_equal:' . $maxDate : '',
            ],
            'time' => ['required', 'date_format:H:i'],
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'images' => ['sometimes', 'array'],
            'images.*.base64' => ['required_with:images', 'string'],
            'images.*.name' => ['required_with:images', 'string'],
        ];
    }
}
