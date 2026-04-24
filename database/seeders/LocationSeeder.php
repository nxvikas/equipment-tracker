<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::insert([
            [
                'name' => 'Главный склад',
                'type' => 'warehouse',
                'address' => 'г. Москва, ул. Ленина, д. 1, складской комплекс',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IT-отдел',
                'type' => 'office',
                'address' => 'г. Москва, ул. Ленина, д. 1, офис 405',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Бухгалтерия',
                'type' => 'office',
                'address' => 'г. Москва, ул. Ленина, д. 1, офис 210',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Сервисный центр',
                'type' => 'service',
                'address' => 'г. Москва, ул. Строителей, д. 15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Удаленная работа',
                'type' => 'remote',
                'address' => 'Домашний офис сотрудника',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
