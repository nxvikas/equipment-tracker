@extends('layouts.app')

@section('title', $category->name)

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
                    <a href="{{ route('admin.categories.index') }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Назад к списку
                    </a>
                @endif
                <h1 class="page-title mt-2">{{ $category->name }}</h1>
                <p class="page-subtitle">Категория оборудования</p>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-lg-4 col-md-5">
                <div class="equipment-card">
                    <div class="equipment-card-body">
                        @php
                            $categoryIcon = match(strtolower($category->name)) {
                                'ноутбуки' => 'bi-laptop',
                                'мониторы' => 'bi-display',
                                'принтеры' => 'bi-printer',
                                'планшеты' => 'bi-tablet',
                                'сетевое оборудование' => 'bi-router',
                                'периферия' => 'bi-mouse',
                                'телефоны' => 'bi-phone',
                                default => 'bi-grid'
                            };
                        @endphp


                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="flex-shrink-0">
                                <i class="bi {{ $categoryIcon }}" style="font-size: 48px; color: var(--accent);"></i>
                            </div>
                            <div>
                                <h5 style="color: var(--accent); margin-bottom: 4px;">
                                    {{ $category->name }}
                                </h5>
                                <p class="text-secondary mb-0" style="font-size: 13px;">
                                    {{ $category->description ?: 'Категория оборудования' }}
                                </p>
                            </div>
                        </div>

                        <div class="info-grid">
                            <div class="info-item info-item-full">
                                <span class="info-label">Количество оборудования</span>
                                <span class="info-value">
    В данной категории содержится {{ $category->equipment_count }} ед. оборудования.
</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-8 col-md-7">
                <div class="equipment-card">
                    <div class="equipment-card-body">

                        <div class="actions-title mb-3">
                            <i class="bi bi-gear"></i> Действия с категорией
                        </div>
                        <div class="actions-grid">
                            <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                <i class="bi bi-pencil"></i> Редактировать
                            </button>
                            @if($category->equipment_count == 0)
                                <button type="button" class="action-button action-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">
                                    <i class="bi bi-trash"></i> Удалить
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="equipment-section mt-4">
            <div class="table-wrapper">
                <div class="table-head">
                    <div>
                        <h3><i class="bi bi-laptop"></i> Оборудование в категории</h3>
                        <p class="table-desc">Техника, относящаяся к данной категории</p>
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
                                <th>Статус</th>
                                <th>Локация</th>
                                <th>Сотрудник</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($equipments as $equipment)
                                <tr>
                                    <td class="inv-number">{{ $equipment->inventory_number }}</td>
                                    <td class="equipment-name">
                                        <a href="{{ route('admin.equipment.show', ['equipment' => $equipment->id, 'from_category' => $category->id]) }}">
                                            {{ $equipment->name }}
                                        </a>
                                    </td>
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
                                    <td>{{ $equipment->location->name ?? '—' }}</td>
                                    <td>{{ $equipment->currentUser ? $equipment->currentUser->surname . ' ' . $equipment->currentUser->name : '—' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center text-secondary py-4">
                            <i class="bi bi-inbox"></i> Нет оборудования в категории
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editCategoryModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Редактировать категорию</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" id="editCategoryForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom" value="{{ old('name', $category->name) }}">
                            <div class="invalid-feedback" data-error="name"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="description" class="form-control-custom" rows="2">{{ old('description', $category->description) }}</textarea>
                            <div class="invalid-feedback" data-error="description"></div>
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


    @if($category->equipment_count == 0)
        <div class="modal fade" id="deleteCategoryModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Вы уверены, что хотите удалить категорию?</p>
                        <p class="text-secondary"><strong>{{ $category->name }}</strong></p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" id="deleteCategoryForm">
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
            const editForm = document.getElementById('editCategoryForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
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
                        .catch(() => {});
                });
            }

            const deleteForm = document.getElementById('deleteCategoryForm');
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
                                window.location.href = '{{ route("admin.categories.index") }}';
                            } else {
                                alert(data.message || 'Ошибка при удалении');
                            }
                        });
                });
            }
        });
    </script>
@endpush
