@extends('layouts.app')

@section('title', $user->surname . ' ' . $user->name)

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
                @if(request('from_equipment'))
                    <a href="{{ route('admin.equipment.show', request('from_equipment')) }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Назад к оборудованию
                    </a>
                @elseif(request('from_dashboard'))
                    <a href="{{ route('admin.dashboard') }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Назад на главную
                    </a>
                @else
                    <a href="{{ route('admin.users.index') }}" class="text-secondary text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Назад к списку
                    </a>
                @endif
                <h1 class="page-title mt-2">{{ $user->surname }} {{ $user->name }} {{ $user->patronymic }}</h1>
                <p class="page-subtitle">{{ $user->email }}</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-5">
                <div class="equipment-card">
                    <div class="equipment-card-body text-center">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Аватар" class="rounded-circle"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="avatar-placeholder"
                                 style="width: 150px; height: 150px; background: var(--bg-surface); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                <i class="bi bi-person" style="font-size: 64px; color: var(--text-secondary);"></i>
                            </div>
                        @endif
                        <div class="mt-3">
                            <span class="status-badge {{ match($user->status->value ?? $user->status) {
                                'active' => 'success',
                                'pending' => 'warning',
                                'blocked' => 'danger',
                                'rejected' => 'neutral',
                                default => 'info'
                            } }}">
                                {{ \App\Http\Enums\UserStatus::ruValues()[$user->status->value ?? $user->status] ?? $user->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-7">
                <div class="equipment-card">
                    <div class="equipment-card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Фамилия</span>
                                <span class="info-value fw-semibold">{{ $user->surname }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Имя</span>
                                <span class="info-value">{{ $user->name }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Отчество</span>
                                <span class="info-value">{{ $user->patronymic ?: '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $user->email }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Телефон</span>
                                <span class="info-value">{{ $user->phone }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Роль</span>
                                <span class="info-value">{{ $user->role->display_name ?? '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Отдел</span>
                                <span class="info-value">{{ $user->department->name ?? '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Должность</span>
                                <span class="info-value">{{ $user->position->name ?? '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Дата регистрации</span>
                                <span class="info-value">{{ $user->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="info-item info-item-full">
                                <span class="info-label">Статус</span>
                                <span class="info-value">
                                    {{ \App\Http\Enums\UserStatus::ruValues()[$user->status->value ?? $user->status] ?? $user->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="actions-card">
            <div class="actions-title">
                <i class="bi bi-gear"></i> Действия с сотрудником
            </div>
            <div class="actions-grid">
                <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#editUserModal">
                    <i class="bi bi-pencil"></i> Редактировать
                </button>

                @php $currentStatus = $user->status->value ?? $user->status; @endphp

                @if($currentStatus === 'pending')
                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="action-button text-success">
                            <i class="bi bi-check-circle"></i> Активировать
                        </button>
                    </form>
                    <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="action-button text-danger">
                            <i class="bi bi-x-circle"></i> Отклонить
                        </button>
                    </form>
                @elseif($currentStatus === 'active')
                    <button type="button" class="action-button text-warning" data-bs-toggle="modal"
                            data-bs-target="#blockUserModal">
                        <i class="bi bi-lock"></i> Заблокировать
                    </button>
                @elseif(in_array($currentStatus, ['blocked', 'rejected']))
                    <button type="button" class="action-button text-success" data-bs-toggle="modal"
                            data-bs-target="#activateUserModal">
                        <i class="bi bi-unlock"></i> Активировать
                    </button>
                @endif

                @if($user->status->value !== 'pending' && $user->role->name !== 'admin')
                    <button type="button" class="action-button" data-bs-toggle="modal" data-bs-target="#makeAdminModal">
                        <i class="bi bi-shield-plus"></i> Назначить администратором
                    </button>
                @elseif($user->status->value !== 'pending' && $user->role->name === 'admin')
                    @if($user->id !== auth()->id())
                        <button type="button" class="action-button action-danger" data-bs-toggle="modal"
                                data-bs-target="#removeAdminModal">
                            <i class="bi bi-shield-slash"></i> Снять права администратора
                        </button>
                    @endif
                @endif

                @if($user->id !== auth()->id())
                    <button type="button" class="action-button action-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteUserModal">
                        <i class="bi bi-trash"></i> Удалить
                    </button>
                @endif
            </div>
        </div>

        <div class="equipment-section mt-4">
            <div class="row g-4">

                <div class="col-lg-6">
                    <div class="table-wrapper">
                        <div class="table-head">
                            <div>
                                <h3><i class="bi bi-laptop"></i> Выданное оборудование</h3>
                                <p class="table-desc">Техника, закреплённая за сотрудником</p>
                            </div>
                            @if($assignedEquipments->isNotEmpty())
                                <span class="chart-badge">{{ $assignedEquipments->count() }} ед.</span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            @if($assignedEquipments->isNotEmpty())
                                <table class="custom-table">
                                    <thead>
                                    <tr>
                                        <th>Инв. номер</th>
                                        <th>Название</th>
                                        <th>Категория</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($assignedEquipments as $equipment)
                                        <tr>
                                            <td class="inv-number">{{ $equipment->inventory_number }}</td>
                                            <td class="equipment-name">
                                                <a href="{{ route('admin.equipment.show', ['equipment' => $equipment->id, 'from_user' => $user->id]) }}">
                                                    {{ $equipment->name }}
                                                </a>
                                            </td>
                                            <td>{{ $equipment->category->name ?? '—' }}</td>
                                            <td>
                                                <button class="action-btn btn-return"
                                                        data-equipment-id="{{ $equipment->id }}"
                                                        data-equipment-name="{{ $equipment->name }}"
                                                        data-user-id="{{ $user->id }}"
                                                        title="Вернуть на склад">
                                                    <i class="bi bi-box-arrow-in-right" style="color: #10b981;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center text-secondary py-4">
                                    <i class="bi bi-inbox"></i> Нет выданного оборудования
                                </div>
                            @endif
                        </div>
                    </div>
                </div>


                <div class="col-lg-6">
                    <div class="table-wrapper">
                        <div class="table-head">
                            <div>
                                <h3><i class="bi bi-box-seam"></i> Доступно для выдачи</h3>
                                <p class="table-desc">Оборудование на складе</p>
                            </div>
                            @if($availableEquipments->isNotEmpty())
                                <span class="chart-badge" style="background: rgba(59,130,246,0.12); color: #3b82f6;">
                            {{ $availableEquipments->count() }} ед.
                        </span>
                            @endif
                        </div>
                        <div class="table-responsive">
                            @if($availableEquipments->isNotEmpty())
                                <table class="custom-table">
                                    <thead>
                                    <tr>
                                        <th>Инв. номер</th>
                                        <th>Название</th>
                                        <th>Категория</th>
                                        <th>Локация</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($availableEquipments as $equipment)
                                        <tr>
                                            <td class="inv-number">{{ $equipment->inventory_number }}</td>
                                            <td class="equipment-name">
                                                <a href="{{ route('admin.equipment.show', ['equipment' => $equipment->id, 'from_user' => $user->id]) }}">
                                                    {{ $equipment->name }}
                                                </a>
                                            </td>
                                            <td>{{ $equipment->category->name ?? '—' }}</td>
                                            <td>{{ $equipment->location->name ?? '—' }}</td>
                                            <td>
                                                @if($user->status->value === 'active')
                                                    <button class="action-btn btn-assign"
                                                            data-equipment-id="{{ $equipment->id }}"
                                                            data-equipment-name="{{ $equipment->name }}"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->surname }} {{ $user->name }}"
                                                            title="Выдать сотруднику">
                                                        <i class="bi bi-person-check" style="color: var(--accent);"></i>
                                                    </button>
                                                @else
                                                    <span class="text-secondary" title="Сотрудник заблокирован">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center text-secondary py-4">
                                    <i class="bi bi-inbox"></i> Нет доступного оборудования
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="history-section">
            <div class="history-header">
                <h3><i class="bi bi-clock-history"></i> История операций</h3>
                <span class="history-desc">Действия, связанные с сотрудником</span>
            </div>
            <div class="table-responsive">
                <table class="history-table">
                    <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Оборудование</th>
                        <th>Действие</th>
                        <th>Детали</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($history as $record)
                        <tr>
                            <td class="history-date">{{ $record->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                @if($record->equipment)
                                    <a href="{{ route('admin.equipment.show', ['equipment' => $record->equipment_id, 'from_user' => $user->id]) }}"
                                       class="equipment-name">
                                        {{ $record->equipment->name }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $actionTypes[$record->action_type] ?? $record->action_type }}</td>
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
            @if($history->hasPages())
                <div class="pagination-wrapper">
                    {{ $history->links() }}
                </div>
            @endif
        </div>
    </div>


    <div class="modal fade" id="editUserModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Редактировать сотрудника</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.users.update.full', $user) }}" method="POST" id="editUserFullForm">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Фамилия <span class="text-danger">*</span></label>
                                <input type="text" name="surname"
                                       class="form-control-custom @error('surname') is-invalid @enderror"
                                       value="{{ old('surname', $user->surname) }}">
                                @error('surname')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Имя <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                       class="form-control-custom @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Отчество</label>
                                <input type="text" name="patronymic"
                                       class="form-control-custom @error('patronymic') is-invalid @enderror"
                                       value="{{ old('patronymic', $user->patronymic) }}">
                                @error('patronymic')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                       class="form-control-custom @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Телефон <span class="text-danger">*</span></label>
                                <input type="text" name="phone"
                                       class="form-control-custom @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Статус <span class="text-danger">*</span></label>
                                <select name="status"
                                        class="form-control-custom custom-dark-select @error('status') is-invalid @enderror">
                                    @php $currentStatus = old('status', $user->status->value ?? $user->status); @endphp

                                    @if($currentStatus === 'pending')
                                        <option value="pending" @selected($currentStatus == 'pending')>Ожидает
                                            подтверждения
                                        </option>
                                        <option value="active" @selected($currentStatus == 'active')>Активировать
                                        </option>
                                        <option value="rejected" @selected($currentStatus == 'rejected')>Отклонить
                                        </option>
                                    @elseif($currentStatus === 'active')
                                        <option value="active" @selected($currentStatus == 'active')>Активен</option>
                                        <option value="blocked" @selected($currentStatus == 'blocked')>Заблокировать
                                        </option>
                                    @elseif($currentStatus === 'blocked')
                                        <option value="blocked" @selected($currentStatus == 'blocked')>Заблокирован
                                        </option>
                                        <option value="active" @selected($currentStatus == 'active')>Активировать
                                        </option>
                                    @elseif($currentStatus === 'rejected')
                                        <option value="rejected" @selected($currentStatus == 'rejected')>Отклонён
                                        </option>
                                        <option value="active" @selected($currentStatus == 'active')>Активировать
                                        </option>
                                        <option value="blocked" @selected($currentStatus == 'blocked')>Заблокировать
                                        </option>
                                    @else
                                        @foreach(\App\Http\Enums\UserStatus::cases() as $status)
                                            <option
                                                value="{{ $status->value }}" @selected($currentStatus == $status->value)>
                                                {{ \App\Http\Enums\UserStatus::ruValues()[$status->value] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Отдел</label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addDepartmentModal"
                                       style="font-size: 12px; color: var(--accent);">+ Добавить</a>
                                </div>
                                <select name="department_id"
                                        class="form-control-custom custom-dark-select @error('department_id') is-invalid @enderror">
                                    <option value="">Не назначен</option>
                                    @foreach($departments as $dept)
                                        <option
                                            value="{{ $dept->id }}" @selected(old('department_id', $user->department_id) == $dept->id)>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Должность</label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addPositionModal"
                                       style="font-size: 12px; color: var(--accent);">+ Добавить</a>
                                </div>
                                <select name="position_id"
                                        class="form-control-custom custom-dark-select @error('position_id') is-invalid @enderror">
                                    <option value="">Не назначена</option>
                                    @foreach($positions as $pos)
                                        <option
                                            value="{{ $pos->id }}" @selected(old('position_id', $user->position_id) == $pos->id)>
                                            {{ $pos->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary">Сохранить все изменения</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addDepartmentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Новый отдел</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.departments.store') }}" method="POST" class="add-department-form">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom" placeholder="Например: IT-отдел">
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

    <div class="modal fade" id="addPositionModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Новая должность</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.positions.store') }}" method="POST" class="add-position-form">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom"
                                   placeholder="Например: Разработчик">
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


    <div class="modal fade" id="returnFromUserModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-in-right me-2" style="color: var(--accent);"></i>
                        Подтверждение возврата
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.equipment.return-from-user') }}" method="POST">
                    @csrf
                    <input type="hidden" name="equipment_id" id="returnEquipmentId">
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="modal-body">
                        <p>Вы уверены, что хотите вернуть оборудование на склад?</p>
                        <p class="text-secondary">
                            Сотрудник: <strong>{{ $user->surname }} {{ $user->name }}</strong><br>
                            Оборудование: <strong id="returnEquipmentName">—</strong>
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


    <div class="modal fade" id="assignToUserModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <i class="bi bi-person-check me-2" style="color: var(--accent);"></i>
                        Выдача оборудования
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <form action="{{ route('admin.equipment.assign-to-user') }}" method="POST">
                    @csrf
                    <input type="hidden" name="equipment_id" id="assignEquipmentId">
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="modal-body">
                        <p class="text-secondary">
                            Сотрудник: <strong>{{ $user->surname }} {{ $user->name }}</strong><br>
                            Оборудование: <strong id="assignEquipmentName">—</strong>
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn-primary">Выдать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteUserModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p>Вы уверены, что хотите удалить сотрудника?</p>
                    <p class="text-secondary">
                        <strong>{{ $user->surname }} {{ $user->name }}</strong><br>
                        {{ $user->email }}
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
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


    <div class="modal fade" id="blockUserModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-warning">
                        <i class="bi bi-lock me-2"></i>Подтверждение блокировки
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p>Вы уверены, что хотите заблокировать сотрудника?</p>
                    <p class="text-secondary">
                        <strong>{{ $user->surname }} {{ $user->name }}</strong><br>
                        {{ $user->email }}
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                    <form action="{{ route('admin.users.block', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary" style="background: var(--warning); color: #02040a;">
                            <i class="bi bi-lock"></i> Заблокировать
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="activateUserModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-success">
                        <i class="bi bi-unlock me-2"></i>Подтверждение активации
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p>Вы уверены, что хотите активировать сотрудника?</p>
                    <p class="text-secondary">
                        <strong>{{ $user->surname }} {{ $user->name }}</strong><br>
                        {{ $user->email }}
                    </p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                    <form action="{{ route('admin.users.activate', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary" style="background: var(--success); color: white;">
                            <i class="bi bi-unlock"></i> Активировать
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if($user->role->name !== 'admin')
        <div class="modal fade" id="makeAdminModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-warning">
                            <i class="bi bi-shield-plus me-2"></i>Назначение администратором
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <p>Вы уверены, что хотите назначить администратором?</p>
                        <p class="text-secondary">
                            <strong>{{ $user->surname }} {{ $user->name }}</strong><br>
                            {{ $user->email }}
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <form action="{{ route('admin.users.make-admin', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-primary"
                                    style="background: var(--warning); color: #02040a;">
                                <i class="bi bi-shield-plus"></i> Назначить
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        @if($user->id !== auth()->id())
            <div class="modal fade" id="removeAdminModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title text-danger">
                                <i class="bi bi-shield-slash me-2"></i>Снятие прав администратора
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            <p>Вы уверены, что хотите снять права администратора?</p>
                            <p class="text-secondary">
                                <strong>{{ $user->surname }} {{ $user->name }}</strong><br>
                                {{ $user->email }}
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <form action="{{ route('admin.users.remove-admin', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-primary"
                                        style="background: var(--danger); color: white;">
                                    <i class="bi bi-shield-slash"></i> Снять права
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editForm = document.querySelector('#editUserModal form');
            if (editForm) {
                editForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    submitAjaxForm(editForm, 'editUserModal', {reloadOnSuccess: true});
                });
            }

            document.querySelector('.add-department-form')?.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(e.target, 'addDepartmentModal', {selectName: 'department_id', reloadOnSuccess: false});
            });

            document.querySelector('.add-position-form')?.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(e.target, 'addPositionModal', {selectName: 'position_id', reloadOnSuccess: false});
            });
            document.querySelectorAll('.btn-return').forEach(btn => {
                btn.addEventListener('click', function () {
                    const equipmentId = this.dataset.equipmentId;
                    const equipmentName = this.dataset.equipmentName;

                    document.getElementById('returnEquipmentId').value = equipmentId;
                    document.getElementById('returnEquipmentName').textContent = equipmentName;

                    new bootstrap.Modal(document.getElementById('returnFromUserModal')).show();
                });
            });
            document.querySelectorAll('.btn-assign').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('assignEquipmentId').value = this.dataset.equipmentId;
                    document.getElementById('assignEquipmentName').textContent = this.dataset.equipmentName;
                    new bootstrap.Modal(document.getElementById('assignToUserModal')).show();
                });
            });
        });
    </script>
@endpush
