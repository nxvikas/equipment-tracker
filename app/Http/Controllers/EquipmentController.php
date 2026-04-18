<?php

namespace App\Http\Controllers;

use App\Http\Enums\TypeEquipmentHistory;
use App\Http\Requests\StoreEquipmentRequest;
use App\Models\Equipment;
use App\Models\Equipment_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function getQrCode(Equipment $equipment)
    {

        if (!$equipment->qr_code) {
            abort(404, 'QR-код не найден');
        }

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($equipment->qr_code) // Ссылка из базы данных
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High) // Высокий уровень коррекции (читается даже если часть затерта)
            ->size(300) // Размер картинки 300x300 px
            ->margin(10) // Отступы от краев
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        // Отдаем картинку прямо в браузер с правильным заголовком
        return response($result->getString())
            ->header('Content-Type', $result->getMimeType());
    }

    public function store(StoreEquipmentRequest $request)
    {
        $validated = $request->validated();


        if (empty($validated['inventory_number'])) {
            $year = date('Y');
            $nextId = Equipment::max('id') + 1;
            $validated['inventory_number'] = 'IT-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }


        $equipment = Equipment::create($validated);


        $equipment->update([
            'qr_code' => url('/admin/equipment/' . $equipment->id)
        ]);


        Equipment_history::create([
            'equipment_id' => $equipment->id,
            'action_type' => TypeEquipmentHistory::CREATED->value,
            'user_id' => Auth::id(),
            'to_location_id' => $equipment->location_id,
            'new_status' => $equipment->status,
            'comment' => 'Оборудование добавлено в систему'
        ]);

        return redirect()->route('admin.equipment')
            ->with('success', 'Оборудование успешно добавлено в базу');
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipment $equipment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipment $equipment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        //
    }
}
