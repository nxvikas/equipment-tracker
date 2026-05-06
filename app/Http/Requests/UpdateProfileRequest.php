<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'surname' => ['required', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'name' => ['required', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'patronymic' => ['nullable', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'email' => ['required', 'email', 'email:rfc', Rule::unique('users')->ignore($userId)],
            'phone' => ['required', 'regex:/^(?:\+7|8)\d{10}$/', Rule::unique('users')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'regex:/^[A-Za-z0-9]+$/u', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
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

            'password.min' => 'Пароль должен быть не менее 8 символов',
            'password.regex' => 'Пароль может содержать только латинские буквы (A-Z, a-z) и цифры (0-9)',
            'password.confirmed' => 'Пароли не совпадают',

            'avatar.image' => 'Файл должен быть изображением',
            'avatar.mimes' => 'Поддерживаемые форматы: jpeg, png, jpg, gif',
            'avatar.max' => 'Размер файла не должен превышать 5MB',
        ];
    }
}
