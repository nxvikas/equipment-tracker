@extends('layouts.app')

@section('title', 'Оборудование')

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

        <div class="page-header">
            <div>
                <h1 class="page-title">Оборудование</h1>
                <p class="page-subtitle">Управление всем оборудованием компании</p>
                @if(request('from')==='dashboard')
                    <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Назад на главную
                    </a>
                @endif
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.export.equipment') }}" class="btn-outline" title="Экспорт в Excel"
                   style="margin-right: 10px">
                    <i class="bi bi-download"></i> Экспорт
                </a>
                <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                    <i class="bi bi-plus-lg"></i> Добавить оборудование
                </button>
            </div>
        </div>

        <div class="filters-bar">
            <form method="GET" action="{{ route('admin.equipment') }}" id="filterForm"
                  class="d-flex w-100 gap-3 justify-content-between">

                <div class="search-input-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           id="searchEquipment"
                           class="search-input"
                           placeholder="Поиск по названию, инв. и сер. номеру...">
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
                                       href="#"
                                       data-value="{{ $category->id }}">
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
    @if(request('status'))
                            {{ \App\Http\Enums\StatusEquipment::ruValues()[request('status')] ?? request('status') }}
                        @else
                            Все статусы
                        @endif
</span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li><a class="dropdown-item {{ !request('status') ? 'active' : '' }}" href="#"
                                   data-value="">Все статусы</a></li>
                            @foreach(\App\Http\Enums\StatusEquipment::cases() as $status)
                                <li>
                                    <a class="dropdown-item {{ request('status') == $status->value ? 'active' : '' }}"
                                       href="#"
                                       data-value="{{ $status->value }}">
                                        {{ \App\Http\Enums\StatusEquipment::ruValues()[$status->value] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="status" class="custom-select-input" value="{{ request('status') }}">
                    </div>


                    <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                        <i class="bi bi-funnel"></i> Применить
                    </button>
                    <a href="{{ route('admin.equipment') }}" class="btn-outline" style="padding: 10px 20px;">
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
                        <th style="width: 60px; text-align: center;">QR</th>
                        <th>Инв. номер</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Серийный номер</th>
                        <th>Статус</th>
                        <th>Сотрудник</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($equipments as $equipment)
                        <tr>
                            <td style="text-align: center;">
                                @if($equipment->qr_code)
                                    <div class="bg-white d-inline-block rounded p-1 shadow-sm">
                                        <img src="{{ route('equipment.qrcode', $equipment->id) }}" alt="QR"
                                             style="width: 40px; height: 40px;">
                                    </div>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                            <td class="inv-number">{{ $equipment->inventory_number }}</td>
                            <td class="equipment-name">
                                <a href="{{ route('admin.equipment.show', $equipment->id) }}" class="equipment-name">
                                    {{ $equipment->name }}
                                </a>
                            </td>
                            <td>{{ $equipment->category->name ?? '—' }}</td>
                            <td class="serial-number">{{ $equipment->serial_number ?? '—' }}</td>
                            <td>
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
                            </td>
                            <td>{{ $equipment->currentUser ? $equipment->currentUser->surname . ' ' . $equipment->currentUser->name : '—' }}</td>
                            <td>
                                <button class="action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editEquipmentModal{{ $equipment->id }}"
                                        title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                @if(in_array($equipment->status, ['written', 'in_stock']))
                                    <button class="action-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteEquipmentModal{{ $equipment->id }}"
                                            title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
                                    <p class="empty-desc">Самое время добавить первую технику в базу.</p>
                                    <button class="btn-outline mt-3" data-bs-toggle="modal"
                                            data-bs-target="#addEquipmentModal">
                                        <i class="bi bi-plus-lg me-2"></i>Добавить оборудование
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($equipments->hasPages())
                <div class="pagination-wrapper">
                    {{ $equipments->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="addEquipmentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2" style="color: var(--accent);"></i>Добавление
                        оборудования</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.equipment.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Название <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control-custom @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Категория <span class="text-danger">*</span></label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addCategoryModal"
                                       style="font-size: 12px; color: var(--accent); text-decoration: none;">+ Добавить
                                        новую</a>
                                </div>
                                <select name="category_id"
                                        class="form-control-custom custom-dark-select @error('category_id') is-invalid @enderror">
                                    <option value="">Выберите категорию</option>
                                    @foreach($categories as $category)
                                        <option
                                            value="{{ $category->id }}" {{ (old('category_id') == $category->id || session('new_category_id') == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Производитель</label>
                                <input type="text"
                                       name="manufacturer"
                                       class="form-control-custom @error('manufacturer') is-invalid @enderror"
                                       value="{{ old('manufacturer') }}">
                                @error('manufacturer')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Модель</label>
                                <input type="text"
                                       name="model"
                                       class="form-control-custom @error('model') is-invalid @enderror"
                                       value="{{ old('model') }}">
                                @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Серийный номер</label>
                                <input type="text"
                                       name="serial_number"
                                       class="form-control-custom @error('serial_number') is-invalid @enderror"
                                       value="{{ old('serial_number') }}">
                                @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Инвентарный номер</label>
                                <input type="text"
                                       name="inventory_number"
                                       class="form-control-custom @error('inventory_number') is-invalid @enderror"
                                       placeholder="Автоматически"
                                       value="{{ old('inventory_number') }}">
                                <small class="form-hint">Оставьте пустым для автоматической генерации</small>
                                @error('inventory_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Дата покупки</label>
                                <input type="date"
                                       name="purchase_date"
                                       class="form-control-custom custom-dark-select @error('purchase_date') is-invalid @enderror"
                                       value="{{ old('purchase_date') }}">
                                @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Стоимость (₽)</label>
                                <input type="number"
                                       step="0.01"
                                       name="purchase_price"
                                       class="form-control-custom @error('purchase_price') is-invalid @enderror"
                                       value="{{ old('purchase_price') }}">
                                @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Гарантия до</label>
                                <input type="date"
                                       name="warranty_date"
                                       class="form-control-custom custom-dark-select @error('warranty_date') is-invalid @enderror"
                                       value="{{ old('warranty_date') }}">
                                @error('warranty_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Статус <span class="text-danger">*</span></label>
                                <select name="status" id="equipmentStatus"
                                        class="form-control-custom custom-dark-select @error('status') is-invalid @enderror">
                                    <option value="">Выберите статус</option>
                                    @foreach(\App\Http\Enums\StatusEquipment::cases() as $status)
                                        @if($status->value !== 'written')
                                            <option
                                                value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
                                                {{ \App\Http\Enums\StatusEquipment::ruValues()[$status->value] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Тип локации <span class="text-danger">*</span></label>
                                <select id="locationTypeSelect" class="form-control-custom custom-dark-select">
                                    <option value="">Выберите тип</option>
                                    @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                                        <option value="{{ $type->value }}" data-type-value="{{ $type->value }}">
                                            {{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row justify-content-end">
                            <div class="col-md-6 mb-3 user-field" id="userField" style="display: none;">
                                <label class="form-label" id="userLabel">Сотрудник <span
                                        class="text-danger">*</span></label>
                                <select name="current_user_id"
                                        class="form-control-custom custom-dark-select @error('current_user_id') is-invalid @enderror">
                                    <option value="">Выберите сотрудника</option>
                                    @foreach($users as $user)
                                        <option
                                            value="{{ $user->id }}" {{ old('current_user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('current_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Выбор локации <span
                                            class="text-danger">*</span></label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addLocationModal"
                                       style="font-size: 12px; color: var(--accent); text-decoration: none;">+ Добавить
                                        новую</a>
                                </div>
                                <select name="location_id" id="locationSelect"
                                        class="form-control-custom custom-dark-select @error('location_id') is-invalid @enderror">
                                    <option value="">Выберите локацию</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}"
                                                data-type="{{ $location->type }}" {{ (old('location_id') == $location->id || session('new_location_id') == $location->id) ? 'selected' : '' }}>
                                            {{ $location->name }}
                                            ({{ \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? $location->type }}
                                            )
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Примечание</label>
                            <textarea name="notes" class="form-control-custom @error('notes') is-invalid @enderror"
                                      rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary">Добавить оборудование</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    @foreach($equipments as $equipment)
        <div class="modal fade" id="editEquipmentModal{{ $equipment->id }}" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2" style="color: var(--accent);"></i>
                            Редактирование оборудования
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.equipment.update', $equipment->id) }}" method="POST"
                          class="edit-equipment-form" data-equipment-id="{{ $equipment->id }}">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Название <span class="text-danger">*</span></label>
                                    <input type="text" name="name"
                                           class="form-control-custom @error('name') is-invalid @enderror"
                                           value="{{ old('name', $equipment->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-end mb-2">
                                        <label class="form-label mb-0">Категория <span
                                                class="text-danger">*</span></label>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#addCategoryModal"
                                           class="add-category-from-edit"
                                           style="font-size: 12px; color: var(--accent); text-decoration: none;">+
                                            Добавить новую</a>
                                    </div>
                                    <select name="category_id"
                                            class="form-control-custom custom-dark-select @error('category_id') is-invalid @enderror">
                                        <option value="">Выберите категорию</option>
                                        @foreach($categories as $category)
                                            <option
                                                value="{{ $category->id }}" {{ old('category_id', $equipment->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Производитель</label>
                                    <input type="text" name="manufacturer" class="form-control-custom"
                                           value="{{ old('manufacturer', $equipment->manufacturer) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Модель</label>
                                    <input type="text" name="model" class="form-control-custom"
                                           value="{{ old('model', $equipment->model) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Серийный номер</label>
                                    <input type="text" name="serial_number" class="form-control-custom"
                                           value="{{ old('serial_number', $equipment->serial_number) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Инвентарный номер</label>
                                    <input type="text" name="inventory_number" class="form-control-custom"
                                           value="{{ old('inventory_number', $equipment->inventory_number) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Дата покупки</label>
                                    <input type="date" name="purchase_date"
                                           class="form-control-custom custom-dark-select"
                                           value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Стоимость (₽)</label>
                                    <input type="number" step="0.01" name="purchase_price" class="form-control-custom"
                                           value="{{ old('purchase_price', $equipment->purchase_price) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Гарантия до</label>
                                    <input type="date" name="warranty_date"
                                           class="form-control-custom custom-dark-select"
                                           value="{{ old('warranty_date', $equipment->warranty_date?->format('Y-m-d')) }}">
                                </div>
                            </div>

                            <!-- СТАТУС И ТИП ЛОКАЦИИ -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Статус <span class="text-danger">*</span></label>
                                    <select name="status"
                                            class="form-control-custom custom-dark-select edit-status-select"
                                            data-id="{{ $equipment->id }}">
                                        @foreach(\App\Http\Enums\StatusEquipment::cases() as $status)
                                            @if($status->value !== 'written')
                                                <option
                                                    value="{{ $status->value }}" {{ old('status', $equipment->status) == $status->value ? 'selected' : '' }}>
                                                    {{ \App\Http\Enums\StatusEquipment::ruValues()[$status->value] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Тип локации <span class="text-danger">*</span></label>
                                    <select id="editLocationTypeSelect{{ $equipment->id }}"
                                            class="form-control-custom custom-dark-select edit-type-select"
                                            data-id="{{ $equipment->id }}">
                                        <option value="">Выберите тип</option>
                                        @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                                            <option value="{{ $type->value }}" data-type-value="{{ $type->value }}"
                                                {{ $type->value === $equipment->location->type ? 'selected' : '' }}>
                                                {{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback edit-type-error"
                                         id="editTypeError{{ $equipment->id }}"></div>
                                </div>
                            </div>

                            <!-- СОТРУДНИК И ВЫБОР ЛОКАЦИИ -->
                            <div class="row justify-content-end">
                                <div class="col-md-6 mb-3 edit-user-field" id="editUserField{{ $equipment->id }}"
                                     style="display: {{ $equipment->status === 'in_use' ? 'block' : 'none' }};">
                                    <label class="form-label edit-user-label" id="editUserLabel{{ $equipment->id }}">Сотрудник {!! $equipment->status === 'in_use' ? '<span class="text-danger">*</span>' : '' !!}</label>
                                    <select name="current_user_id" class="form-control-custom custom-dark-select">
                                        <option value="">Не назначен</option>
                                        @foreach($users as $user)
                                            <option
                                                value="{{ $user->id }}" {{ old('current_user_id', $equipment->current_user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-end mb-2">
                                        <label class="form-label mb-0">Выбор локации <span class="text-danger">*</span></label>
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#addLocationModal"
                                           class="add-location-from-edit"
                                           style="font-size: 12px; color: var(--accent); text-decoration: none;">+
                                            Добавить новую</a>
                                    </div>
                                    <select name="location_id" id="editLocationSelect{{ $equipment->id }}"
                                            class="form-control-custom custom-dark-select edit-location-select"
                                            data-id="{{ $equipment->id }}">
                                        <option value="">Выберите локацию</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" data-type="{{ $location->type }}"
                                                {{ old('location_id', $equipment->location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                                ({{ \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? $location->type }}
                                                )
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Примечание</label>
                                <textarea name="notes" class="form-control-custom"
                                          rows="2">{{ old('notes', $equipment->notes) }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <button type="submit" class="btn-primary">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        @if(in_array($equipment->status, ['written', 'in_stock']))
            <div class="modal fade" id="deleteEquipmentModal{{ $equipment->id }}" tabindex="-1"
                 data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title text-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Вы уверены, что хотите удалить оборудование?</p>
                            <div class="delete-equipment-info">
                                <div class="info-row">
                                    <span class="info-label">Название:</span>
                                    <span class="info-value">{{ $equipment->name }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Инв. номер:</span>
                                    <span class="info-value">{{ $equipment->inventory_number }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Категория:</span>
                                    <span class="info-value">{{ $equipment->category->name ?? '—' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Статус:</span>
                                    <span class="info-value">
                                @php
                                    $statusText = match($equipment->status) {
                                        'in_stock' => 'На складе',
                                        'written' => 'Списан',
                                        default => $equipment->status
                                    };
                                @endphp
                                        {{ $statusText }}
                            </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <form action="{{ route('admin.equipment.destroy', $equipment->id) }}" method="POST"
                                  class="delete-equipment-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-primary"
                                        style="background: var(--danger); color: white;">
                                    <i class="bi bi-trash"></i> Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    <div class="modal fade" id="addCategoryModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Новая категория</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.category.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_to" value="equipment">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название категории <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control-custom @error('name', 'categoryModal') is-invalid @enderror"
                                   placeholder="Например: Мониторы"
                                   value="{{ old('name') }}">
                            @error('name', 'categoryModal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="description"
                                      class="form-control-custom @error('description', 'categoryModal') is-invalid @enderror"
                                      placeholder="Дополнительная информация о категории"
                                      rows="2">{{ old('description') }}</textarea>
                            @error('description', 'categoryModal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Назад</button>
                        <button type="submit" class="btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addLocationModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Новая локация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.locations.store') }}" method="POST" id="addLocationForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom" placeholder="Например: Склад №5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Тип <span class="text-danger">*</span></label>
                            <select name="type" class="form-control-custom custom-dark-select">
                                <option value="">Выберите тип</option>
                                @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                                    <option value="{{ $type->value }}">
                                        {{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Адрес</label>
                            <textarea name="address" class="form-control-custom" rows="2"
                                      placeholder="Физический адрес (необязательно)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Назад</button>
                        <button type="submit" class="btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const initLiveSearch = () => {
            const searchInput = document.getElementById('searchEquipment');
            if (!searchInput) return;

            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.custom-table tbody tr').forEach(row => {
                    const text = [
                        row.querySelector('.inv-number')?.textContent || '',
                        row.querySelector('.equipment-name')?.textContent || '',
                        row.querySelector('.serial-number')?.textContent || ''
                    ].join(' ').toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        };


        let globalLocationsData = @json($locationsForJs);
        let globalCategoriesData = @json($categoriesForJs);


        const refreshAllEditCategorySelects = () => {
            document.querySelectorAll('.edit-equipment-form select[name="category_id"]').forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="">Выберите категорию</option>';

                globalCategoriesData.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    select.appendChild(option);
                });

                if (currentValue && select.querySelector(`option[value="${currentValue}"]`)) {
                    select.value = currentValue;
                }
            });
        };


        const refreshAllEditLocationSelects = () => {
            document.querySelectorAll('.edit-equipment-form').forEach(form => {
                const typeSelect = form.querySelector('.edit-type-select');
                const locationSelect = form.querySelector('.edit-location-select');

                if (!typeSelect || !locationSelect) return;

                const currentTypeValue = typeSelect.value;
                const currentLocationValue = locationSelect.value;

                locationSelect.innerHTML = '<option value="">Выберите локацию</option>';

                if (!currentTypeValue) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Сначала выберите тип локации';
                    option.disabled = true;
                    locationSelect.appendChild(option);
                    return;
                }

                globalLocationsData.forEach(loc => {
                    if (loc.type === currentTypeValue) {
                        const option = document.createElement('option');
                        option.value = loc.id;
                        option.textContent = loc.name + ' (' + loc.typeLabel + ')';
                        option.dataset.type = loc.type;
                        locationSelect.appendChild(option);
                    }
                });

                if (currentLocationValue && locationSelect.querySelector(`option[value="${currentLocationValue}"]`)) {
                    locationSelect.value = currentLocationValue;
                }
            });
        };


        const refreshAddCategorySelect = () => {
            const categorySelect = document.querySelector('#addEquipmentModal select[name="category_id"]');
            if (!categorySelect) return;
            const currentValue = categorySelect.value;
            categorySelect.innerHTML = '<option value="">Выберите категорию</option>';

            globalCategoriesData.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                categorySelect.appendChild(option);
            });

            if (currentValue && categorySelect.querySelector(`option[value="${currentValue}"]`)) {
                categorySelect.value = currentValue;
            }
        };

        const refreshAddLocationSelect = () => {
            const locationSelect = document.getElementById('locationSelect');
            if (!locationSelect) return;
            const currentValue = locationSelect.value;
            locationSelect.innerHTML = '<option value="">Выберите локацию</option>';

            globalLocationsData.forEach(loc => {
                const option = document.createElement('option');
                option.value = loc.id;
                option.textContent = loc.name + ' (' + loc.typeLabel + ')';
                option.dataset.type = loc.type;
                locationSelect.appendChild(option);
            });

            if (currentValue && locationSelect.querySelector(`option[value="${currentValue}"]`)) {
                locationSelect.value = currentValue;
            }
        };

        const initEquipmentFormDependency = () => {
            const statusSelect = document.getElementById('equipmentStatus');
            const typeSelect = document.getElementById('locationTypeSelect');
            const locationSelect = document.getElementById('locationSelect');
            const userField = document.getElementById('userField');
            const userLabel = document.getElementById('userLabel');

            if (!statusSelect) return;

            const allLocations = globalLocationsData.map(loc => ({
                value: loc.id,
                text: loc.name + ' (' + loc.typeLabel + ')',
                type: loc.type
            }));

            const filterLocations = () => {
                const selectedType = typeSelect.value;
                locationSelect.innerHTML = '<option value="">Выберите локацию</option>';

                if (!selectedType) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Сначала выберите тип локации';
                    option.disabled = true;
                    locationSelect.appendChild(option);
                    return;
                }

                let hasOptions = false;
                allLocations.forEach(loc => {
                    if (loc.type === selectedType) {
                        const option = document.createElement('option');
                        option.value = loc.value;
                        option.textContent = loc.text;
                        option.dataset.type = loc.type;
                        locationSelect.appendChild(option);
                        hasOptions = true;
                    }
                });

                if (!hasOptions) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Нет доступных локаций';
                    option.disabled = true;
                    locationSelect.appendChild(option);
                }
            };

            const updateForm = () => {
                const status = statusSelect.value;

                if (status === 'in_use') {
                    userField.style.display = 'block';
                    if (userLabel) userLabel.innerHTML = 'Сотрудник <span class="text-danger">*</span>';
                } else {
                    userField.style.display = 'none';
                    if (userLabel) userLabel.innerHTML = 'Сотрудник';
                    const userSelect = userField.querySelector('select');
                    if (userSelect) userSelect.value = '';
                }

                if (status === '') {
                    typeSelect.disabled = true;
                    locationSelect.disabled = true;
                    typeSelect.value = '';
                    locationSelect.innerHTML = '<option value="">Сначала выберите статус</option>';
                    return;
                }

                typeSelect.disabled = false;
                locationSelect.disabled = false;

                let allowedTypes = [];
                let defaultType = '';

                if (status === 'in_stock') {
                    allowedTypes = ['warehouse'];
                    defaultType = 'warehouse';
                    typeSelect.disabled = true;
                } else if (status === 'repair') {
                    allowedTypes = ['service'];
                    defaultType = 'service';
                    typeSelect.disabled = true;
                } else if (status === 'in_use') {
                    allowedTypes = ['office', 'remote'];
                    defaultType = '';
                    typeSelect.disabled = false;
                }

                typeSelect.innerHTML = '<option value="">Выберите тип</option>';
                @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                if (allowedTypes.length === 0 || allowedTypes.includes('{{ $type->value }}')) {
                    const option = document.createElement('option');
                    option.value = '{{ $type->value }}';
                    option.textContent = '{{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}';
                    typeSelect.appendChild(option);
                }
                @endforeach

                if (defaultType) {
                    typeSelect.value = defaultType;
                }
                filterLocations();
            };

            statusSelect.addEventListener('change', updateForm);
            typeSelect.addEventListener('change', filterLocations);
            updateForm();
        };

        const initEditFormsDependency = () => {
            document.querySelectorAll('.edit-equipment-form').forEach(form => {
                const statusSelect = form.querySelector('.edit-status-select');
                const typeSelect = form.querySelector('.edit-type-select');
                const locationSelect = form.querySelector('.edit-location-select');
                const userField = form.querySelector('.edit-user-field');
                const userLabel = form.querySelector('.edit-user-label');

                if (!statusSelect || !typeSelect || !locationSelect) return;


                const getCurrentLocations = () => {
                    return globalLocationsData.map(loc => ({
                        value: loc.id,
                        text: loc.name + ' (' + loc.typeLabel + ')',
                        type: loc.type
                    }));
                };

                const filterLocations = () => {
                    const selectedType = typeSelect.value;
                    const currentValue = locationSelect.value;


                    const allLocations = getCurrentLocations();

                    locationSelect.innerHTML = '<option value="">Выберите локацию</option>';

                    if (!selectedType) {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Сначала выберите тип локации';
                        option.disabled = true;
                        locationSelect.appendChild(option);
                        return;
                    }

                    allLocations.forEach(loc => {
                        if (loc.type === selectedType) {
                            const option = document.createElement('option');
                            option.value = loc.value;
                            option.textContent = loc.text;
                            option.dataset.type = loc.type;
                            locationSelect.appendChild(option);
                        }
                    });


                    if (currentValue && locationSelect.querySelector(`option[value="${currentValue}"]`)) {
                        locationSelect.value = currentValue;
                    } else {
                        locationSelect.value = '';
                    }
                };

                const updateForm = () => {
                    const status = statusSelect.value;


                    if (status === 'in_use') {
                        if (userField) userField.style.display = 'block';
                        if (userLabel) userLabel.innerHTML = 'Сотрудник <span class="text-danger">*</span>';
                    } else {
                        if (userField) userField.style.display = 'none';
                        if (userLabel) userLabel.innerHTML = 'Сотрудник';
                        const userSelect = userField?.querySelector('select');
                        if (userSelect) userSelect.value = '';
                    }

                    let allowedTypes = [];
                    let newTypeValue = typeSelect.value;
                    let shouldDisableTypeSelect = false;

                    if (status === 'in_stock') {
                        allowedTypes = ['warehouse'];
                        newTypeValue = 'warehouse';
                        shouldDisableTypeSelect = true;
                    } else if (status === 'repair') {
                        allowedTypes = ['service'];
                        newTypeValue = 'service';
                        shouldDisableTypeSelect = true;
                    } else if (status === 'in_use') {
                        allowedTypes = ['office', 'remote'];
                        shouldDisableTypeSelect = false;

                        if (typeSelect.value && !allowedTypes.includes(typeSelect.value)) {
                            newTypeValue = '';
                        }
                    } else {

                        allowedTypes = [];
                        shouldDisableTypeSelect = false;
                    }


                    typeSelect.disabled = shouldDisableTypeSelect;


                    const allTypeOptions = Array.from(typeSelect.options).filter(opt => opt.value !== '');


                    allTypeOptions.forEach(opt => {
                        if (allowedTypes.length === 0 || allowedTypes.includes(opt.value)) {
                            opt.style.display = '';
                        } else {
                            opt.style.display = 'none';
                        }
                    });


                    if (newTypeValue && typeSelect.querySelector(`option[value="${newTypeValue}"]`)) {
                        typeSelect.value = newTypeValue;
                    } else if (!newTypeValue) {
                        typeSelect.value = '';
                    }


                    setTimeout(() => {
                        filterLocations();
                    }, 0);
                };


                const forceUpdateOnModalOpen = () => {
                    const modal = form.closest('.modal');
                    if (modal) {
                        modal.addEventListener('shown.bs.modal', () => {
                            updateForm();
                        });
                    }
                };

                statusSelect.addEventListener('change', updateForm);
                typeSelect.addEventListener('change', filterLocations);


                updateForm();
                forceUpdateOnModalOpen();
            });
        };

        document.addEventListener('DOMContentLoaded', () => {
            @if($errors->any() && !$errors->hasBag('categoryModal') && !$errors->hasBag('locationModal'))
            new bootstrap.Modal(document.getElementById('addEquipmentModal')).show();
            @endif

            @if(session('reopen_equipment_modal'))
            new bootstrap.Modal(document.getElementById('addEquipmentModal')).show();
            @endif

            initCustomSelects();
            initLiveSearch();
            initEquipmentFormDependency();
            initEditFormsDependency();

            const equipmentForm = document.querySelector('#addEquipmentModal form');
            if (equipmentForm) {
                equipmentForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    submitAjaxForm(equipmentForm, 'addEquipmentModal', {reloadOnSuccess: true});
                });
            }


            const categoryForm = document.querySelector('#addCategoryModal form');
            if (categoryForm) {
                categoryForm.addEventListener('submit', (e) => {
                    e.preventDefault();

                    const formData = new FormData(categoryForm);
                    fetch(categoryForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                                if (modal) modal.hide();

                                globalCategoriesData.push({
                                    id: data.item.id,
                                    name: data.item.name
                                });

                                refreshAddCategorySelect();
                                refreshAllEditCategorySelects();

                                const categorySelect = document.querySelector('#addEquipmentModal select[name="category_id"]');
                                if (categorySelect) {
                                    categorySelect.value = data.item.id;
                                }

                                window.showToast(data.message || 'Категория добавлена', 'success');
                                categoryForm.reset();
                                categoryForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                            } else if (data.errors) {
                                window.showFormErrors(categoryForm, data.errors);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.showToast('Ошибка соединения с сервером', 'danger');
                        });
                });
            }


            const locationForm = document.getElementById('addLocationForm');
            if (locationForm) {
                locationForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(locationForm);
                    fetch(locationForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addLocationModal'));
                                if (modal) modal.hide();

                                const typeLabelMap = {
                                    'office': 'Офис',
                                    'warehouse': 'Склад',
                                    'service': 'Сервис',
                                    'remote': 'Удаленно'
                                };

                                globalLocationsData.push({
                                    id: data.item.id,
                                    name: data.item.name,
                                    type: data.item.type,
                                    typeLabel: typeLabelMap[data.item.type] || data.item.type
                                });

                                refreshAddLocationSelect();
                                refreshAllEditLocationSelects();

                                const typeSelect = document.getElementById('locationTypeSelect');
                                if (typeSelect) {
                                    typeSelect.dispatchEvent(new Event('change'));
                                }

                                const locationSelect = document.getElementById('locationSelect');
                                if (locationSelect) {
                                    locationSelect.value = data.item.id;
                                }

                                window.showToast(data.message || 'Локация добавлена', 'success');
                                locationForm.reset();
                                locationForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                            } else if (data.errors) {
                                window.showFormErrors(locationForm, data.errors);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.showToast('Ошибка соединения с сервером', 'danger');
                        });
                });
            }
        });

        document.querySelectorAll('.edit-equipment-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(form, form.closest('.modal').id, {reloadOnSuccess: true});
            });
        });

        document.querySelectorAll('.delete-equipment-form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(form, form.closest('.modal').id, {reloadOnSuccess: true});
            });
        });
    </script>
@endpush
