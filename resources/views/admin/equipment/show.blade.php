@extends('layouts.app')

@section('title', $equipment->name)

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
                <a href="{{ route('admin.equipment') }}" class="text-secondary text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Назад к списку
                </a>
                <h1 class="page-title mt-2">{{ $equipment->name }}</h1>
                <p class="page-subtitle">{{ $equipment->inventory_number }}</p>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-4 col-md-5">
                <div class="equipment-card">
                    <div class="equipment-card-body text-center">
                        @if($equipment->qr_code)
                            <img src="{{ route('admin.equipment.qrcode', $equipment->id) }}" alt="QR-код"
                                 class="qr-code-image">
                            <div class="mt-3">
                                <a href="{{ route('admin.equipment.qrcode', $equipment->id) }}"
                                   download="qr-{{ $equipment->inventory_number }}.png"
                                   class="btn-outline w-100">
                                    <i class="bi bi-download"></i> Скачать QR-код
                                </a>
                            </div>
                        @else
                            <div class="qr-placeholder">
                                <i class="bi bi-qr-code"></i>
                                <p>QR-код не сгенерирован</p>
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
                                <span class="info-value">
                                {{ $equipment->location->name ?? '—' }}
                                    @if($equipment->location)
                                        <span class="location-type-badge">
                                        {{ \App\Http\Enums\TypeLocation::ruValues()[$equipment->location->type] ?? '' }}
                                    </span>
                                    @endif
                            </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Сотрудник</span>
                                <span
                                    class="info-value">{{ $equipment->currentUser ? $equipment->currentUser->surname . ' ' . $equipment->currentUser->name : '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Дата покупки</span>
                                <span class="info-value">{{ $equipment->purchase_date?->format('d.m.Y') ?? '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Стоимость</span>
                                <span
                                    class="info-value">{{ $equipment->purchase_price ? number_format($equipment->purchase_price, 0, ',', ' ') . ' ₽' : '—' }}</span>
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


        <div class="actions-card">
            <div class="actions-title">
                <i class="bi bi-gear"></i> Действия с оборудованием
            </div>
            <div class="actions-grid">
                @if($equipment->status === 'written')
                    <div class="text-secondary py-2">
                        <i class="bi bi-info-circle"></i> Оборудование списано. Действия недоступны.
                    </div>
                @else
                    <button type="button" class="action-button" data-bs-toggle="modal"
                            data-bs-target="#editEquipmentModal">
                        <i class="bi bi-pencil"></i> Редактировать
                    </button>

                    @if($equipment->status === 'in_stock')
                        <button type="button" class="action-button" data-bs-toggle="modal"
                                data-bs-target="#assignModal">
                            <i class="bi bi-person-check"></i> Выдать сотруднику
                        </button>
                    @endif

                    @if($equipment->status === 'in_use')
                        <button type="button" class="action-button" data-bs-toggle="modal"
                                data-bs-target="#returnModal">
                            <i class="bi bi-box-arrow-in-right"></i> Вернуть на склад
                        </button>
                    @endif

                    @if($equipment->status !== 'repair' && $equipment->status !== 'written')
                        <button type="button" class="action-button" data-bs-toggle="modal"
                                data-bs-target="#repairModal">
                            <i class="bi bi-tools"></i> В ремонт
                        </button>
                    @endif

                    @if($equipment->status === 'repair')
                        <button type="button" class="action-button" data-bs-toggle="modal"
                                data-bs-target="#returnFromRepairModal">
                            <i class="bi bi-check-circle"></i> Вернуть из ремонта
                        </button>
                    @endif


                    @if($equipment->status !== 'written')
                        <button type="button" class="action-button action-danger" data-bs-toggle="modal"
                                data-bs-target="#writeOffModal">
                            <i class="bi bi-trash"></i> Списать
                        </button>
                    @endif

                    @php
                        $otherActions = $equipment->history->filter(function($record) {
                            return $record->action_type !== \App\Http\Enums\TypeEquipmentHistory::CREATED->value;
                        })->count();
                    @endphp

                    @if($otherActions === 0)
                        <button type="button" class="action-button action-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteModal">
                            <i class="bi bi-x-circle"></i> Удалить
                        </button>
                    @endif
                @endif
            </div>
        </div>


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
                            <td>{{ $record->user->name ?? 'Система' }}</td>
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
    </div>


    <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-trash" style="font-size: 48px; color: var(--danger);"></i>
                    <p class="mt-3 mb-0">Вы уверены, что хотите удалить оборудование?</p>
                    <p class="text-secondary mt-2">
                        <strong>{{ $equipment->name }}</strong><br>
                        {{ $equipment->inventory_number }}
                    </p>
                    <p class="text-danger small mt-3">
                        <i class="bi bi-exclamation-circle"></i> Это действие нельзя отменить.
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                    <form action="{{ route('admin.equipment.destroy', $equipment->id) }}" method="POST">
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


    <div class="modal fade" id="editEquipmentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2" style="color: var(--accent);"></i>
                        Редактирование оборудования
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.equipment.update', $equipment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
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
                                <input type="date" name="purchase_date" class="form-control-custom custom-dark-select"
                                       value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Стоимость (₽)</label>
                                <input type="number" step="0.01" name="purchase_price" class="form-control-custom"
                                       value="{{ old('purchase_price', $equipment->purchase_price) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Гарантия до</label>
                                <input type="date" name="warranty_date" class="form-control-custom custom-dark-select"
                                       value="{{ old('warranty_date', $equipment->warranty_date?->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Статус <span class="text-danger">*</span></label>
                                <select name="status" class="form-control-custom custom-dark-select">
                                    <option
                                        value="in_stock" {{ old('status', $equipment->status) == 'in_stock' ? 'selected' : '' }}>
                                        На складе
                                    </option>
                                    <option
                                        value="in_use" {{ old('status', $equipment->status) == 'in_use' ? 'selected' : '' }}>
                                        В работе
                                    </option>
                                    <option
                                        value="repair" {{ old('status', $equipment->status) == 'repair' ? 'selected' : '' }}>
                                        В ремонте
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Местоположение <span
                                            class="text-danger">*</span></label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addLocationModal"
                                       style="font-size: 12px; color: var(--accent); text-decoration: none;">+ Добавить
                                        новую</a>
                                </div>
                                <select name="location_id" class="form-control-custom custom-dark-select">
                                    <option value="">Выберите локацию</option>
                                    @foreach($locations as $location)
                                        <option
                                            value="{{ $location->id }}" {{ old('location_id', $equipment->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 user-field" style="display: none;">
                                <label class="form-label" id="editUserLabel">Сотрудник</label>
                                <select name="current_user_id" class="form-control-custom custom-dark-select">
                                    <option value="">Выберите сотрудника</option>
                                    @foreach($users as $user)
                                        <option
                                            value="{{ $user->id }}" {{ old('current_user_id', $equipment->current_user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
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
                        <button type="submit" class="btn-primary">Сохранить изменения</button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.category.store') }}" method="POST" class="category-form">
                    @csrf
                    <input type="hidden" name="source" value="show_page">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название категории <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom"
                                   placeholder="Например: Мониторы" value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="description" class="form-control-custom"
                                      placeholder="Дополнительная информация о категории"
                                      rows="2">{{ old('description') }}</textarea>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.location.store') }}" method="POST" class="location-form">
                    @csrf
                    <input type="hidden" name="source" value="show_page">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название локации <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom"
                                   placeholder="Например: Склад №5" value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Тип <span class="text-danger">*</span></label>
                            <select name="type" class="form-control-custom custom-dark-select">
                                <option value="office" {{ old('type') == 'office' ? 'selected' : '' }}>Офис</option>
                                <option value="warehouse" {{ old('type') == 'warehouse' ? 'selected' : '' }}>Склад
                                </option>
                                <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Сервис
                                    (Ремонт)
                                </option>
                                <option value="remote" {{ old('type') == 'remote' ? 'selected' : '' }}>Удаленно</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Адрес</label>
                            <textarea name="address" class="form-control-custom"
                                      placeholder="Физический адрес (необязательно)"
                                      rows="2">{{ old('address') }}</textarea>
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


    <div class="modal fade" id="returnModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-in-right me-2" style="color: var(--accent);"></i>
                        Подтверждение возврата
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.equipment.return', $equipment->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Вы уверены, что хотите вернуть оборудование на склад?</p>
                        <p class="text-secondary">
                            <strong>{{ $equipment->name }}</strong><br>
                            Сотрудник: {{ $equipment->currentUser?->name ?? '—' }}
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Комментарий</label>
                            <textarea name="comment" class="form-control-custom" rows="2"
                                      placeholder="Необязательно"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary">Подтвердить возврат</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="repairModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <i class="bi bi-tools me-2" style="color: var(--warning);"></i>
                        Отправка в ремонт
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.equipment.repair', $equipment->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-secondary">
                            <strong>{{ $equipment->name }}</strong><br>
                            {{ $equipment->inventory_number }}
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Сервисный центр <span class="text-danger">*</span></label>
                            <select name="location_id" class="form-control-custom custom-dark-select">
                                <option value="">Выберите сервис</option>
                                @foreach($locations->where('type', 'service') as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Причина ремонта <span class="text-danger">*</span></label>
                            <textarea name="comment" class="form-control-custom" rows="3"
                                      placeholder="Опишите неисправность"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary">Отправить в ремонт</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="writeOffModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-trash me-2"></i>
                        Списание оборудования
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.equipment.write-off', $equipment->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-secondary">
                            <strong>{{ $equipment->name }}</strong><br>
                            {{ $equipment->inventory_number }}
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Причина списания <span class="text-danger">*</span></label>
                            <textarea name="comment" class="form-control-custom" rows="3"
                                      placeholder="Укажите причину"></textarea>
                        </div>
                        <p class="text-danger small">
                            <i class="bi bi-exclamation-circle"></i> После списания оборудование нельзя будет
                            восстановить.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary" style="background: var(--danger); color: white;">
                            Подтвердить списание
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="assignModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <i class="bi bi-person-check me-2" style="color: var(--accent);"></i>
                        Выдача оборудования
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.equipment.assign', $equipment->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-secondary">
                            <strong>{{ $equipment->name }}</strong><br>
                            {{ $equipment->inventory_number }}
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Сотрудник <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-control-custom custom-dark-select">
                                <option value="">Выберите сотрудника</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-end mb-2">
                                <label class="form-label mb-0">Новая локация</label>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#addLocationModal"
                                   style="font-size: 12px; color: var(--accent); text-decoration: none;">
                                    + Добавить новую
                                </a>
                            </div>
                            <select name="location_id" class="form-control-custom custom-dark-select">
                                <option value="">Оставить текущую ({{ $equipment->location->name ?? '—' }})</option>
                                @foreach($locations->where('type', 'office') as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Комментарий</label>
                            <textarea name="comment" class="form-control-custom" rows="2"
                                      placeholder="Необязательно"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary">Выдать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="returnFromRepairModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2" style="color: var(--success);"></i>
                        Возврат из ремонта
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.equipment.return-from-repair', $equipment->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-secondary">
                            <strong>{{ $equipment->name }}</strong><br>
                            {{ $equipment->inventory_number }}
                        </p>
                        <div class="mb-3">
                            <label class="form-label">Куда вернуть? <span class="text-danger">*</span></label>
                            <select name="location_id" class="form-control-custom custom-dark-select">
                                <option value="">Выберите локацию</option>
                                @foreach($locations->whereIn('type', ['warehouse', 'office']) as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}
                                        ({{ \App\Http\Enums\TypeLocation::ruValues()[$location->type] ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-hint">Склад или офис</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Комментарий</label>
                            <textarea name="comment" class="form-control-custom" rows="2"
                                      placeholder="Например: Отремонтировано, исправно"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary">Подтвердить возврат</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('edit_modal_open') || ($errors->any() && !$errors->hasBag('categoryModal') && !$errors->hasBag('locationModal')))
            new bootstrap.Modal(document.getElementById('editEquipmentModal')).show();
            @endif
            @if(session('open_category_modal') || $errors->hasBag('categoryModal'))
            new bootstrap.Modal(document.getElementById('addCategoryModal')).show();
            @endif
            @if(session('open_location_modal') || $errors->hasBag('locationModal'))
            new bootstrap.Modal(document.getElementById('addLocationModal')).show();
            @endif
        });


        async function submitCategoryForm(form) {
            const formData = new FormData(form);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addCategoryModal')).hide();


                    const editSelect = document.querySelector('#editEquipmentModal select[name="category_id"]');
                    if (editSelect) {
                        editSelect.add(new Option(data.item.name, data.item.id));
                    }

                    showToast(data.message, 'success');
                    form.reset();
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                } else {
                    showFormErrors(form, data.errors);
                }
            } catch (error) {
                showToast('Произошла ошибка', 'danger');
            }
        }


        async function submitLocationForm(form) {
            const formData = new FormData(form);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}
                });
                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addLocationModal')).hide();


                    const editSelect = document.querySelector('#editEquipmentModal select[name="location_id"]');
                    if (editSelect) {
                        editSelect.add(new Option(data.item.name, data.item.id));
                    }


                    const assignSelect = document.querySelector('#assignModal select[name="location_id"]');
                    if (assignSelect) {
                        assignSelect.add(new Option(data.item.name, data.item.id));
                    }

                    showToast(data.message, 'success');
                    form.reset();
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                } else {
                    showFormErrors(form, data.errors);
                }
            } catch (error) {
                showToast('Произошла ошибка', 'danger');
            }
        }

        async function submitReturnFromRepairForm(form) {
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {

                    bootstrap.Modal.getInstance(document.getElementById('returnFromRepairModal')).hide();


                    window.location.reload();
                } else {

                    showFormErrors(form, data.errors);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showToast('Произошла ошибка', 'danger');
            }
        }

        async function submitAssignForm(form) {
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('assignModal')).hide();
                    window.location.reload();
                } else {
                    showFormErrors(form, data.errors);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showToast('Произошла ошибка', 'danger');
            }
        }

        async function submitRepairForm(form) {
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('repairModal')).hide();
                    window.location.reload();
                } else {
                    showFormErrors(form, data.errors);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showToast('Произошла ошибка', 'danger');
            }
        }

        async function submitWriteOffForm(form) {
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('writeOffModal')).hide();
                    window.location.reload();
                } else {
                    showFormErrors(form, data.errors);
                }
            } catch (error) {
                console.error('Ошибка:', error);
                showToast('Произошла ошибка', 'danger');
            }
        }

        function showFormErrors(form, errors) {
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            for (let field in errors) {
                const input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = errors[field][0];
                    input.closest('.mb-3')?.appendChild(feedback) || input.parentNode.appendChild(feedback);
                }
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} position-fixed bottom-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.style.backgroundColor = type === 'success' ? 'rgba(190, 242, 100, 0.9)' : 'rgba(239, 68, 68, 0.9)';
            toast.style.color = type === 'success' ? '#02040a' : '#fff';
            toast.style.borderRadius = '12px';
            toast.style.padding = '12px 20px';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }


        document.addEventListener('DOMContentLoaded', function () {

            const categoryForm = document.querySelector('#addCategoryModal form');
            if (categoryForm) {
                categoryForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    submitCategoryForm(this);
                });
            }


            const locationForm = document.querySelector('#addLocationModal form');
            if (locationForm) {
                locationForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    submitLocationForm(this);
                });
            }


            const editStatusSelect = document.querySelector('#editEquipmentModal select[name="status"]');
            const editUserField = document.querySelector('#editEquipmentModal .user-field');
            if (editStatusSelect && editUserField) {
                function toggleEditUserField() {
                    const userLabel = editUserField.querySelector('.form-label');
                    if (editStatusSelect.value === 'in_use') {
                        editUserField.style.display = 'block';
                        if (userLabel) userLabel.innerHTML = 'Сотрудник <span class="text-danger">*</span>';
                    } else {
                        editUserField.style.display = 'none';
                        editUserField.querySelector('select').value = '';
                        if (userLabel) userLabel.innerHTML = 'Сотрудник';
                    }
                }

                editStatusSelect.addEventListener('change', toggleEditUserField);
                toggleEditUserField();
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            const addLocationModal = document.getElementById('addLocationModal');

            if (addLocationModal) {
                addLocationModal.addEventListener('shown.bs.modal', function () {

                    const openModals = document.querySelectorAll('.modal.show');
                    if (openModals.length > 1) {
                        this.style.zIndex = 1060 + (openModals.length * 10);
                    }
                });

                addLocationModal.addEventListener('hidden.bs.modal', function () {
                    this.style.zIndex = '';
                });
            }
            const returnFromRepairForm = document.querySelector('#returnFromRepairModal form');
            if (returnFromRepairForm) {
                returnFromRepairForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    submitReturnFromRepairForm(this);
                });
            }

            const assignForm = document.querySelector('#assignModal form');
            if (assignForm) {
                assignForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    submitAssignForm(this);
                });
            }

            const repairForm = document.querySelector('#repairModal form');
            if (repairForm) {
                repairForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    submitRepairForm(this);
                });
            }

            const writeOffForm = document.querySelector('#writeOffModal form');
            if (writeOffForm) {
                writeOffForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    submitWriteOffForm(this);
                });
            }

        });
    </script>
@endpush
