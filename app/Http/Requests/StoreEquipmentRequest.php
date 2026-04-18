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
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'warranty_date' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле обязательно для заполнения',
            'string' => 'Поле должно быть текстовым',
            'max' => 'Максимальная длина не должна превышать :max символов',
            'unique' => 'Запись с таким значением уже существует',
            'serial_number.unique' => 'Оборудование с таким серийным номером уже существует',
            'inventory_number.unique' => 'Оборудование с таким инвентарным номером уже существует',
            'category_id.exists' => 'Выбранная категория не найдена в базе',
            'location_id.exists' => 'Выбранная локация не найдена в базе',
            'status.in' => 'Выбран недопустимый статус оборудования',
            'purchase_date.date' => 'Введите корректную дату покупки',
            'purchase_price.numeric' => 'Стоимость должна быть числом',
            'purchase_price.min' => 'Стоимость не может быть отрицательной',
            'warranty_date.date' => 'Введите корректную дату окончания гарантии',
            'warranty_date.after_or_equal' => 'Дата гарантии не может быть раньше даты покупки',
        ];
    }
}
