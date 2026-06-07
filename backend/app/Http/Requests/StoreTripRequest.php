<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'activities' => ['sometimes', 'array'],
            'activities.*.date' => ['required_with:activities', 'date'],
            'activities.*.time' => ['required_with:activities', 'date_format:H:i'],
            'activities.*.title' => ['required_with:activities', 'string', 'max:255'],
            'activities.*.location' => ['nullable', 'string', 'max:500'],
            'activities.*.notes' => ['nullable', 'string'],
            'activities.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}
