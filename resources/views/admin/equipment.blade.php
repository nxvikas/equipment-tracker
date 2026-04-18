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
            </div>
            <div class="page-actions">
                <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                    <i class="bi bi-plus-lg"></i> Добавить оборудование
                </button>
            </div>
        </div>

        <div class="filters-bar">
            <div class="search-input-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" id="searchEquipment" class="search-input" placeholder="Поиск по названию, инв. номеру...">
            </div>
            <div class="filters-group">
                <select id="filterCategory" class="filter-select custom-dark-select">
                    <option value="">Все категории</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <select id="filterStatus" class="filter-select custom-dark-select">
                    <option value="">Все статусы</option>
                    <option value="in_stock">На складе</option>
                    <option value="in_use">В работе</option>
                    <option value="repair">В ремонте</option>
                    <option value="written_off">Списано</option>
                </select>
            </div>
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
                    @forelse($equipments as $item)
                        <tr>
                            <td style="text-align: center;">
                                @if($item->qr_code)
                                    <div class="bg-white d-inline-block rounded p-1 shadow-sm">
                                        <img src="{{ route('admin.equipment.qrcode', $item->id) }}" alt="QR" style="width: 40px; height: 40px;">
                                    </div>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                            <td class="inv-number">{{ $item->inventory_number }}</td>
                            <td class="equipment-name">{{ $item->name }}</td>
                            <td>{{ $item->category->name ?? '—' }}</td>
                            <td class="serial-number">{{ $item->serial_number }}</td>
                            <td>
                                @php
                                    $statusClass = match($item->status) {
                                        'in_use' => 'success',
                                        'in_stock' => 'neutral',
                                        'repair' => 'warning',
                                        'written_off' => 'danger',
                                        default => 'neutral'
                                    };
                                    $statusText = match($item->status) {
                                        'in_use' => 'В работе',
                                        'in_stock' => 'На складе',
                                        'repair' => 'В ремонте',
                                        'written_off' => 'Списано',
                                        default => $item->status
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>{{ $item->currentUser?->name ?? '—' }}</td>
                            <td>
                                <button class="action-btn" onclick="viewEquipment({{ $item->id }})" title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn" onclick="editEquipment({{ $item->id }})" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($item->status === 'in_stock')
                                    <button class="action-btn assign" onclick="assignEquipment({{ $item->id }})" title="Выдать сотруднику">
                                        <i class="bi bi-person-check"></i>
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
                                    <button class="btn-outline mt-3" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
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
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2" style="color: var(--accent);"></i>Добавление оборудования</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.equipment.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @if ($errors->any() && !$errors->hasBag('categoryModal') && !$errors->hasBag('locationModal'))
                            <div class="alert custom-alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Название <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control-custom @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Категория <span class="text-danger">*</span></label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="font-size: 12px; color: var(--accent); text-decoration: none;">+ Добавить новую</a>
                                </div>
                                <select name="category_id" class="form-control-custom custom-dark-select">
                                    <option value="">Выберите категорию</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id') == $category->id || session('new_category_id') == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Производитель</label>
                                <input type="text" name="manufacturer" class="form-control-custom" value="{{ old('manufacturer') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Модель</label>
                                <input type="text" name="model" class="form-control-custom" value="{{ old('model') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Серийный номер</label>
                                <input type="text" name="serial_number" class="form-control-custom" value="{{ old('serial_number') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Инвентарный номер</label>
                                <input type="text" name="inventory_number" class="form-control-custom" placeholder="Автоматически" value="{{ old('inventory_number') }}">
                                <small class="form-hint">Оставьте пустым для автоматической генерации</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Дата покупки</label>
                                <input type="date" name="purchase_date" class="form-control-custom custom-dark-select" value="{{ old('purchase_date') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Стоимость (₽)</label>
                                <input type="number" step="0.01" name="purchase_price" class="form-control-custom" value="{{ old('purchase_price') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Гарантия до</label>
                                <input type="date" name="warranty_date" class="form-control-custom custom-dark-select" value="{{ old('warranty_date') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Статус <span class="text-danger">*</span></label>
                                <select name="status" class="form-control-custom custom-dark-select">
                                    <option value="">Выберите статус</option>
                                    <option value="in_stock" {{ old('status') == 'in_stock' ? 'selected' : '' }}>На складе</option>
                                    <option value="in_use" {{ old('status') == 'in_use' ? 'selected' : '' }}>В работе</option>
                                    <option value="repair" {{ old('status') == 'repair' ? 'selected' : '' }}>В ремонте</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Местоположение <span class="text-danger">*</span></label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addLocationModal" style="font-size: 12px; color: var(--accent); text-decoration: none;">+ Добавить новую</a>
                                </div>
                                <select name="location_id" class="form-control-custom custom-dark-select">
                                    <option value="">Выберите локацию</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ (old('location_id') == $location->id || session('new_location_id') == $location->id) ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Примечание</label>
                            <textarea name="notes" class="form-control-custom" rows="2">{{ old('notes') }}</textarea>
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
                        @if ($errors->hasBag('categoryModal'))
                            <div class="alert custom-alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->categoryModal->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Название категории <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom" placeholder="Например: Мониторы">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">Назад</button>
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
                <form action="{{ route('admin.location.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_to" value="equipment">

                    <div class="modal-body">
                        @if ($errors->hasBag('locationModal'))
                            <div class="alert custom-alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->locationModal->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Название локации <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom" placeholder="Например: Склад №5">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Тип <span class="text-danger">*</span></label>
                            <select name="type" class="form-control-custom custom-dark-select">
                                <option value="office">Офис</option>
                                <option value="warehouse">Склад</option>
                                <option value="service">Сервис (Ремонт)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">Назад</button>
                        <button type="submit" class="btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function viewEquipment(id) { window.location.href = '/admin/equipment/' + id; }
        function editEquipment(id) { window.location.href = '/admin/equipment/' + id + '/edit'; }
        function assignEquipment(id) { window.location.href = '/admin/equipment/' + id + '/assign'; }


        document.addEventListener('DOMContentLoaded', function() {
            @if(session('open_category_modal') || $errors->hasBag('categoryModal'))
            new bootstrap.Modal(document.getElementById('addCategoryModal')).show();

            @elseif(session('open_location_modal') || $errors->hasBag('locationModal'))
            new bootstrap.Modal(document.getElementById('addLocationModal')).show();

            @elseif(session('reopen_equipment_modal') || ($errors->any() && !$errors->hasBag('categoryModal') && !$errors->hasBag('locationModal')))
            new bootstrap.Modal(document.getElementById('addEquipmentModal')).show();
            @endif
        });
    </script>
@endpush
