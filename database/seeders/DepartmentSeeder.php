<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::insert([
            ['name' => 'IT-отдел', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Бухгалтерия', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Отдел продаж', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Отдел кадров', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Юридический отдел', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Администрация', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
