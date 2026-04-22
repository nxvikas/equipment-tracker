@extends('layouts.app')

@section('title', 'Управление локациями')

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
                    @if(request('from_dashboard'))
                        <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Назад на главную
                        </a>
                    @endif
                    <h1 class="page-title mt-2">Локации</h1>
                    <p class="page-subtitle">Управление местоположениями оборудования</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('admin.export.locations') }}" class="btn-outline" title="Экспорт в Excel" style="margin-right: 10px">
                        <i class="bi bi-download"></i> Экспорт
                    </a>
                    <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                        <i class="bi bi-plus-lg"></i> Добавить локацию
                    </button>
                </div>
            </div>


        <div class="filters-bar">
            <form method="GET" action="{{ route('admin.locations.index') }}" id="filterForm"
                  class="d-flex w-100 gap-3 justify-content-between">
                <div class="search-input-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           id="searchLocation"
                           class="search-input"
                           placeholder="Поиск по названию, адресу...">
                </div>

                <div class="filters-group">

                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="selected-text">
                                @if(request('type'))
                                    {{ \App\Http\Enums\TypeLocation::ruValues()[request('type')] ?? 'Все типы' }}
                                @else
                                    Все типы
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li><a class="dropdown-item {{ !request('type') ? 'active' : '' }}" href="#" data-value="">Все
                                    типы</a></li>
                            @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                                <li>
                                    <a class="dropdown-item {{ request('type') == $type->value ? 'active' : '' }}"
                                       href="#"
                                       data-value="{{ $type->value }}">
                                        {{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="type" class="custom-select-input" value="{{ request('type') }}">
                    </div>


                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="selected-text">
            @if(request('direction') === 'asc')
                Сначала меньше оборудования
            @else
                Сначала больше оборудования
            @endif
        </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li>
                                <a class="dropdown-item {{ request('direction', 'desc') === 'desc' ? 'active' : '' }}"
                                   href="#"
                                   data-direction="desc">
                                    Сначала больше оборудования
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('direction') === 'asc' ? 'active' : '' }}"
                                   href="#"
                                   data-direction="asc">
                                    Сначала меньше оборудования
                                </a>
                            </li>
                        </ul>
                        <input type="hidden" name="direction" class="custom-direction-input"
                               value="{{ request('direction', 'desc') }}">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                        <i class="bi bi-funnel"></i> Применить
                    </button>
                    <a href="{{ route('admin.locations.index') }}" class="btn-outline" style="padding: 10px 20px;">
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
                        <th>ID</th>
                        <th>Название</th>
                        <th>Тип</th>
                        <th>Адрес</th>
                        <th>Кол-во оборудования</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($locations as $location)
                        <tr>
                            <td>{{ $location->id }}</td>
                            <td class="equipment-name">{{ $location->name }}</td>
                            <td>{{ \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? $location->type }}</td>
                            <td class="location-address">{{ $location->address ?: '—' }}</td>
                            <td>{{ $location->equipment_count }}</td>
                            <td>
                                <button class="action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editLocationModal{{ $location->id }}"
                                        title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($location->equipment_count == 0)
                                    <button class="action-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteLocationModal{{ $location->id }}"
                                            title="Удалить">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0 border-bottom-0">
                                <div class="empty-state">
                                    <div class="empty-icon-wrapper">
                                        <i class="bi bi-inbox"></i>
                                    </div>
                                    <h4 class="empty-title">Нет локаций</h4>
                                    <p class="empty-desc">Добавьте первую локацию для размещения оборудования</p>
                                    <button class="btn-outline mt-3" data-bs-toggle="modal"
                                            data-bs-target="#addLocationModal">
                                        <i class="bi bi-plus-lg me-2"></i>Добавить локацию
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($locations->hasPages())
                <div class="pagination-wrapper">
                    {{ $locations->appends(request()->query())->links() }}
                </div>
            @endif
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
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @foreach($locations as $location)

        <div class="modal fade" id="editLocationModal{{ $location->id }}" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Редактировать локацию</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.locations.update', $location->id) }}" method="POST"
                          class="edit-location-form">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Название <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control-custom"
                                       value="{{ $location->name }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тип <span class="text-danger">*</span></label>
                                <select name="type" class="form-control-custom custom-dark-select">
                                    <option value="">Выберите тип</option>
                                    @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                                        <option
                                            value="{{ $type->value }}" {{ $location->type == $type->value ? 'selected' : '' }}>
                                            {{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Адрес</label>
                                <textarea name="address" class="form-control-custom"
                                          rows="2">{{ $location->address }}</textarea>
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


        @if($location->equipment_count == 0)
            <div class="modal fade" id="deleteLocationModal{{ $location->id }}" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title text-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <i class="bi bi-trash" style="font-size: 48px; color: var(--danger);"></i>
                            <p class="mt-3 mb-0">Вы уверены, что хотите удалить локацию?</p>
                            <p class="text-secondary mt-2">
                                <strong>{{ $location->name }}</strong>
                            </p>
                            <p class="text-danger small mt-3">
                                <i class="bi bi-exclamation-circle"></i> Это действие нельзя отменить.
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST"
                                  class="delete-location-form">
                                @csrf
                                @method('DELETE')
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
@endsection

@push('scripts')
    <script>

        const initLiveSearch = () => {
            const searchInput = document.getElementById('searchLocation');
            if (!searchInput) return;

            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.custom-table tbody tr').forEach(row => {
                    const name = row.querySelector('.equipment-name')?.textContent.toLowerCase() || '';
                    const address = row.querySelector('.location-address')?.textContent.toLowerCase() || '';
                    row.style.display = (name.includes(term) || address.includes(term)) ? '' : 'none';
                });
            });
        };


        document.addEventListener('DOMContentLoaded', () => {
            initCustomSelects();
            initLiveSearch();

            const addForm = document.getElementById('addLocationForm');
            if (addForm) {
                addForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    submitAjaxForm(addForm, 'addLocationModal', {reloadOnSuccess: true});
                });
            }

            document.querySelectorAll('.edit-location-form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const modalId = form.closest('.modal').id;
                    submitAjaxForm(form, modalId, {reloadOnSuccess: true});
                });
            });

            document.querySelectorAll('.delete-location-form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const modalId = form.closest('.modal').id;
                    submitAjaxForm(form, modalId, {reloadOnSuccess: true});
                });
            });
        });
    </script>
@endpush
