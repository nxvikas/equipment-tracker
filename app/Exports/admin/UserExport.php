<?php

namespace App\Exports\admin;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $users;

    public function __construct($users = null)
    {
        $this->users = $users ?? User::with(['department', 'position'])->get();
    }

    public function array(): array
    {
        $exportData = [];

        $exportData[] = ['ОТЧЕТ ПО СОТРУДНИКАМ'];
        $exportData[] = ['Дата формирования:', now()->format('d.m.Y H:i:s')];
        $exportData[] = [' '];
        $exportData[] = [' '];

        $exportData[] = ['СПИСОК СОТРУДНИКОВ'];
        $exportData[] = ['ID', 'Фамилия', 'Имя', 'Отчество', 'Email', 'Телефон', 'Отдел', 'Должность', 'Статус'];

        foreach ($this->users as $user) {
            $statusValue = $user->status->value ?? $user->status;
            $exportData[] = [
                $user->id,
                $user->surname,
                $user->name,
                $user->patronymic ?? '—',
                $user->email,
                $user->phone ?? '—',
                $user->position?->department?->name ?? '—',
                $user->position->name ?? '—',
                \App\Http\Enums\UserStatus::ruValues()[$statusValue] ?? $statusValue
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
        return 'Сотрудники';
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
