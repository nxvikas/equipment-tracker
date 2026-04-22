<?php

namespace App\Exports\admin;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Equipment_history;
use App\Models\Location;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $data;
    protected $monthlyAssigns;
    protected $categories;
    protected $topUsers;
    protected $locationStats;
    protected $recentOperations;

    public function __construct()
    {

        $this->data = [
            'total' => Equipment::count(),
            'in_use' => Equipment::where('status', 'in_use')->count(),
            'in_stock' => Equipment::where('status', 'in_stock')->count(),
            'in_repair' => Equipment::where('status', 'repair')->count(),
            'written' => Equipment::where('status', 'written')->count(),
        ];

        $this->monthlyAssigns = Equipment_history::where('action_type', 'assigned')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $this->categories = Category::withCount('equipment')
            ->orderBy('equipment_count', 'desc')
            ->take(6)
            ->get();

        $this->topUsers = User::where('status', 'active')
            ->withCount(['equipment as equipment_count' => function ($query) {
                $query->where('status', 'in_use');
            }])
            ->having('equipment_count', '>', 0)
            ->orderBy('equipment_count', 'desc')
            ->take(5)
            ->get();

        $this->locationStats = Location::withCount('equipment')
            ->orderBy('equipment_count', 'desc')
            ->get();

        $this->recentOperations = Equipment_history::with(['equipment', 'user', 'toUser'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function array(): array
    {
        $exportData = [];


        $exportData[] = ['ОТЧЕТ ПО ДАШБОРДУ'];
        $exportData[] = ['Дата формирования:', now()->format('d.m.Y H:i:s')];
        $exportData[] = [' '];
        $exportData[] = [' '];

        // 1. ОСНОВНЫЕ МЕТРИКИ
        $exportData[] = ['1. ОСНОВНЫЕ МЕТРИКИ'];
        $exportData[] = ['Показатель', 'Значение'];
        $exportData[] = ['Всего активов', $this->data['total']];
        $exportData[] = ['В работе (выдано)', $this->data['in_use']];
        $exportData[] = ['На складе', $this->data['in_stock']];
        $exportData[] = ['В ремонте', $this->data['in_repair']];
        $exportData[] = ['Списано', $this->data['written']];
        $exportData[] = [' '];
        $exportData[] = [' '];

        // 2. ТОП КАТЕГОРИЙ
        $exportData[] = ['2. ПОПУЛЯРНЫЕ КАТЕГОРИИ ОБОРУДОВАНИЯ'];
        $exportData[] = ['Название категории', 'Количество'];
        foreach ($this->categories as $category) {
            $exportData[] = [$category->name, $category->equipment_count];
        }
        $exportData[] = [' '];
        $exportData[] = [' '];

        // 3. ТОП СОТРУДНИКОВ
        $exportData[] = ['3. ТОП СОТРУДНИКОВ ПО ТЕХНИКЕ'];
        $exportData[] = ['№', 'Сотрудник', 'Отдел', 'Кол-во техники'];
        $index = 1;
        foreach ($this->topUsers as $user) {
            $exportData[] = [
                $index++,
                $user->surname . ' ' . $user->name,
                $user->department->name ?? 'Без отдела',
                $user->equipment_count
            ];
        }
        $exportData[] = [' '];
        $exportData[] = [' '];

        // 4. РАСПРЕДЕЛЕНИЕ ПО ЛОКАЦИЯМ
        $exportData[] = ['4. РАСПРЕДЕЛЕНИЕ ПО ЛОКАЦИЯМ'];
        $exportData[] = ['Название локации', 'Тип', 'Кол-во оборудования'];
        foreach ($this->locationStats as $location) {
            $typeLabel = \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? $location->type;
            $exportData[] = [$location->name, $typeLabel, $location->equipment_count];
        }
        $exportData[] = [' '];
        $exportData[] = [' '];

        // 5. ДИНАМИКА ВЫДАЧ ПО МЕСЯЦАМ
        $exportData[] = ['5. ДИНАМИКА ВЫДАЧ (ПОСЛЕДНИЕ 6 МЕСЯЦЕВ)'];
        $exportData[] = ['Месяц', 'Количество выдач'];
        foreach ($this->monthlyAssigns as $item) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $item->month);
            $exportData[] = [$date->translatedFormat('F Y'), $item->count];
        }
        $exportData[] = [' '];
        $exportData[] = [' '];

        // 6. ПОСЛЕДНИЕ ОПЕРАЦИИ
        $exportData[] = ['6. ПОСЛЕДНИЕ ОПЕРАЦИИ (10 последних)'];
        $exportData[] = ['Дата', 'Оборудование', 'Инв. номер', 'Операция', 'Кто выполнил'];
        foreach ($this->recentOperations as $operation) {
            $operationText = \App\Http\Enums\TypeEquipmentHistory::ruValues()[$operation->action_type] ?? $operation->action_type;
            $exportData[] = [
                $operation->created_at->format('d.m.Y H:i'),
                $operation->equipment->name ?? '—',
                $operation->equipment->inventory_number ?? '—',
                $operationText,
                $operation->user->name ?? 'Система'
            ];
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Дашборд отчет';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
        ];
    }
}
