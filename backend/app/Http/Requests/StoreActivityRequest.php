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
        return [
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }
}
