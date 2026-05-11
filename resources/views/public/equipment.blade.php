@extends('layouts.app')

@section('title', $equipment->name . ' - Информация об оборудовании')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/aggregator/admin/equipment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/aggregator/admin/equipment.css') }}">
@endpush

@section('content')
    <div class="equipment-page">

        <div class="page-header">
            <div>
                <h1 class="page-title mt-2">{{ $equipment->name }}</h1>
                <p class="page-subtitle">Инвентарный номер: {{ $equipment->inventory_number }}</p>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.equipment.show', $equipment->id) }}"
                       class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Вернуться к админ-панели
                    </a>
                @else
                    @if(request('from') === 'employee_equipment')
                        <a href="{{ route('employee.equipment') }}" class="text-secondary text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Назад к моему оборудованию
                        </a>
                    @else
                        <a href="{{ route('employee.dashboard') }}" class="text-secondary text-decoration-none">
                            <i class="bi bi-arrow-left"></i> На главную
                        </a>
                    @endif
                @endif
            </div>
        </div>

        <div class="row g-4">

            <div class="col-lg-4 col-md-5">
                <div class="equipment-card">
                    <div class="equipment-card-body text-center">
                        @if($equipment->qr_code)
                            <div class="qr-code-container mb-3">
                                <img src="{{ route('equipment.qrcode', $equipment->id) }}"
                                     alt="QR-код"
                                     class="qr-code-image">
                            </div>
                            <a href="{{ route('equipment.qrcode', $equipment->id) }}"
                               download="qr-{{ $equipment->inventory_number }}.png"
                               class="btn-outline w-100">
                                <i class="bi bi-download"></i> Скачать QR-код
                            </a>
                        @else
                            <div class="qr-placeholder">
                                <i class="bi bi-qr-code" style="font-size: 64px;"></i>
                                <p class="mt-2">QR-код не сгенерирован</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <div class="col-lg-8 col-md-7">
                <div class="equipment-card">
                    <div class="equipment-card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Инвентарный номер</span>
                                <span class="info-value fw-semibold">{{ $equipment->inventory_number }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Статус</span>
                                <span class="info-value">
        @php
            $statusClass = match($equipment->status) {
                'in_use' => 'success',
                'in_stock' => 'neutral',
                'repair' => 'warning',
                'written' => 'danger',
                default => 'neutral'
            };
        @endphp
        <span class="status-badge {{ $statusClass }}">
            {{ \App\Http\Enums\StatusEquipment::ruValues()[$equipment->status] ?? $equipment->status }}
        </span>
    </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Категория</span>
                                <span class="info-value">{{ $equipment->category->name ?? '—' }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Производитель</span>
                                <span class="info-value">{{ $equipment->manufacturer ?? '—' }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Модель</span>
                                <span class="info-value">{{ $equipment->model ?? '—' }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Серийный номер</span>
                                <span class="info-value font-monospace">{{ $equipment->serial_number ?? '—' }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Местоположение</span>
                                <span class="info-value">{{ $equipment->location->name ?? '—' }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Ответственный сотрудник</span>
                                <span class="info-value">
                                    @if($equipment->currentUser)
                                        {{ $equipment->currentUser->surname }} {{ $equipment->currentUser->name }}
                                        @if($equipment->currentUser->patronymic)
                                            {{ $equipment->currentUser->patronymic }}
                                        @endif
                                        @if(auth()->id() === $equipment->current_user_id)
                                            <span class="text-secondary">(это Вы)</span>
                                        @endif
                                    @else
                                        <span class="text-secondary">Не назначен</span>
                                    @endif
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Дата покупки</span>
                                <span class="info-value">{{ $equipment->purchase_date?->format('d.m.Y') ?? '—' }}</span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Стоимость</span>
                                <span class="info-value">
                                    {{ $equipment->purchase_price ? number_format($equipment->purchase_price, 0, ',', ' ') . ' ₽' : '—' }}
                                </span>
                            </div>

                            <div class="info-item">
                                <span class="info-label">Гарантия до</span>
                                <span class="info-value">
                                    @if($equipment->warranty_date)
                                        @if($equipment->warranty_date->isPast())
                                            <span class="text-danger">{{ $equipment->warranty_date->format('d.m.Y') }} (истекла)</span>
                                        @else
                                            {{ $equipment->warranty_date->format('d.m.Y') }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>

                            <div class="info-item info-item-full">
                                <span class="info-label">Примечание</span>
                                <span class="info-value">{{ $equipment->notes ?? '—' }}</span>
                            </div>

                            @if($equipment->status_comment)
                                <div class="info-item info-item-full">
                                    <span class="info-label">Комментарий к статусу</span>
                                    <span class="info-value text-warning">{{ $equipment->status_comment }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @if(auth()->user()->isAdmin() && $equipment->relationLoaded('history'))
            <div class="history-section">
                <div class="history-header">
                    <h3><i class="bi bi-clock-history"></i> История операций</h3>
                    <span class="history-desc">Хронология всех действий с оборудованием</span>
                </div>
                <div class="table-responsive">
                    <table class="history-table">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Действие</th>
                            <th>Пользователь</th>
                            <th>Детали</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($equipment->history->sortByDesc('created_at') as $record)
                            <tr>
                                <td class="history-date">{{ $record->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ \App\Http\Enums\TypeEquipmentHistory::ruValues()[$record->action_type] ?? $record->action_type }}</td>
                                <td>
                                    @if($record->user)
                                        {{ $record->user->name }}
                                    @else
                                        Система
                                    @endif
                                </td>
                                <td>
                                    @if($record->toUser)
                                        <span class="history-detail">→ {{ $record->toUser->name }}</span>
                                    @endif
                                    @if($record->toLocation)
                                        <span class="history-detail">→ {{ $record->toLocation->name }}</span>
                                    @endif
                                    @if($record->comment)
                                        <br><small class="text-secondary">{{ $record->comment }}</small>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="history-empty">
                                    <i class="bi bi-inbox"></i> История операций пуста
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
