@extends('layouts.app')

@section('title', 'История операций')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/equipment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/tables.css') }}">
@endpush

@section('content')
    <div class="equipment-page">
        <div class="page-header">
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Назад на главную
                </a>
                <h1 class="page-title mt-2">История операций</h1>
                <p class="page-subtitle">Полная хронология всех действий с оборудованием</p>
            </div>
        </div>

        {{-- Фильтры --}}
        <div class="filters-bar">
            <form method="GET" action="{{ route('admin.history') }}" class="d-flex w-100 gap-3 justify-content-between">
                <div class="filters-group">
                    {{-- Кастомный селект вместо нативного --}}
                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="selected-text">
                                @if(request('action_type'))
                                    {{ $actionTypes[request('action_type')] ?? 'Все операции' }}
                                @else
                                    Все операции
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li><a class="dropdown-item {{ !request('action_type') ? 'active' : '' }}" href="#"
                                   data-value="">Все операции</a></li>
                            @foreach($actionTypes as $value => $label)
                                <li>
                                    <a class="dropdown-item {{ request('action_type') == $value ? 'active' : '' }}"
                                       href="#"
                                       data-value="{{ $value }}">
                                        {{ $label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="action_type" class="custom-select-input"
                               value="{{ request('action_type') }}">
                    </div>

                    {{-- Кнопки --}}
                    <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                        <i class="bi bi-funnel"></i> Применить
                    </button>
                    <a href="{{ route('admin.history') }}" class="btn-outline" style="padding: 10px 20px;">
                        <i class="bi bi-arrow-counterclockwise"></i> Сбросить
                    </a>
                </div>
            </form>
        </div>

        {{-- Таблица --}}
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Оборудование</th>
                        <th>Инв. номер</th>
                        <th>Операция</th>
                        <th>Пользователь</th>
                        <th>Откуда</th>
                        <th>Куда</th>
                        <th>Статус</th>
                        <th>Комментарий</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($operations as $operation)
                        <tr>
                            <td class="date">{{ $operation->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.equipment.show', $operation->equipment_id) }}"
                                   class="equipment-name">
                                    {{ $operation->equipment->name ?? '—' }}
                                </a>
                            </td>
                            <td class="inv-number">{{ $operation->equipment->inventory_number ?? '—' }}</td>
                            <td>
                                @php
                                    $operationClass = match($operation->action_type) {
                                        'assigned' => 'assign',
                                        'returned' => 'return',
                                        'repair' => 'repair',
                                        'written' => 'repair',
                                        'moved' => 'receive',
                                        default => 'receive'
                                    };
                                @endphp
                                <span class="operation {{ $operationClass }}">
                                    {{ $actionTypes[$operation->action_type] ?? $operation->action_type }}
                                </span>
                            </td>
                            <td>{{ $operation->user->name ?? 'Система' }}</td>
                            <td>
                                @if($operation->fromUser)
                                    <i class="bi bi-person"></i> {{ $operation->fromUser->name }}
                                @endif
                                @if($operation->fromLocation)
                                    <i class="bi bi-geo-alt"></i> {{ $operation->fromLocation->name }}
                                @endif
                                @if(!$operation->fromUser && !$operation->fromLocation)
                                    —
                                @endif
                            </td>
                            <td>
                                @if($operation->toUser)
                                    <i class="bi bi-person"></i> {{ $operation->toUser->name }}
                                @endif
                                @if($operation->toLocation)
                                    <i class="bi bi-geo-alt"></i> {{ $operation->toLocation->name }}
                                @endif
                                @if(!$operation->toUser && !$operation->toLocation)
                                    —
                                @endif
                            </td>
                            <td>
                                @if($operation->new_status)
                                    @php
                                        $statusClass = match($operation->new_status) {
                                            'in_use' => 'success',
                                            'in_stock' => 'neutral',
                                            'repair' => 'warning',
                                            'written' => 'danger',
                                            default => 'info'
                                        };
                                        $statusText = \App\Http\Enums\StatusEquipment::ruValues()[$operation->new_status] ?? $operation->new_status;
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <small class="text-secondary">{{ Str::limit($operation->comment, 30) }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-0 border-bottom-0">
                                <div class="empty-state">
                                    <div class="empty-icon-wrapper">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h4 class="empty-title">Здесь пока пусто</h4>
                                    <p class="empty-desc">История операций появится после первых действий с
                                        оборудованием.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Пагинация в правильной обёртке --}}
            @if($operations->hasPages())
                <div class="pagination-wrapper">
                    {{ $operations->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const customSelects = document.querySelectorAll('.custom-select');
            customSelects.forEach(select => {
                const btn = select.querySelector('.custom-select-btn');
                const items = select.querySelectorAll('.dropdown-item');
                const hiddenInput = select.querySelector('.custom-select-input');
                const selectedText = btn.querySelector('.selected-text');

                items.forEach(item => {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        const value = this.dataset.value;
                        const text = this.textContent;
                        selectedText.textContent = text;
                        if (hiddenInput) hiddenInput.value = value;
                        items.forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                    });
                });
            });
        });
    </script>
@endpush
