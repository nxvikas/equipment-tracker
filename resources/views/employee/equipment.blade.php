@extends('layouts.app')

@section('title', 'Моё оборудование')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/equipment.css') }}">
@endpush

@section('content')
    <div class="equipment-page">

        @if(session('success'))
            <div class="alert custom-alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert custom-alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="page-header">
            <div>
                <h1 class="page-title">Моё оборудование</h1>
                <p class="page-subtitle">Техника, закреплённая за вами</p>
                @if(request('from')==='dashboard')
                    <a href="{{ route('employee.dashboard') }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Назад на главную
                    </a>
                @endif
            </div>
            <div class="page-actions">
                <a href="{{ route('employee.export.equipment') }}" class="btn-outline" title="Экспорт в Excel">
                    <i class="bi bi-download"></i> Экспорт
                </a>
            </div>
        </div>


        <div class="filters-bar">
            <form method="GET" action="{{ route('employee.equipment') }}" id="filterForm"
                  class="d-flex w-100 gap-3 justify-content-between">

                <div class="search-input-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           name="search"
                           id="searchEquipment"
                           class="search-input"
                           placeholder="Поиск по названию, инв. номеру..."
                           value="{{ request('search') }}">
                </div>

                <div class="filters-group">
                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="selected-text">
                                @if(request('category_id'))
                                    {{ $categories->find(request('category_id'))->name ?? 'Все категории' }}
                                @else
                                    Все категории
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li><a class="dropdown-item" href="#" data-value="">Все категории</a></li>
                            @foreach($categories as $category)
                                <li>
                                    <a class="dropdown-item {{ request('category_id') == $category->id ? 'active' : '' }}"
                                       href="#" data-value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="category_id" class="custom-select-input"
                               value="{{ request('category_id') }}">
                    </div>

                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="selected-text">
                                @if(request('status') && isset($statuses[request('status')]))
                                    {{ $statuses[request('status')] }}
                                @else
                                    Все статусы
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li><a class="dropdown-item {{ !request('status') ? 'active' : '' }}" href="#"
                                   data-value="">Все статусы</a></li>
                            @foreach($statuses as $value => $label)
                                <li>
                                    <a class="dropdown-item {{ request('status') == $value ? 'active' : '' }}" href="#"
                                       data-value="{{ $value }}">
                                        {{ $label }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="status" class="custom-select-input" value="{{ request('status') }}">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                        <i class="bi bi-funnel"></i> Применить
                    </button>
                    <a href="{{ route('employee.equipment') }}" class="btn-outline" style="padding: 10px 20px;">
                        <i class="bi bi-arrow-counterclockwise"></i> Сбросить
                    </a>
                </div>
            </form>
        </div>


        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                    <tr>
                        <th>QR</th>
                        <th>Инв. номер</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Модель</th>
                        <th>Серийный номер</th>
                        <th>Статус</th>
                        <th>Локация</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($equipments as $item)
                        <tr>
                            <td style="text-align: center;">
                                @if($item->qr_code)
                                    <div class="bg-white d-inline-block rounded p-1 shadow-sm">
                                        <img src="{{ route('equipment.qrcode', $item->id) }}" alt="QR"
                                             style="width: 40px; height: 40px;">
                                    </div>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                            <td class="inv-number">{{ $item->inventory_number }}</td>
                            <td class="equipment-name">
                                <a href="{{ route('public.equipment', ['id' => $item->id, 'from' => 'employee_equipment']) }}" class="equipment-name">
                                    {{ $item->name }}
                                </a>
                            </td>
                            <td>{{ $item->category->name ?? '—' }}</td>
                            <td>{{ $item->model ?? '—' }}</td>
                            <td class="serial-number">{{ $item->serial_number ?? '—' }}</td>
                            <td>
                                @php
                                    $statusClass = match($item->status) {
                                        'in_use' => 'success',
                                        'repair' => 'warning',
                                        default => 'neutral'
                                    };
                                    $statusText = match($item->status) {
                                        'in_use' => 'В использовании',
                                        'repair' => 'В ремонте',
                                        default => $item->status
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>{{ $item->location->name ?? '—' }}</td>
                            <td>
                                @if($item->status === 'in_use')
                                    <button type="button"
                                            class="return-btn"
                                            data-equipment-id="{{ $item->id }}"
                                            data-equipment-name="{{ $item->name }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#returnConfirmModal"
                                            title="Вернуть на склад">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </button>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-0 border-bottom-0">
                                <div class="empty-state">
                                    <div class="empty-icon-wrapper">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h4 class="empty-title">Здесь пока пусто</h4>
                                    <p class="empty-desc">У вас нет закреплённого оборудования</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($equipments->hasPages())
                <div class="pagination-wrapper">
                    {{ $equipments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="returnConfirmModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-warning">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Подтверждение возврата
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-question-circle" style="font-size: 48px; color: var(--warning);"></i>
                    <p class="mt-3 mb-0">Вы уверены, что хотите вернуть оборудование на склад?</p>
                    <p class="text-secondary mt-2" id="returnEquipmentName"></p>
                    <p class="text-warning small mt-3">
                        <i class="bi bi-exclamation-circle"></i> После возврата оборудование будет доступно для выдачи другим сотрудникам.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                    <form action="" method="POST" id="returnForm">
                        @csrf
                        <button type="submit" class="btn-primary" style="background: var(--warning); color: #02040a;">
                            <i class="bi bi-box-arrow-in-right"></i> Подтвердить возврат
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const initLiveSearch = () => {
            const searchInput = document.getElementById('searchEquipment');
            if (!searchInput) return;


        };

        document.addEventListener('DOMContentLoaded', () => {
            initCustomSelects();
        });

        document.addEventListener('DOMContentLoaded', function() {

            const returnButtons = document.querySelectorAll('.return-btn');
            const returnForm = document.getElementById('returnForm');
            const returnEquipmentName = document.getElementById('returnEquipmentName');

            returnButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const equipmentId = this.dataset.equipmentId;
                    const equipmentName = this.dataset.equipmentName;

                    returnEquipmentName.innerHTML = `<strong>${equipmentName}</strong>`;
                    returnForm.action = `/employee/equipment/${equipmentId}/return`;
                });
            });
        });
    </script>
@endpush
