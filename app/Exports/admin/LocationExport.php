<?php

namespace App\Exports\admin;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LocationExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $locations;

    public function __construct($locations = null)
    {
        $this->locations = $locations ?? Location::withCount('equipment')->get();
    }

    public function array(): array
    {
        $exportData = [];

        $exportData[] = ['ОТЧЕТ ПО ЛОКАЦИЯМ'];
        $exportData[] = ['Дата формирования:', now()->format('d.m.Y H:i:s')];
        $exportData[] = [' '];
        $exportData[] = [' '];

        $exportData[] = ['СПИСОК ЛОКАЦИЙ'];
        $exportData[] = ['ID', 'Название', 'Тип', 'Адрес', 'Кол-во оборудования'];

        foreach ($this->locations as $location) {
            $typeLabel = \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? $location->type;
            $exportData[] = [
                $location->id,
                $location->name,
                $typeLabel,
                $location->address ?? '—',
                $location->equipment_count === 0 ? '0' : $location->equipment_count,
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
        return 'Локации';
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
