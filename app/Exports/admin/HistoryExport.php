<?php

namespace App\Exports\admin;

use App\Models\Equipment_history;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistoryExport implements FromArray, WithHeadings, WithTitle, WithStyles
{
    protected $operations;

    public function __construct($operations = null)
    {
        $this->operations = $operations ?? Equipment_history::with(['equipment', 'user', 'toUser', 'fromUser', 'toLocation', 'fromLocation'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function array(): array
    {
        $exportData = [];
        $actionTypes = \App\Http\Enums\TypeEquipmentHistory::ruValues();

        $exportData[] = ['ОТЧЕТ ПО ИСТОРИИ ОПЕРАЦИЙ'];
        $exportData[] = ['Дата формирования:', now()->format('d.m.Y H:i:s')];
        $exportData[] = [' '];
        $exportData[] = [' '];

        $exportData[] = ['СПИСОК ОПЕРАЦИЙ'];
        $exportData[] = [
            'Дата', 'Оборудование', 'Инв. номер', 'Операция',
            'Пользователь', 'Откуда', 'Куда', 'Статус', 'Комментарий'
        ];

        foreach ($this->operations as $operation) {
            // Откуда
            $from = '';
            if ($operation->fromUser) $from .= 'Сотрудник: ' . $operation->fromUser->name;
            if ($operation->fromLocation) $from .= ($from ? ', ' : '') . 'Локация: ' . $operation->fromLocation->name;
            if (!$from) $from = '—';

            // Куда
            $to = '';
            if ($operation->toUser) $to .= 'Сотрудник: ' . $operation->toUser->name;
            if ($operation->toLocation) $to .= ($to ? ', ' : '') . 'Локация: ' . $operation->toLocation->name;
            if (!$to) $to = '—';

            $statusText = $operation->new_status
                ? (\App\Http\Enums\StatusEquipment::ruValues()[$operation->new_status] ?? $operation->new_status)
                : '—';

            $exportData[] = [
                $operation->created_at->format('d.m.Y H:i'),
                $operation->equipment->name ?? '—',
                $operation->equipment->inventory_number ?? '—',
                $actionTypes[$operation->action_type] ?? $operation->action_type,
                $operation->user->name ?? 'Система',
                $from,
                $to,
                $statusText,
                $operation->comment ?? '—'
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
        return 'История операций';
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
