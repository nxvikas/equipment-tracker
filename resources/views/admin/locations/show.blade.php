@extends('layouts.app')

@section('title', $location->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/aggregator/admin/equipment.css') }}">
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
                @elseif(request('from_equipment'))
                    <a href="{{ route('admin.equipment.show', request('from_equipment')) }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Назад к оборудованию
                    </a>
                @else
                    <a href="{{ route('admin.locations.index') }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Назад к списку
                    </a>
                @endif
                <h1 class="page-title mt-2">{{ $location->name }}</h1>
                <p class="page-subtitle">{{ \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? $location->type }}</p>
            </div>
        </div>

        <div class="row g-4">
            {{-- Левая колонка: иконка слева + данные --}}
            <div class="col-lg-4 col-md-5">
                <div class="equipment-card">
                    <div class="equipment-card-body">
                        @php
                            $typeIcon = match($location->type) {
                                'office' => 'bi-building',
                                'warehouse' => 'bi-box-seam',
                                'service' => 'bi-tools',
                                'remote' => 'bi-wifi',
                                default => 'bi-geo-alt'
                            };
                            $typeColor = match($location->type) {
                                'office' => '#3b82f6',
                                'warehouse' => '#f59e0b',
                                'service' => '#ef4444',
                                'remote' => '#10b981',
                                default => '#94a3b8'
                            };
                        @endphp

                        {{-- Иконка слева от текста --}}
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="flex-shrink-0">
                                <i class="bi {{ $typeIcon }}" style="font-size: 48px; color: {{ $typeColor }};"></i>
                            </div>
                            <div>
                                <h5 style="color: {{ $typeColor }}; margin-bottom: 4px;">
                                    {{ \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? $location->type }}
                                </h5>
                                <p class="text-secondary mb-0" style="font-size: 13px;">
                                    @php
                                        $typeDescription = match($location->type) {
                                            'office' => 'Офисное помещение',
                                            'warehouse' => 'Складское помещение',
                                            'service' => 'Сервисный центр',
                                            'remote' => 'Удалённое размещение',
                                            default => '—'
                                        };
                                    @endphp
                                    {{ $typeDescription }}
                                </p>
                            </div>
                        </div>

                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Название</span>
                                <span class="info-value fw-semibold">{{ $location->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Адрес</span>
                                <span class="info-value">{{ $location->address ?: '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Оборудования</span>
                                <span class="info-value">{{ $location->equipment_count }} ед.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Правая колонка: описание + действия --}}
            <div class="col-lg-8 col-md-7">
                <div class="equipment-card">
                    <div class="equipment-card-body">
                        <div class="info-grid">
                            <div class="info-item info-item-full">
                                <span class="info-label">Описание</span>
                                <span class="info-value">{{ $typeDescription }}</span>
                            </div>
                        </div>

                        {{-- Действия с локацией --}}
                        <div class="mt-4 pt-3 border-top">
                            <div class="actions-title mb-3">
                                <i class="bi bi-gear"></i> Действия с локацией
                            </div>
                            <div class="actions-grid">
                                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#editLocationModal">
                                    <i class="bi bi-pencil"></i> Редактировать
                                </button>
                                @if($location->equipment_count == 0)
                                    <button type="button" class="action-button action-danger" data-bs-toggle="modal" data-bs-target="#deleteLocationModal">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Таблица с оборудованием в локации --}}
        <div class="equipment-section mt-4">
            <div class="table-wrapper">
                <div class="table-head">
                    <div>
                        <h3><i class="bi bi-laptop"></i> Оборудование в локации</h3>
                        <p class="table-desc">Находится в данном местоположении</p>
                    </div>
                    @if($equipments->isNotEmpty())
                        <span class="chart-badge">{{ $equipments->count() }} ед.</span>
                    @endif
                </div>
                <div class="table-responsive">
                    @if($equipments->isNotEmpty())
                        <table class="custom-table">
                            <thead>
                            <tr>
                                <th>Инв. номер</th>
                                <th>Название</th>
                                <th>Категория</th>
                                <th>Статус</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($equipments as $equipment)
                                <tr>
                                    <td class="inv-number">{{ $equipment->inventory_number }}</td>
                                    <td class="equipment-name">
                                        <a href="{{ route('admin.equipment.show', ['equipment' => $equipment->id, 'from_location' => $location->id]) }}">
                                            {{ $equipment->name }}
                                        </a>
                                    </td>
                                    <td>{{ $equipment->category->name ?? '—' }}</td>
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
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center text-secondary py-4">
                            <i class="bi bi-inbox"></i> Нет оборудования в локации
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Модалка редактирования локации --}}
    <div class="modal fade" id="editLocationModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Редактировать локацию</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.locations.update', $location->id) }}" method="POST" id="editLocationForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom" value="{{ old('name', $location->name) }}">
                            <div class="invalid-feedback" data-error="name"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Тип <span class="text-danger">*</span></label>
                            <select name="type" class="form-control-custom">
                                @foreach(\App\Http\Enums\TypeLocation::cases() as $type)
                                    <option value="{{ $type->value }}" {{ old('type', $location->type) == $type->value ? 'selected' : '' }}>
                                        {{ \App\Http\Enums\TypeLocation::ruValues()[$type->value] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" data-error="type"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Адрес</label>
                            <textarea name="address" class="form-control-custom" rows="2">{{ old('address', $location->address) }}</textarea>
                            <div class="invalid-feedback" data-error="address"></div>
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

    {{-- Модалка удаления --}}
    @if($location->equipment_count == 0)
        <div class="modal fade" id="deleteLocationModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-trash" style="font-size: 48px; color: var(--danger);"></i>
                        <p class="mt-3 mb-0">Вы уверены, что хотите удалить локацию?</p>
                        <p class="text-secondary mt-2"><strong>{{ $location->name }}</strong></p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST" id="deleteLocationForm">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-primary" style="background: var(--danger); color: white;">
                                <i class="bi bi-trash"></i> Удалить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // AJAX-отправка формы редактирования локации
            const editForm = document.getElementById('editLocationForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Очищаем предыдущие ошибки
                    editForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    editForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                    const formData = new FormData(this);

                    fetch(this.action, {
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
                                location.reload();
                            } else if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    const input = editForm.querySelector(`[name="${field}"]`);
                                    const feedback = editForm.querySelector(`[data-error="${field}"]`);
                                    if (input) input.classList.add('is-invalid');
                                    if (feedback) feedback.textContent = data.errors[field][0];
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            }

            // AJAX-отправка формы удаления
            const deleteForm = document.getElementById('deleteLocationForm');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '{{ route("admin.locations.index") }}';
                            } else {
                                alert(data.message || 'Ошибка при удалении');
                            }
                        });
                });
            }
        });
    </script>
@endpush
