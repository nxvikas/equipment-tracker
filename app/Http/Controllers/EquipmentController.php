<?php

namespace App\Http\Controllers;

use App\Http\Enums\StatusEquipment;
use App\Http\Enums\TypeEquipmentHistory;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Models\Equipment;
use App\Models\Equipment_history;
use App\Models\Location;
use App\Models\User;
use Dotenv\Validator;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class EquipmentController extends Controller
{

    public function getQrCode(Equipment $equipment)
    {
        if (!$equipment->qr_code) {
            abort(404, 'QR-код не найден');
        }

        $result = new Builder(
            writer: new PngWriter(),
            data: $equipment->qr_code,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $qrCode = $result->build();

        return response($qrCode->getString())
            ->header('Content-Type', $qrCode->getMimeType());
    }

    public function store(StoreEquipmentRequest $request)
    {
        $validated = $request->validated();


        if ($validated['status'] === 'in_use' && empty($validated['current_user_id'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['current_user_id' => ['Для статуса "В работе" необходимо выбрать сотрудника']]
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Для статуса "В работе" необходимо выбрать сотрудника')
                ->with('reopen_equipment_modal', true)
                ->withInput();
        }

        if ($validated['status'] !== 'in_use') {
            $validated['current_user_id'] = null;
        }

        if (empty($validated['inventory_number'])) {
            $year = date('Y');
            $nextId = Equipment::max('id') + 1;
            $validated['inventory_number'] = 'IT-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        $equipment = Equipment::create($validated);

        $equipment->update([
            'qr_code' => route('admin.equipment.qrcode', $equipment->id)
        ]);

        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => TypeEquipmentHistory::CREATED->value,
            'user_id' => Auth::id(),
            'to_location_id' => $equipment->location_id,
            'new_status' => $equipment->status,
            'comment' => 'Оборудование добавлено в систему'
        ]);


        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Оборудование "' . $equipment->name . '" успешно добавлено',
                'item' => $equipment
            ]);
        }

        return redirect()->route('admin.equipment')
            ->with('success', 'Оборудование успешно добавлено в базу');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load(['category', 'location', 'currentUser', 'history.user', 'history.toUser', 'history.toLocation']);

        $categories = \App\Models\Category::all();
        $locations = \App\Models\Location::all();
        $users = \App\Models\User::where('status', 'active')->orderBy('name')->get();

        return view('admin.equipment.show', compact('equipment', 'categories', 'locations', 'users'));
    }


    public function update(UpdateEquipmentRequest $request, Equipment $equipment)
    {
        $validated = $request->validated();

        if ($validated['status'] === 'in_use' && empty($validated['current_user_id'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['current_user_id' => ['Для статуса "В работе" необходимо выбрать сотрудника']]
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Для статуса "В работе" необходимо выбрать сотрудника')
                ->with('edit_modal_open', true)
                ->withInput();
        }

        if ($validated['status'] !== 'in_use') {
            $validated['current_user_id'] = null;
        }

        $equipment->update($validated);

        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => TypeEquipmentHistory::MOVED->value,
            'user_id' => Auth::id(),
            'new_status' => $equipment->status,
            'comment' => 'Оборудование отредактировано'
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Оборудование обновлено',
                'item' => $equipment
            ]);
        }

        return redirect()->route('admin.equipment.show', $equipment->id)
            ->with('success', 'Оборудование обновлено');
    }

    public function destroy(Equipment $equipment)
    {
        $otherActions = $equipment->history()
            ->where('action_type', '!=', TypeEquipmentHistory::CREATED->value)
            ->exists();

        if ($otherActions) {
            return redirect()->back()->with('error', 'Нельзя удалить оборудование с историей операций');
        }

        $name = $equipment->name;
        $equipment->delete();

        return redirect()->route('admin.equipment')
            ->with('success', "Оборудование $name удалено");
    }


    public function assign(Request $request, Equipment $equipment)
    {
        if ($equipment->status !== StatusEquipment::IN_STOCK->value) {
            return response()->json([
                'success' => false,
                'message' => 'Оборудование должно быть на складе для выдачи'
            ], 400);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'user_id.required' => 'Выберите сотрудника',
            'user_id.exists' => 'Выбранный сотрудник не найден',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $oldLocationId = $equipment->location_id;

        $equipment->update([
            'status' => StatusEquipment::IN_USE->value,
            'current_user_id' => $request->user_id,
            'location_id' => $request->location_id ?? $equipment->location_id,
        ]);

        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => TypeEquipmentHistory::ASSIGNED->value,
            'user_id' => Auth::id(),
            'to_user_id' => $request->user_id,
            'from_location_id' => $oldLocationId,
            'to_location_id' => $equipment->location_id,
            'new_status' => StatusEquipment::IN_USE->value,
            'comment' => $request->comment,
        ]);

        session()->flash('success', 'Оборудование выдано сотруднику');

        return response()->json(['success' => true]);
    }

    public function return(Request $request, Equipment $equipment)
    {
        if ($equipment->status !== StatusEquipment::IN_USE->value) {
            return redirect()->back()->with('error', 'Оборудование не находится в использовании');
        }

        $request->validate([
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $oldUserId = $equipment->current_user_id;

        $equipment->update([
            'status' => StatusEquipment::IN_STOCK->value,
            'current_user_id' => null,
        ]);

        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => TypeEquipmentHistory::RETURNED->value,
            'user_id' => Auth::id(),
            'from_user_id' => $oldUserId,
            'new_status' => StatusEquipment::IN_STOCK->value,
            'comment' => $request->comment ?? 'Возвращено на склад',
        ]);

        return redirect()->route('admin.equipment.show', $equipment->id)
            ->with('success', 'Оборудование возвращено на склад');
    }


    public function repair(Request $request, Equipment $equipment)
    {
        if ($equipment->status === StatusEquipment::WRITTEN->value) {
            return response()->json([
                'success' => false,
                'message' => 'Списанное оборудование нельзя отправить в ремонт'
            ], 400);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'location_id' => ['required', 'exists:locations,id'],
            'comment' => ['required', 'string', 'max:500'],
        ], [
            'location_id.required' => 'Выберите сервисный центр',
            'comment.required' => 'Укажите причину ремонта',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $oldStatus = $equipment->status;
        $oldLocationId = $equipment->location_id;
        $oldUserId = $equipment->current_user_id;

        $equipment->update([
            'status' => StatusEquipment::REPAIR->value,
            'location_id' => $request->location_id,
            'status_comment' => $request->comment,
            'current_user_id' => null,
        ]);

        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => TypeEquipmentHistory::REPAIRED->value,
            'user_id' => Auth::id(),
            'from_user_id' => $oldUserId,
            'from_location_id' => $oldLocationId,
            'to_location_id' => $request->location_id,
            'old_status' => $oldStatus,
            'new_status' => StatusEquipment::REPAIR->value,
            'comment' => $request->comment,
        ]);

        session()->flash('success', 'Оборудование отправлено в ремонт');

        return response()->json(['success' => true]);
    }
    public function returnFromRepair(Request $request, Equipment $equipment)
    {
        if ($equipment->status !== StatusEquipment::REPAIR->value) {
            return response()->json([
                'success' => false,
                'message' => 'Оборудование не находится в ремонте'
            ], 400);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'location_id' => ['required', 'exists:locations,id'],
            'comment' => ['nullable', 'string', 'max:500'],
        ], [
            'location_id.required' => 'Выберите локацию для возврата',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $newLocation = Location::find($request->location_id);

        if ($newLocation->type === 'service') {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя вернуть из ремонта в сервисный центр'
            ], 400);
        }

        $oldLocationId = $equipment->location_id;

        $equipment->update([
            'status' => StatusEquipment::IN_STOCK->value,
            'location_id' => $request->location_id,
            'status_comment' => null,
            'current_user_id' => null,
        ]);

        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => TypeEquipmentHistory::REPAIRED->value,
            'user_id' => Auth::id(),
            'from_location_id' => $oldLocationId,
            'to_location_id' => $request->location_id,
            'old_status' => StatusEquipment::REPAIR->value,
            'new_status' => StatusEquipment::IN_STOCK->value,
            'comment' => $request->comment ?? 'Возвращено из ремонта',
        ]);

        // Сохраняем сообщение в сессию для отображения после перезагрузки
        session()->flash('success', 'Оборудование возвращено из ремонта');

        return response()->json([
            'success' => true
        ]);
    }



    public function writeOff(Request $request, Equipment $equipment)
    {
        if ($equipment->status === StatusEquipment::WRITTEN->value) {
            return response()->json([
                'success' => false,
                'message' => 'Оборудование уже списано'
            ], 400);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'comment' => ['required', 'string', 'max:500'],
        ], [
            'comment.required' => 'Укажите причину списания',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $oldStatus = $equipment->status;
        $oldUserId = $equipment->current_user_id;

        $equipment->update([
            'status' => StatusEquipment::WRITTEN->value,
            'status_comment' => $request->comment,
            'current_user_id' => null,
        ]);

        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => TypeEquipmentHistory::WRITTEN->value,
            'user_id' => Auth::id(),
            'from_user_id' => $oldUserId,
            'old_status' => $oldStatus,
            'new_status' => StatusEquipment::WRITTEN->value,
            'comment' => $request->comment,
        ]);

        session()->flash('success', 'Оборудование списано');

        return response()->json(['success' => true]);
    }
}
