<?php

namespace Database\Seeders;

use App\Http\Enums\StatusEquipment;
use App\Http\Enums\TypeEquipmentHistory;
use App\Models\Equipment;
use App\Models\Equipment_history;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Seeder;

class EquipmentHistorySeeder extends Seeder
{
    public function run(): void
    {
        $equipments = Equipment::all();
        $users = User::where('role_id', 2)->get();
        $admin = User::where('role_id', 1)->first();
        $locations = Location::all();

        foreach ($equipments as $index => $equipment) {
            // 1. История создания
            Equipment_history::create([
                'equipment_id' => $equipment->id,
                'action_type' => TypeEquipmentHistory::CREATED->value, // 'created'
                'user_id' => $admin->id,
                'from_user_id' => null,
                'to_user_id' => null,
                'from_location_id' => null,
                'to_location_id' => $equipment->location_id,
                'old_status' => null,
                'new_status' => $equipment->status,
                'comment' => 'Оборудование добавлено в систему',
                'created_at' => $equipment->created_at,
                'updated_at' => $equipment->created_at,
            ]);


            if ($equipment->status === StatusEquipment::IN_USE->value && $equipment->current_user_id) {
                Equipment_history::create([
                    'equipment_id' => $equipment->id,
                    'action_type' => TypeEquipmentHistory::ASSIGNED->value,
                    'user_id' => $admin->id,
                    'from_user_id' => null,
                    'to_user_id' => $equipment->current_user_id,
                    'from_location_id' => $equipment->location_id,
                    'to_location_id' => null,
                    'old_status' => StatusEquipment::IN_STOCK->value,
                    'new_status' => StatusEquipment::IN_USE->value,
                    'comment' => 'Выдано сотруднику',
                    'created_at' => $equipment->created_at->addMinutes(5),
                    'updated_at' => $equipment->created_at->addMinutes(5),
                ]);
            }


            if ($equipment->status === StatusEquipment::REPAIR->value) {
                $serviceLocation = Location::where('type', 'service')->first();


                $oldStatus = $equipment->current_user_id
                    ? StatusEquipment::IN_USE->value
                    : StatusEquipment::IN_STOCK->value;

                Equipment_history::create([
                    'equipment_id' => $equipment->id,
                    'action_type' => TypeEquipmentHistory::REPAIRED->value,
                    'user_id' => $admin->id,
                    'from_user_id' => $equipment->current_user_id,
                    'to_user_id' => null,
                    'from_location_id' => $equipment->location_id,
                    'to_location_id' => $serviceLocation?->id,
                    'old_status' => $oldStatus,
                    'new_status' => StatusEquipment::REPAIR->value,
                    'comment' => $equipment->status_comment ?? 'Отправлено в ремонт',
                    'created_at' => $equipment->created_at->addMinutes(10),
                    'updated_at' => $equipment->created_at->addMinutes(10),
                ]);
            }
        }
    }
}
