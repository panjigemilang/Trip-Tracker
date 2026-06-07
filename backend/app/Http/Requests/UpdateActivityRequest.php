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
        return [
            'date' => ['sometimes', 'required', 'date'],
            'time' => ['sometimes', 'required', 'date_format:H:i'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
