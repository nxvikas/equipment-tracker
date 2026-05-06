<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'surname' => ['required', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'name' => ['required', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'patronymic' => ['nullable', 'string', 'regex:/^[А-ЯЁа-яё\-]+$/u', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email', 'email:rfc'],
            'phone' => ['required', 'regex:/^(?:\+7|8)\d{10}$/','unique:users,phone'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^[A-Za-z0-9]+$/u'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле обязательно для заполнения',

            'surname.regex' => 'Фамилия должна содержать только русские буквы и тире',
            'name.regex' => 'Имя должно содержать только русские буквы и тире',
            'patronymic.regex' => 'Отчество должно содержать только русские буквы и тире',
            'surname.max' => 'Фамилия не должна превышать 50 символов',
            'name.max' => 'Имя не должна превышать 50 символов',
            'patronymic.max' => 'Отчество не должна превышать 50 символов',

            'phone.regex' => 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX',
            'phone.unique' => 'Данный номер уже зарегистрирован',

            'email.email' => 'Введите корректный email',
            'email.unique' => 'Данный email уже зарегистрирован',

            'password.confirmed' => 'Пароли не совпадают',
            'password.min' => 'Пароль должен содержать не менее 8 символов',
            'password.regex' => 'Пароль может содержать только латинские буквы (A-Z, a-z) и цифры (0-9)',
        ];
    }
}
