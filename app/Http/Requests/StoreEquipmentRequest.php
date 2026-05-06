<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'unique:equipment,serial_number', 'max:255'],
            'inventory_number' => ['nullable', 'string', 'unique:equipment,inventory_number', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'status' => ['required', 'string', 'in:in_stock,in_use,repair'],
            'current_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'warranty_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('current_user_id', 'required', function ($input) {
            return $input->status === 'in_use';
        });
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле обязательно для заполнения',
            'string' => 'Поле должно быть текстовым',

            'name.max' => 'Название не должно превышать 255 символов',

            'serial_number.unique' => 'Оборудование с таким серийным номером уже существует',
            'serial_number.max' => 'Серийный номер не должен превышать 255 символов',

            'inventory_number.unique' => 'Оборудование с таким инвентарным номером уже существует',
            'inventory_number.max' => 'Инвентарный номер не должен превышать 255 символов',

            'manufacturer.max' => 'Производитель не должен превышать 255 символов',

            'model.max' => 'Модель не должна превышать 255 символов',

            'category_id.required' => 'Выберите категорию',
            'category_id.integer' => 'Категория должна быть выбрана корректно',
            'category_id.exists' => 'Выбранная категория не найдена в базе',

            'location_id.required' => 'Выберите местоположение',
            'location_id.integer' => 'Местоположение должно быть выбрано корректно',
            'location_id.exists' => 'Выбранная локация не найдена в базе',

            'status.required' => 'Выберите статус оборудования',
            'status.in' => 'Выбран недопустимый статус оборудования',

            'current_user_id.required' => 'Для статуса "В работе" необходимо выбрать сотрудника',
            'current_user_id.integer' => 'Сотрудник должен быть выбран корректно',
            'current_user_id.exists' => 'Выбранный сотрудник не найден',

            'purchase_date.date' => 'Введите корректную дату покупки',

            'purchase_price.numeric' => 'Стоимость должна быть числом',
            'purchase_price.min' => 'Стоимость не может быть отрицательной',

            'warranty_date.date' => 'Введите корректную дату окончания гарантии',
            'warranty_date.after_or_equal' => 'Дата гарантии не может быть раньше даты покупки',

            'notes.max' => 'Примечание не должно превышать 1000 символов',
        ];
    }
}
