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
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Выбран недопустимый статус',
            'department_id.exists' => 'Выбранный отдел не существует',
            'position_id.exists' => 'Выбранная должность не существует',
        ];
    }
}
