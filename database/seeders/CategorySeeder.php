<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Ноутбуки',
                'description' => 'Портативные компьютеры для работы и учебы',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Мониторы',
                'description' => 'Дисплеи для настольных компьютеров',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Принтеры',
                'description' => 'Печатающие устройства',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Сетевое оборудование',
                'description' => 'Коммутаторы, маршрутизаторы, точки доступа',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Оргтехника',
                'description' => 'Клавиатуры, мыши, гарнитуры',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
