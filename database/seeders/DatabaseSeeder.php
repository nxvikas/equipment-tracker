<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            CategorySeeder::class,
            LocationSeeder::class,
            UserSeeder::class,
            EquipmentSeeder::class,
            EquipmentHistorySeeder::class,
        ]);
    }
}
