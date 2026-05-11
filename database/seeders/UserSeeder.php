<?php

namespace Database\Seeders;

use App\Models\User;
use App\Http\Enums\UserStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'surname' => 'Админов',
            'name' => 'Алексей',
            'patronymic' => 'Владимирович',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'phone' => '+79990000001',
            'role_id' => 1,
            'position_id' => null,
            'status' => UserStatus::ACTIVE->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        $employees = [
            [
                'surname' => 'Иванов',
                'name' => 'Иван',
                'patronymic' => 'Иванович',
                'email' => 'ivanov@example.com',
                'phone' => '+79990000002',
                'position_id' => 1,
            ],
            [
                'surname' => 'Петров',
                'name' => 'Петр',
                'patronymic' => 'Петрович',
                'email' => 'petrov@example.com',
                'phone' => '+79990000003',
                'position_id' => 2,
            ],
            [
                'surname' => 'Сидорова',
                'name' => 'Мария',
                'patronymic' => 'Алексеевна',
                'email' => 'sidorova@example.com',
                'phone' => '+79990000004',
                'position_id' => 3,
            ],
            [
                'surname' => 'Кузнецова',
                'name' => 'Екатерина',
                'patronymic' => 'Дмитриевна',
                'email' => 'kuznetsova@example.com',
                'phone' => '+79990000005',
                'position_id' => 5,
            ],
            [
                'surname' => 'Смирнова',
                'name' => 'Ольга',
                'patronymic' => 'Владимировна',
                'email' => 'smirnova@example.com',
                'phone' => '+79990000006',
                'position_id' => 4,
            ],
            [
                'surname' => 'Волков',
                'name' => 'Дмитрий',
                'patronymic' => 'Сергеевич',
                'email' => 'volkov@example.com',
                'phone' => '+79990000007',
                'position_id' => 7,
            ],
            [
                'surname' => 'Морозова',
                'name' => 'Анна',
                'patronymic' => 'Игоревна',
                'email' => 'morozova@example.com',
                'phone' => '+79990000008',
                'position_id' => 8,
            ],
            [
                'surname' => 'Новикова',
                'name' => 'Татьяна',
                'patronymic' => 'Павловна',
                'email' => 'novikova@example.com',
                'phone' => '+79990000009',
                'position_id' => 10,
            ],
            [
                'surname' => 'Федоров',
                'name' => 'Андрей',
                'patronymic' => 'Николаевич',
                'email' => 'fedorov@example.com',
                'phone' => '+79990000010',
                'position_id' => 12,
            ],
            [
                'surname' => 'Михайлов',
                'name' => 'Сергей',
                'patronymic' => 'Александрович',
                'email' => 'mikhailov@example.com',
                'phone' => '+79990000011',
                'position_id' => 14,
            ],
        ];


        foreach ($employees as $employee) {
            User::create([
                'surname' => $employee['surname'],
                'name' => $employee['name'],
                'patronymic' => $employee['patronymic'],
                'email' => $employee['email'],
                'password' => Hash::make('employee123'),
                'phone' => $employee['phone'],
                'role_id' => 2,
                'position_id' => $employee['position_id'],
                'status' => UserStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
