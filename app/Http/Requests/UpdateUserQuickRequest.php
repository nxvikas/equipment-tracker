<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserQuickRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'in:' . implode(',', \App\Http\Enums\UserStatus::values())],
            'position_id' => ['nullable', 'exists:positions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.string' => 'Статус должен быть текстовым',
            'status.in' => 'Выбран недопустимый статус',
            'position_id.exists' => 'Выбранная должность не существует',
        ];
    }
}
