<?php

namespace App\Exports\employee;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeEquipmentExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $equipments;
    protected $user;

    public function __construct($equipments, $user)
    {
        $this->equipments = $equipments;
        $this->user = $user;
    }

    public function array(): array
    {
        $exportData = [];


        $exportData[] = ['ОТЧЕТ О ЗАКРЕПЛЕННОМ ОБОРУДОВАНИИ'];
        $exportData[] = ['Сотрудник:', $this->user->surname . ' ' . $this->user->name . ' ' . ($this->user->patronymic ?? '')];
        $exportData[] = ['Отдел:', $this->user->department->name ?? 'Не назначен'];
        $exportData[] = ['Должность:', $this->user->position->name ?? 'Не назначена'];
        $exportData[] = ['Дата формирования:', now()->format('d.m.Y H:i:s')];
        $exportData[] = [' '];
        $exportData[] = [' '];


        $totalCount = $this->equipments->count();
        $exportData[] = ['Наименование отчета:', 'Список оборудования, закрепленного за сотрудником'];
        $exportData[] = ['Всего единиц техники:', $totalCount === 0 ? '0' : $totalCount];
        $exportData[] = [' '];
        $exportData[] = [' '];


        $exportData[] = ['ПЕРЕЧЕНЬ ОБОРУДОВАНИЯ'];
        $exportData[] = [
            '№ п/п',
            'Инвентарный номер',
            'Наименование оборудования',
            'Категория',
            'Производитель',
            'Модель',
            'Серийный номер',
            'Текущий статус',
            'Местоположение',
            'Примечание'
        ];

        if ($totalCount === 0) {
            $exportData[] = ['', '', 'Оборудование отсутствует', '', '', '', '', '', '', ''];
        } else {
            $index = 1;
            foreach ($this->equipments as $item) {
                $statusText = match($item->status) {
                    'in_use' => 'В использовании',
                    'repair' => 'В ремонте',
                    default => $item->status
                };

                $exportData[] = [
                    $index++,
                    $item->inventory_number,
                    $item->name,
                    $item->category->name ?? '—',
                    $item->manufacturer ?? '—',
                    $item->model ?? '—',
                    $item->serial_number ?? '—',
                    $statusText,
                    $item->location->name ?? '—',
                    $item->notes ?? '—'
                ];
            }
        }

        $exportData[] = [' '];
        $exportData[] = [' '];


        $exportData[] = ['Подпись сотрудника:', '___________________'];
        $exportData[] = ['Дата:', now()->format('d.m.Y')];
        $exportData[] = [' '];
        $exportData[] = ['Отчет сформирован автоматически в системе учета оборудования "УчетТМЦ"'];

        return $exportData;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Отчет_' . $this->user->surname . '_' . $this->user->name;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            5 => ['font' => ['italic' => true]],
            11 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
            14 => ['font' => ['bold' => true, 'size' => 12]],
            15 => ['font' => ['bold' => true]],
        ];
    }
}
