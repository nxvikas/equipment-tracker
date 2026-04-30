<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {

        Position::insert([
            [
                'name' => 'Разработчик',
                'department_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Системный администратор',
                'department_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Тестировщик',
                'department_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Team Lead',
                'department_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Position::insert([
            [
                'name' => 'Главный бухгалтер',
                'department_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Бухгалтер',
                'department_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Экономист',
                'department_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Position::insert([
            [
                'name' => 'Менеджер по продажам',
                'department_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Руководитель отдела продаж',
                'department_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Специалист по работе с клиентами',
                'department_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Position::insert([
            [
                'name' => 'HR-менеджер',
                'department_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Руководитель отдела кадров',
                'department_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Position::insert([
            [
                'name' => 'Юрист',
                'department_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Юрисконсульт',
                'department_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        Position::insert([
            [
                'name' => 'Директор',
                'department_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Заместитель директора',
                'department_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
