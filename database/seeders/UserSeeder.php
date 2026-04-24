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
            ],
            [
                'surname' => 'Петрова',
                'name' => 'Анна',
                'patronymic' => 'Сергеевна',
                'email' => 'petrova@example.com',
                'phone' => '+79990000003',
            ],
            [
                'surname' => 'Сидоров',
                'name' => 'Сергей',
                'patronymic' => 'Алексеевич',
                'email' => 'sidorov@example.com',
                'phone' => '+79990000004',
            ],
            [
                'surname' => 'Кузнецова',
                'name' => 'Екатерина',
                'patronymic' => 'Дмитриевна',
                'email' => 'kuznetsova@example.com',
                'phone' => '+79990000005',
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
                'status' => UserStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
