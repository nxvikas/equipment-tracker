@extends('layouts.app')

@section('title', 'Моя панель')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">
@endpush

@section('content')
    <div class="employee-dashboard">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Моя панель</h1>
                <p class="dashboard-subtitle">
                    Добро пожаловать, {{ $user->surname }} {{ $user->name }}!
                </p>
            </div>
            <div class="dashboard-actions">
                <a href="{{ url()->current() }}" class="btn-icon" title="Обновить">
                    <i class="bi bi-arrow-repeat"></i>
                </a>
            </div>
        </div>

        {{-- СТАТИСТИКА ДЛЯ СОТРУДНИКА --}}
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-icon-wrapper green">
                    <i class="bi bi-laptop"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Моё оборудование</span>
                    <span class="stat-number accent">{{ $totalMyEquipment }}</span>
                    <span class="stat-change">единиц в использовании</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper orange">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">В ремонте</span>
                    <span class="stat-number">{{ $totalRepairEquipment }}</span>
                    <span class="stat-change warning">требуют внимания</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper blue">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Мой отдел</span>
                    <span class="stat-number" style="font-size: 18px;">{{ $user->department->name ?? 'Не назначен' }}</span>
                    <span class="stat-change">{{ $user->position->name ?? '' }}</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper blue">
                    <i class="bi bi-envelope"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Контактные данные</span>
                    <span class="stat-number" style="font-size: 14px;">{{ $user->email }}</span>
                    <span class="stat-change">{{ $user->phone ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- МОЁ ОБОРУДОВАНИЕ --}}
        <div class="section-wrapper">
            <div class="section-head">
                <div>
                    <h2 class="section-title">
                        <i class="bi bi-laptop me-2"></i>Моё оборудование
                    </h2>
                    <p class="section-desc">Техника, которая сейчас у вас в использовании</p>
                </div>
            </div>

            @if($myEquipment->count() > 0)
                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Инв. номер</th>
                                <th>Категория</th>
                                <th>Модель</th>
                                <th>Серийный номер</th>
                                <th>Статус</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($myEquipment as $item)
                                <tr>
                                    <td class="equipment-name">{{ $item->name }}</td>
                                    <td class="inv-number">{{ $item->inventory_number }}</td>
                                    <td>{{ $item->category->name ?? '—' }}</td>
                                    <td>{{ $item->model ?? '—' }}</td>
                                    <td class="serial-number">{{ $item->serial_number ?? '—' }}</td>
                                    <td>
                                        <span class="status-badge success">В использовании</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.equipment.show', $item->id) }}"
                                           class="action-btn"
                                           title="Посмотреть">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon-wrapper">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h4 class="empty-title">Нет оборудования</h4>
                    <p class="empty-desc">У вас пока нет выданного оборудования</p>
                </div>
            @endif
        </div>

        {{-- ОБОРУДОВАНИЕ В РЕМОНТЕ --}}
        @if($repairEquipment->count() > 0)
            <div class="section-wrapper">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">
                            <i class="bi bi-tools me-2"></i>Оборудование в ремонте
                        </h2>
                        <p class="section-desc">Техника, которая находится в сервисном центре</p>
                    </div>
                </div>

                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Инв. номер</th>
                                <th>Категория</th>
                                <th>Модель</th>
                                <th>Серийный номер</th>
                                <th>Статус</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($repairEquipment as $item)
                                <tr>
                                    <td class="equipment-name">{{ $item->name }}</td>
                                    <td class="inv-number">{{ $item->inventory_number }}</td>
                                    <td>{{ $item->category->name ?? '—' }}</td>
                                    <td>{{ $item->model ?? '—' }}</td>
                                    <td class="serial-number">{{ $item->serial_number ?? '—' }}</td>
                                    <td>
                                        <span class="status-badge warning">В ремонте</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.equipment.show', $item->id) }}"
                                           class="action-btn"
                                           title="Посмотреть">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- ИСТОРИЯ ОПЕРАЦИЙ --}}
        <div class="section-wrapper">
            <div class="section-head">
                <div>
                    <h2 class="section-title">
                        <i class="bi bi-clock-history me-2"></i>История операций
                    </h2>
                    <p class="section-desc">Действия с оборудованием, связанные с вами</p>
                </div>
            </div>

            @if($recentHistory->count() > 0)
                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="custom-table">
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Оборудование</th>
                                <th>Инв. номер</th>
                                <th>Действие</th>
                                <th>Комментарий</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($recentHistory as $record)
                                <tr>
                                    <td class="date">{{ $record->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="equipment-name">{{ $record->equipment->name ?? '—' }}</td>
                                    <td class="inv-number">{{ $record->equipment->inventory_number ?? '—' }}</td>
                                    <td>
                                        @php
                                            $operationText = \App\Http\Enums\TypeEquipmentHistory::ruValues()[$record->action_type] ?? $record->action_type;
                                            $operationClass = match($record->action_type) {
                                                'assigned' => 'assign',
                                                'returned' => 'return',
                                                'repaired' => 'repair',
                                                default => 'receive'
                                            };
                                        @endphp
                                        <span class="operation {{ $operationClass }}">{{ $operationText }}</span>
                                    </td>
                                    <td>
                                        <small class="text-secondary">{{ Str::limit($record->comment, 50) }}</small>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon-wrapper">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h4 class="empty-title">Нет операций</h4>
                    <p class="empty-desc">История операций с вашим участием пока пуста</p>
                </div>
            @endif
        </div>
    </div>
@endsection
