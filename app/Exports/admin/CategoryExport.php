<?php

namespace App\Exports\admin;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoryExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $categories;

    public function __construct($categories = null)
    {
        $this->categories = $categories ?? Category::withCount('equipment')->get();
    }

    public function array(): array
    {
        $exportData = [];

        $exportData[] = ['ОТЧЕТ ПО КАТЕГОРИЯМ'];
        $exportData[] = ['Дата формирования:', now()->format('d.m.Y H:i:s')];
        $exportData[] = [' '];
        $exportData[] = [' '];

        $exportData[] = ['СПИСОК КАТЕГОРИЙ'];
        $exportData[] = ['ID', 'Название', 'Описание', 'Кол-во оборудования'];

        foreach ($this->categories as $category) {
            $exportData[] = [
                $category->id,
                $category->name,
                $category->description ?? '—',
                $category->equipment_count === 0 ? '0' : $category->equipment_count
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
        return 'Категории';
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
