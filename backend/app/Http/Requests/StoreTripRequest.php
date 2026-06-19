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
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'activities' => ['sometimes', 'array'],
            'activities.*.date' => ['required_with:activities', 'date'],
            'activities.*.time' => ['required_with:activities', 'date_format:H:i'],
            'activities.*.title' => ['required_with:activities', 'string', 'max:255'],
            'activities.*.location' => ['nullable', 'string', 'max:500'],
            'activities.*.notes' => ['nullable', 'string'],
            'activities.*.sort_order' => ['nullable', 'integer'],
            'activities.*.images' => ['sometimes', 'array'],
            'activities.*.images.*.base64' => ['required_with:activities.*.images', 'string'],
            'activities.*.images.*.name' => ['required_with:activities.*.images', 'string'],
            'images' => ['sometimes', 'array'],
            'images.*.base64' => ['required_with:images', 'string'],
            'images.*.name' => ['required_with:images', 'string'],
        ];
    }
}
