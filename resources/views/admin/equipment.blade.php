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
                           placeholder="Поиск по названию, инв. номеру...">
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
                        @php
                            $statusLabels = [
                                'in_stock' => 'На складе',
                                'in_use' => 'В работе',
                                'repair' => 'В ремонте',
                                'written' => 'Списано',
                            ];
                        @endphp
                        {{ request('status') ? $statusLabels[request('status')] : 'Все статусы' }}
                    </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li><a class="dropdown-item" href="#" data-value="">Все статусы</a></li>
                            <li><a class="dropdown-item {{ request('status') == 'in_stock' ? 'active' : '' }}" href="#"
                                   data-value="in_stock">На складе</a></li>
                            <li><a class="dropdown-item {{ request('status') == 'in_use' ? 'active' : '' }}" href="#"
                                   data-value="in_use">В работе</a></li>
                            <li><a class="dropdown-item {{ request('status') == 'repair' ? 'active' : '' }}" href="#"
                                   data-value="repair">В ремонте</a></li>
                            <li><a class="dropdown-item {{ request('status') == 'written' ? 'active' : '' }}" href="#"
                                   data-value="written">Списано</a></li>
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
                    @forelse($equipments as $item)
                        <tr>
                            <td style="text-align: center;">
                                @if($item->qr_code)
                                    <div class="bg-white d-inline-block rounded p-1 shadow-sm">
                                        <img src="{{ route('admin.equipment.qrcode', $item->id) }}" alt="QR"
                                             style="width: 40px; height: 40px;">
                                    </div>
                                @else
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                            <td class="inv-number">{{ $item->inventory_number }}</td>
                            <td class="equipment-name">{{ $item->name }}</td>
                            <td>{{ $item->category->name ?? '—' }}</td>
                            <td class="serial-number">{{ $item->serial_number ?? '—' }}</td>
                            <td>
                                @php
                                    $statusClass = match($item->status) {
                                        'in_use' => 'success',
                                        'in_stock' => 'neutral',
                                        'repair' => 'warning',
                                        'written' => 'danger',
                                        default => 'neutral'
                                    };
                                    $statusText = match($item->status) {
                                        'in_use' => 'В работе',
                                        'in_stock' => 'На складе',
                                        'repair' => 'В ремонте',
                                        'written' => 'Списано',
                                        default => $item->status
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td>{{ $item->currentUser ? $item->currentUser->surname . ' ' . $item->currentUser->name : '—' }}</td>
                            <td>
                                <a href="{{ route('admin.equipment.show', $item->id) }}"
                                   class="action-button"
                                   style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; text-decoration: none;">
                                    <i class="bi bi-eye"></i>
                                    <span>Открыть</span>
                                </a>

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
                                <select name="status"
                                        class="form-control-custom custom-dark-select @error('status') is-invalid @enderror">
                                    <option value="">Выберите статус</option>
                                    @foreach(\App\Http\Enums\StatusEquipment::cases() as $status)
                                        @if($status->value !== 'written')
                                            <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
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
                                    <option value="">Все типы</option>
                                    @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                                        <option value="{{ $type->value }}">
                                            {{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="row justify-content-end">
                            <div class="col-md-6 mb-3 user-field" style="display: none;">
                                <label class="form-label">Сотрудник <span class="text-danger">*</span></label>
                                <select name="current_user_id"
                                        class="form-control-custom custom-dark-select @error('current_user_id') is-invalid @enderror">
                                    <option value="">Выберите сотрудника</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('current_user_id') == $user->id ? 'selected' : '' }}>
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
                                    <label class="form-label mb-0">Выбор локации <span class="text-danger">*</span></label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addLocationModal"
                                       style="font-size: 12px; color: var(--accent); text-decoration: none;">+ Добавить новую</a>
                                </div>
                                <select name="location_id" id="locationSelect"
                                        class="form-control-custom custom-dark-select @error('location_id') is-invalid @enderror">
                                    <option value="">Выберите локацию</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}"
                                                data-type="{{ $location->type }}"
                                            {{ (old('location_id') == $location->id || session('new_location_id') == $location->id) ? 'selected' : '' }}>
                                            {{ $location->name }} ({{ \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? $location->type }})
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
                <form action="{{ route('admin.location.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_to" value="equipment">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название локации <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control-custom @error('name', 'locationModal') is-invalid @enderror"
                                   placeholder="Например: Склад №5"
                                   value="{{ old('name') }}">
                            @error('name', 'locationModal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Тип <span class="text-danger">*</span></label>
                            <select name="type"
                                    class="form-control-custom custom-dark-select @error('type', 'locationModal') is-invalid @enderror">
                                <option value="">Выберите тип</option>
                                @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                                    <option
                                        value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                        {{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type', 'locationModal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Адрес</label>
                            <textarea name="address"
                                      class="form-control-custom @error('address', 'locationModal') is-invalid @enderror"
                                      placeholder="Физический адрес (необязательно)"
                                      rows="2">{{ old('address') }}</textarea>
                            @error('address', 'locationModal')
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
@endsection

@push('scripts')
    <script>
        const initUserFieldToggle = () => {
            const statusSelect = document.querySelector('#addEquipmentModal select[name="status"]');
            const userField = document.querySelector('#addEquipmentModal .user-field');

            if (!statusSelect || !userField) return;

            const toggle = () => {
                const isInUse = statusSelect.value === 'in_use';
                userField.style.display = isInUse ? 'block' : 'none';
                if (!isInUse) {
                    const select = userField.querySelector('select');
                    if (select) select.value = '';
                }
                const label = userField.querySelector('.form-label');
                if (label) {
                    label.innerHTML = isInUse ? 'Сотрудник <span class="text-danger">*</span>' : 'Сотрудник';
                }
            };

            statusSelect.addEventListener('change', toggle);
            toggle();
        };

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

        const initLocationFilter = () => {
            const typeSelect = document.getElementById('locationTypeSelect');
            const locationSelect = document.getElementById('locationSelect');

            if (!typeSelect || !locationSelect) return;

            const allOptions = Array.from(locationSelect.options);

            typeSelect.addEventListener('change', () => {
                const selectedType = typeSelect.value;
                locationSelect.innerHTML = '<option value="">Выберите локацию</option>';

                allOptions.forEach(option => {
                    if (option.value === '') return;
                    const optionType = option.dataset.type;
                    if (!selectedType || optionType === selectedType) {
                        locationSelect.appendChild(option.cloneNode(true));
                    }
                });

                const savedValue = locationSelect.dataset.savedValue;
                if (savedValue) {
                    const optionToSelect = locationSelect.querySelector(`option[value="${savedValue}"]`);
                    if (optionToSelect) optionToSelect.selected = true;
                }
            });

            locationSelect.addEventListener('change', () => {
                locationSelect.dataset.savedValue = locationSelect.value;
            });

            const selectedOption = Array.from(allOptions).find(opt => opt.selected && opt.value !== '');
            if (selectedOption) {
                const optionType = selectedOption.dataset.type;
                if (optionType) {
                    typeSelect.value = optionType;
                    typeSelect.dispatchEvent(new Event('change'));
                }
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            @if($errors->any() && !$errors->hasBag('categoryModal') && !$errors->hasBag('locationModal'))
            new bootstrap.Modal(document.getElementById('addEquipmentModal')).show();
            @endif

            @if(session('reopen_equipment_modal'))
            new bootstrap.Modal(document.getElementById('addEquipmentModal')).show();
            @endif

            initCustomSelects();
            initUserFieldToggle();
            initLiveSearch();
            initLocationFilter();

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
                    submitAjaxForm(categoryForm, 'addCategoryModal', {selectName: 'category_id'});
                });
            }

            const locationForm = document.querySelector('#addLocationModal form');
            if (locationForm) {
                locationForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    submitAjaxForm(locationForm, 'addLocationModal', {selectName: 'location_id'});
                });
            }
        });
    </script>
@endpush
