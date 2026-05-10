<?php

namespace App\Exports\admin;

use App\Models\Department;
use App\Models\Position;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StructureExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $departments;
    protected $positions;

    public function __construct()
    {
        $this->departments = Department::withCount('users')->get();
        $this->positions = Position::withCount('users')->get();
    }

    public function array(): array
    {
        $exportData = [];

        $exportData[] = ['ОТЧЕТ ПО СТРУКТУРЕ КОМПАНИИ'];
        $exportData[] = ['Дата формирования:', now()->format('d.m.Y H:i:s')];
        $exportData[] = [' '];
        $exportData[] = [' '];

        // Отделы
        $exportData[] = ['ОТДЕЛЫ'];
        $exportData[] = ['ID', 'Название', 'Кол-во сотрудников'];
        foreach ($this->departments as $department) {
            $exportData[] = [
                $department->id,
                $department->name,
                $department->users_count === 0 ?: '0'
            ];
        }

        $exportData[] = [' '];
        $exportData[] = [' '];

        // Должности
        $exportData[] = ['ДОЛЖНОСТИ'];
        $exportData[] = ['ID', 'Название', 'Кол-во сотрудников'];
        foreach ($this->positions as $position) {
            $exportData[] = [
                $position->id,
                $position->name,
                $position->users_count === 0 ?: '0'
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
        return 'Структура';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['italic' => true]],
            5 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]],
        ];
    }
}
