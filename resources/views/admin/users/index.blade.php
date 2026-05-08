@extends('layouts.app')

@section('title', 'Сотрудники')

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

                <h1 class="page-title mt-2">Пользователи системы</h1>
                <p class="page-subtitle">Управление пользователями и правами доступа</p>
            </div>
            <a href="{{ route('admin.export.users') }}" class="btn-outline" title="Экспорт в Excel">
                <i class="bi bi-download"></i> Экспорт
            </a>
        </div>


        <div class="filters-bar">
            <form method="GET" action="{{ route('admin.users.index') }}"
                  class="d-flex w-100 gap-3 justify-content-between">
                <div class="search-input-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchUser" class="search-input"
                           placeholder="Поиск по ФИО, email, телефону...">
                </div>

                <div class="filters-group">

                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown">
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
                                <li><a class="dropdown-item {{ request('status') == $value ? 'active' : '' }}" href="#"
                                       data-value="{{ $value }}">{{ $label }}</a></li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="status" class="custom-select-input" value="{{ request('status') }}">
                    </div>


                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown">
                            <span class="selected-text">
                                @if(request('department_id'))
                                    {{ $departments->find(request('department_id'))->name ?? 'Все отделы' }}
                                @else
                                    Все отделы
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li><a class="dropdown-item {{ !request('department_id') ? 'active' : '' }}" href="#"
                                   data-value="">Все отделы</a></li>
                            @foreach($departments as $dept)
                                <li><a class="dropdown-item {{ request('department_id') == $dept->id ? 'active' : '' }}"
                                       href="#" data-value="{{ $dept->id }}">{{ $dept->name }}</a></li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="department_id" class="custom-select-input"
                               value="{{ request('department_id') }}">
                    </div>


                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown">
                            <span class="selected-text">
                                @if(request('position_id'))
                                    {{ $positions->find(request('position_id'))->name ?? 'Все должности' }}
                                @else
                                    Все должности
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li><a class="dropdown-item {{ !request('position_id') ? 'active' : '' }}" href="#"
                                   data-value="">Все должности</a></li>
                            @foreach($positions as $pos)
                                <li><a class="dropdown-item {{ request('position_id') == $pos->id ? 'active' : '' }}"
                                       href="#" data-value="{{ $pos->id }}">{{ $pos->name }}</a></li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="position_id" class="custom-select-input"
                               value="{{ request('position_id') }}">
                    </div>

                    <div class="dropdown custom-select">
                        <button class="custom-select-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="selected-text">
            @if(request('direction', 'desc') === 'asc')
                Сначала старые
            @else
                Сначала новые
            @endif
        </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu custom-select-menu">
                            <li>
                                <a class="dropdown-item {{ request('direction', 'desc') === 'desc' ? 'active' : '' }}"
                                   href="#"
                                   data-direction="desc">
                                    Сначала новые
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request('direction') === 'asc' ? 'active' : '' }}"
                                   href="#"
                                   data-direction="asc">
                                    Сначала старые
                                </a>
                            </li>
                        </ul>
                        <input type="hidden" name="direction" class="custom-direction-input"
                               value="{{ request('direction', 'desc') }}">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                        <i class="bi bi-funnel"></i> Применить
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn-outline" style="padding: 10px 20px;">
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
                        <th>Создано</th>
                        <th>ФИО</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Отдел</th>
                        <th>Должность</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="date">{{ $user->created_at->format('d.m.y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="equipment-name">
                                    {{ $user->surname }} {{ $user->name }} {{ $user->patronymic }}
                                </a>
                                @if($user->role->name === 'admin')
                                    <span class="role-indicator">(Админ)</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->department->name ?? '—' }}</td>
                            <td>{{ $user->position->name ?? '—' }}</td>
                            <td>
                                @php
                                    $statusValue = $user->status->value ?? $user->status;

                                    $statusClass = match($statusValue) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'blocked' => 'danger',
                                        'rejected' => 'neutral',
                                        default => 'info'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
        {{ \App\Http\Enums\UserStatus::ruValues()[$statusValue] ?? $statusValue }}
    </span>
                            </td>
                            <td>
                                <button class="action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal{{ $user->id }}"
                                        title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                @if($user->status->value !== 'pending' && $user->role->name !== 'admin')
                                    <button class="action-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#makeAdminModal{{ $user->id }}"
                                            title="Сделать администратором">
                                        <i class="bi bi-shield-plus"></i>
                                    </button>
                                @elseif($user->status->value !== 'pending' && $user->role->name === 'admin')
                                    @if($user->id !== auth()->id())
                                        <button class="action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#removeAdminModal{{ $user->id }}"
                                                title="Снять права администратора">
                                            <i class="bi bi-shield-slash"></i>
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-0 border-bottom-0">
                                <div class="empty-state">
                                    <div class="empty-icon-wrapper"><i class="bi bi-inbox"></i></div>
                                    <h4 class="empty-title">Нет сотрудников</h4>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="pagination-wrapper">{{ $users->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>

    @foreach($users as $user)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Редактировать сотрудника</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.users.update.quick', $user) }}" method="POST" class="edit-user-form">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <p class="text-secondary mb-3">
                                <strong>{{ $user->surname }} {{ $user->name }}</strong><br>
                                <small>{{ $user->email }}</small>
                            </p>

                            <div class="mb-3">
                                <label class="form-label">Статус</label>
                                <select name="status" class="form-control-custom custom-dark-select">
                                    @php $currentStatus = $user->status->value ?? $user->status; @endphp

                                    @if($currentStatus === 'pending')
                                        <option value="pending" selected>Ожидает подтверждения</option>
                                        <option value="active">Активировать</option>
                                        <option value="rejected">Отклонить</option>
                                    @elseif($currentStatus === 'active')
                                        <option value="active" selected>Активен</option>
                                        <option value="blocked">Заблокировать</option>
                                    @elseif($currentStatus === 'blocked')
                                        <option value="blocked" selected>Заблокирован</option>
                                        <option value="active">Активировать</option>
                                    @elseif($currentStatus === 'rejected')
                                        <option value="rejected" selected>Отклонён</option>
                                        <option value="active">Активировать</option>
                                        <option value="blocked">Заблокировать</option>
                                    @else
                                        @foreach(\App\Http\Enums\UserStatus::cases() as $status)
                                            <option
                                                value="{{ $status->value }}" {{ $currentStatus == $status->value ? 'selected' : '' }}>
                                                {{ \App\Http\Enums\UserStatus::ruValues()[$status->value] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Отдел</label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addDepartmentModal"
                                       style="font-size: 12px; color: var(--accent);">+ Добавить</a>
                                </div>
                                <select name="department_id" class="form-control-custom custom-dark-select">
                                    <option value="">Не назначен</option>
                                    @foreach($departments as $dept)
                                        <option
                                            value="{{ $dept->id }}" {{ $user->department_id == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <label class="form-label mb-0">Должность</label>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#addPositionModal"
                                       style="font-size: 12px; color: var(--accent);">+ Добавить</a>
                                </div>
                                <select name="position_id" class="form-control-custom custom-dark-select">
                                    <option value="">Не назначена</option>
                                    @foreach($positions as $pos)
                                        <option
                                            value="{{ $pos->id }}" {{ $user->position_id == $pos->id ? 'selected' : '' }}>
                                            {{ $pos->name }}
                                        </option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-secondary"
                                   style="font-size: 12px;">
                                    <i class="bi bi-pencil-square"></i> Расширенное редактирование
                                </a>
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

    @endforeach

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

    @foreach($users as $user)
        @if($user->role->name !== 'admin')
            <div class="modal fade" id="makeAdminModal{{ $user->id }}" tabindex="-1" data-bs-backdrop="static">
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
                            <form action="{{ route('admin.users.make-admin', $user) }}" method="POST"
                                  class="make-admin-form">
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
                <div class="modal fade" id="removeAdminModal{{ $user->id }}" tabindex="-1" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title text-danger">
                                    <i class="bi bi-shield-slash me-2"></i>Снятие прав администратора
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
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
                                <form action="{{ route('admin.users.remove-admin', $user) }}" method="POST"
                                      class="remove-admin-form">
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
    @endforeach
@endsection

@push('scripts')
    <script>
        const initLiveSearch = () => {
            const searchInput = document.getElementById('searchUser');
            if (!searchInput) return;
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.custom-table tbody tr').forEach(row => {
                    const text = Array.from(row.querySelectorAll('td')).map(td => td.textContent.toLowerCase()).join(' ');
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            });
        };

        document.addEventListener('DOMContentLoaded', () => {
            initCustomSelects();
            initLiveSearch();

            document.querySelectorAll('.edit-user-form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    submitAjaxForm(form, form.closest('.modal').id, {reloadOnSuccess: true});
                });
            });
        });
        document.querySelector('.add-department-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            submitAjaxForm(e.target, 'addDepartmentModal', {
                selectName: 'department_id',
                reloadOnSuccess: false
            });
        });

        document.querySelector('.add-position-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            submitAjaxForm(e.target, 'addPositionModal', {
                selectName: 'position_id',
                reloadOnSuccess: false
            });
        });


    </script>
@endpush
