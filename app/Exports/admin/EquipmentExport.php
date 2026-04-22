<?php

namespace App\Exports\admin;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquipmentExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $equipments;

    public function __construct($equipments = null)
    {
        $this->equipments = $equipments ?? Equipment::with(['category', 'currentUser'])->get();
    }

    public function array(): array
    {
        $exportData = [];

        $exportData[] = ['ОТЧЕТ ПО ОБОРУДОВАНИЮ'];
        $exportData[] = ['Дата формирования:', now()->format('d.m.Y H:i:s')];
        $exportData[] = [' '];
        $exportData[] = [' '];

        $exportData[] = ['СПИСОК ОБОРУДОВАНИЯ'];
        $exportData[] = [
            'ID', 'Инв. номер', 'Название', 'Категория', 'Производитель',
            'Модель', 'Серийный номер', 'Статус', 'Сотрудник', 'Локация',
            'Дата покупки', 'Стоимость', 'Гарантия до', 'Примечание'
        ];

        foreach ($this->equipments as $item) {
            $exportData[] = [
                $item->id,
                $item->inventory_number,
                $item->name,
                $item->category->name ?? '—',
                $item->manufacturer ?? '—',
                $item->model ?? '—',
                $item->serial_number ?? '—',
                \App\Http\Enums\StatusEquipment::ruValues()[$item->status] ?? $item->status,
                $item->currentUser ? $item->currentUser->surname . ' ' . $item->currentUser->name : '—',
                $item->location->name ?? '—',
                $item->purchase_date?->format('d.m.Y') ?? '—',
                $item->purchase_price ? number_format($item->purchase_price, 0, ',', ' ') . ' ₽' : '—',
                $item->warranty_date?->format('d.m.Y') ?? '—',
                $item->notes ?? '—'
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
        return 'Оборудование';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            5 => ['font' => ['bold' => true]],
        ];
    }
}
