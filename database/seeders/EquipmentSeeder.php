<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Http\Enums\StatusEquipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role_id', 2)->get();
        $categories = Category::all();
        $locations = Location::all();

        $equipments = [
            [
                'name' => 'Apple MacBook Pro 14 M3',
                'inventory_number' => 'IT-2024-0001',
                'manufacturer' => 'Apple',
                'model' => 'MacBook Pro 14 M3',
                'serial_number' => 'SN-MBP-001',
                'status' => 'in_use',
                'purchase_date' => '2024-01-15',
                'purchase_price' => 189990.00,
                'warranty_date' => '2026-01-14',
                'notes' => 'Основной ноутбук разработчика',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dell XPS 15',
                'inventory_number' => 'IT-2024-0002',
                'manufacturer' => 'Dell',
                'model' => 'XPS 15 9530',
                'serial_number' => 'SN-DELL-002',
                'status' => 'in_use',
                'purchase_date' => '2024-02-10',
                'purchase_price' => 159990.00,
                'warranty_date' => '2026-02-09',
                'notes' => 'Ноутбук для дизайнера',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon',
                'inventory_number' => 'IT-2024-0003',
                'manufacturer' => 'Lenovo',
                'model' => 'ThinkPad X1 Carbon Gen 12',
                'serial_number' => 'SN-LENOVO-003',
                'status' => 'in_use',
                'purchase_date' => '2024-03-05',
                'purchase_price' => 169990.00,
                'warranty_date' => '2026-03-04',
                'notes' => 'Для руководителя IT-отдела',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dell UltraSharp U2723QE',
                'inventory_number' => 'IT-2024-0004',
                'manufacturer' => 'Dell',
                'model' => 'UltraSharp U2723QE',
                'serial_number' => 'SN-MON-004',
                'status' => 'in_use',
                'purchase_date' => '2024-01-20',
                'purchase_price' => 45990.00,
                'warranty_date' => '2026-01-19',
                'notes' => '4K монитор для разработчика',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'HP LaserJet Pro M404dn',
                'inventory_number' => 'IT-2024-0005',
                'manufacturer' => 'HP',
                'model' => 'LaserJet Pro M404dn',
                'serial_number' => 'SN-PRN-005',
                'status' => 'in_stock',
                'purchase_date' => '2024-02-15',
                'purchase_price' => 18990.00,
                'warranty_date' => '2025-02-14',
                'notes' => 'Черно-белый принтер для офиса',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Logitech MX Master 3S',
                'inventory_number' => 'IT-2024-0006',
                'manufacturer' => 'Logitech',
                'model' => 'MX Master 3S',
                'serial_number' => 'SN-MOU-006',
                'status' => 'in_use',
                'purchase_date' => '2024-03-10',
                'purchase_price' => 10990.00,
                'warranty_date' => '2025-03-09',
                'notes' => 'Беспроводная мышь',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Keychron K2 Pro',
                'inventory_number' => 'IT-2024-0007',
                'manufacturer' => 'Keychron',
                'model' => 'K2 Pro',
                'serial_number' => 'SN-KBD-007',
                'status' => 'repair',
                'purchase_date' => '2024-01-25',
                'purchase_price' => 12990.00,
                'warranty_date' => '2025-01-24',
                'status_comment' => 'Не работает подсветка',
                'notes' => 'Механическая клавиатура',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ASUS RT-AX88U',
                'inventory_number' => 'IT-2024-0008',
                'manufacturer' => 'ASUS',
                'model' => 'RT-AX88U',
                'serial_number' => 'SN-RTR-008',
                'status' => 'in_use',
                'purchase_date' => '2024-02-20',
                'purchase_price' => 24990.00,
                'warranty_date' => '2026-02-19',
                'notes' => 'Маршрутизатор для офиса',
                'qr_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($equipments as $index => $equipment) {

            $userId = ($equipment['status'] === 'in_use') ? $users[$index % count($users)]->id : null;
            $locationId = $locations[$index % count($locations)]->id;
            $categoryId = $categories[$index % count($categories)]->id;

            Equipment::create([
                'name' => $equipment['name'],
                'inventory_number' => $equipment['inventory_number'],
                'manufacturer' => $equipment['manufacturer'],
                'model' => $equipment['model'],
                'serial_number' => $equipment['serial_number'],
                'status' => $equipment['status'],
                'status_comment' => $equipment['status_comment'] ?? null,
                'purchase_date' => $equipment['purchase_date'],
                'purchase_price' => $equipment['purchase_price'],
                'warranty_date' => $equipment['warranty_date'],
                'notes' => $equipment['notes'],
                'qr_code' => null, // QR-код сгенерируется при создании через контроллер
                'current_user_id' => $userId,
                'location_id' => $locationId,
                'category_id' => $categoryId,
                'created_at' => $equipment['created_at'],
                'updated_at' => $equipment['updated_at'],
            ]);
        }
    }
}
