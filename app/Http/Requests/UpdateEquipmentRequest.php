<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $equipmentId = $this->route('equipment')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:equipment,serial_number,' . $equipmentId],
            'inventory_number' => ['nullable', 'string', 'max:255', 'unique:equipment,inventory_number,' . $equipmentId],
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
            'name.required' => 'Название обязательно для заполнения',
            'serial_number.unique' => 'Оборудование с таким серийным номером уже существует',
            'inventory_number.unique' => 'Оборудование с таким инвентарным номером уже существует',
            'category_id.required' => 'Выберите категорию',
            'category_id.exists' => 'Выбранная категория не найдена',
            'location_id.required' => 'Выберите местоположение',
            'location_id.exists' => 'Выбранная локация не найдена',
            'status.required' => 'Выберите статус',
            'status.in' => 'Выбран недопустимый статус',
            'current_user_id.required' => 'Для статуса "В работе" необходимо выбрать сотрудника',
            'current_user_id.exists' => 'Выбранный сотрудник не найден',
            'purchase_date.date' => 'Введите корректную дату',
            'purchase_price.numeric' => 'Стоимость должна быть числом',
            'purchase_price.min' => 'Стоимость не может быть отрицательной',
            'warranty_date.date' => 'Введите корректную дату',
            'warranty_date.after_or_equal' => 'Дата гарантии не может быть раньше даты покупки',
        ];
    }
}
