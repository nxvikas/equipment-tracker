<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserFullRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'surname' => ['required', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'name' => ['required', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'patronymic' => ['nullable', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'email' => ['required', 'email', 'email:rfc', Rule::unique('users')->ignore($userId)],
            'phone' => ['required', 'regex:/^(?:\+7|8)\d{10}$/', Rule::unique('users')->ignore($userId)],
            'status' => ['required', 'string', 'in:' . implode(',', \App\Http\Enums\UserStatus::values())],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле обязательно для заполнения',
            'string' => 'Поле должно быть текстовым',

            'surname.regex' => 'Фамилия должна содержать только русские буквы и тире',
            'surname.max' => 'Фамилия не должна превышать 50 символов',

            'name.regex' => 'Имя должно содержать только русские буквы и тире',
            'name.max' => 'Имя не должно превышать 50 символов',

            'patronymic.regex' => 'Отчество должно содержать только русские буквы и тире',
            'patronymic.max' => 'Отчество не должно превышать 50 символов',

            'email.email' => 'Введите корректный email',
            'email.unique' => 'Данный email уже зарегистрирован',

            'phone.regex' => 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX',
            'phone.unique' => 'Данный номер уже зарегистрирован',

            'status.required' => 'Выберите статус пользователя',
            'status.in' => 'Выбран недопустимый статус',

            'department_id.exists' => 'Выбранный отдел не существует',

            'position_id.exists' => 'Выбранная должность не существует',
        ];
    }
}
