@extends('layouts.app')

@section('title', 'Структура компании')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/equipment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/tabs.css') }}">
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
                <h1 class="page-title mt-2">Структура компании</h1>
                <p class="page-subtitle">Управление отделами и должностями</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.export.structure') }}" class="btn-outline" title="Экспорт в Excel">
                    <i class="bi bi-download"></i> Экспорт
                </a>
            </div>
        </div>


        <div class="d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs-custom" id="structureTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab == 'departments' ? 'active' : '' }}"
                       href="{{ route('admin.structure.index', ['tab' => 'departments']) }}">
                        <i class="bi bi-building me-2"></i>Отделы
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab == 'positions' ? 'active' : '' }}"
                       href="{{ route('admin.structure.index', ['tab' => 'positions']) }}">
                        <i class="bi bi-person-badge me-2"></i>Должности
                    </a>
                </li>
            </ul>

            @if($activeTab == 'departments')
                <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                    <i class="bi bi-plus-lg"></i> Добавить отдел
                </button>
            @else
                <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                    <i class="bi bi-plus-lg"></i> Добавить должность
                </button>
            @endif
        </div>


        @if($activeTab == 'departments')

            <div class="filters-bar">
                <form method="GET" action="{{ route('admin.structure.index') }}"
                      class="d-flex w-100 gap-3 justify-content-between">
                    <input type="hidden" name="tab" value="departments">

                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchDepartment" class="search-input"
                               placeholder="Поиск по названию...">
                    </div>

                    <div class="filters-group">
                        <div class="dropdown custom-select">
                            <button class="custom-select-btn" type="button" data-bs-toggle="dropdown">
                                <span class="selected-text">
                                    {{ request('direction', 'desc') === 'desc' ? 'Сначала больше сотрудников' : 'Сначала меньше сотрудников' }}
                                </span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu custom-select-menu">
                                <li>
                                    <a class="dropdown-item {{ request('direction', 'desc') === 'desc' ? 'active' : '' }}"
                                       href="#" data-direction="desc">Сначала больше сотрудников</a></li>
                                <li><a class="dropdown-item {{ request('direction') === 'asc' ? 'active' : '' }}"
                                       href="#" data-direction="asc">Сначала меньше сотрудников</a></li>
                            </ul>
                            <input type="hidden" name="direction" class="custom-direction-input"
                                   value="{{ request('direction', 'desc') }}">
                        </div>

                        <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                            <i class="bi bi-funnel"></i> Применить
                        </button>
                        <a href="{{ route('admin.structure.index', ['tab' => 'departments']) }}" class="btn-outline"
                           style="padding: 10px 20px;">
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
                            <th>Название</th>
                            <th>Должности</th>
                            <th>Кол-во сотрудников</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td>{{ $department->id }}</td>
                                <td class="date">{{ $department->created_at->format('d.m.y H:i') }}</td>
                                <td class="equipment-name">{{ $department->name }}</td>
                                <td style="max-width: 300px;">
                                    @if($department->positions->count() > 0)
                                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                            @foreach($department->positions->take(5) as $position)
                                                <span
                                                    style="background: rgba(190, 242, 100, 0.08); padding: 2px 8px; border-radius: 12px; font-size: 11px; color: var(--accent);">
                                                    {{ $position->name }}
                                                </span>
                                            @endforeach
                                            @if($department->positions->count() > 5)
                                                <span
                                                    style="background: rgba(255, 255, 255, 0.05); padding: 2px 8px; border-radius: 12px; font-size: 11px; color: var(--text-secondary);">
                                                    +{{ $department->positions->count() - 5 }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-secondary">—</span>
                                    @endif
                                </td>
                                <td>{{ $department->users_count }}</td>
                                <td style="white-space: nowrap;">
                                    @if($department->users_count > 0)
                                        <button class="action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewDepartmentUsersModal{{ $department->id }}"
                                                title="Список сотрудников">
                                            <i class="bi bi-people"></i>
                                        </button>
                                    @endif



                                    @if($department->users_count == 0)
                                        <button class="action-btn" data-bs-toggle="modal"
                                                data-bs-target="#deleteDepartmentModal{{ $department->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                    <button class="action-btn" data-bs-toggle="modal"
                                            data-bs-target="#editDepartmentModal{{ $department->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-0 border-bottom-0">
                                    <div class="empty-state">
                                        <div class="empty-icon-wrapper"><i class="bi bi-inbox"></i></div>
                                        <h4 class="empty-title">Нет отделов</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($departments->hasPages())
                    <div class="pagination-wrapper">{{ $departments->appends(request()->query())->links() }}</div>
                @endif
            </div>

        @else

            <div class="filters-bar">
                <form method="GET" action="{{ route('admin.structure.index') }}"
                      class="d-flex w-100 gap-3 justify-content-between">
                    <input type="hidden" name="tab" value="positions">

                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchPosition" class="search-input" placeholder="Поиск по названию...">
                    </div>

                    <div class="filters-group">
                        <div class="dropdown custom-select">
                            <button class="custom-select-btn" type="button" data-bs-toggle="dropdown">
                                <span class="selected-text">
                                    {{ request('pos_direction', 'desc') === 'desc' ? 'Сначала больше сотрудников' : 'Сначала меньше сотрудников' }}
                                </span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu custom-select-menu">
                                <li>
                                    <a class="dropdown-item {{ request('pos_direction', 'desc') === 'desc' ? 'active' : '' }}"
                                       href="#" data-pos-direction="desc">Сначала больше сотрудников</a></li>
                                <li><a class="dropdown-item {{ request('pos_direction') === 'asc' ? 'active' : '' }}"
                                       href="#" data-pos-direction="asc">Сначала меньше сотрудников</a></li>
                            </ul>
                            <input type="hidden" name="pos_direction" class="custom-pos-direction-input"
                                   value="{{ request('pos_direction', 'desc') }}">
                        </div>

                        <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                            <i class="bi bi-funnel"></i> Применить
                        </button>
                        <a href="{{ route('admin.structure.index', ['tab' => 'positions']) }}" class="btn-outline"
                           style="padding: 10px 20px;">
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
                            <th>Название</th>
                            <th>Отдел</th>
                            <th>Кол-во сотрудников</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($positions as $position)
                            <tr>
                                <td>{{ $position->id }}</td>
                                <td class="date">{{ $position->created_at->format('d.m.y H:i') }}</td>
                                <td class="equipment-name">{{ $position->name }}</td>
                                <td>
                                    @if($position->department)
                                        <span
                                            style="background: rgba(190, 242, 100, 0.1); color: var(--accent); padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                            {{ $position->department->name }}
                                        </span>
                                    @else
                                        <span class="text-secondary">—</span>
                                    @endif
                                </td>
                                <td>{{ $position->users_count }}</td>
                                <td style="white-space: nowrap;">
                                    @if($position->users_count > 0)
                                        <button class="action-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewPositionUsersModal{{ $position->id }}"
                                                title="Список сотрудников">
                                            <i class="bi bi-people"></i>
                                        </button>
                                    @endif



                                    @if($position->users_count == 0)
                                        <button class="action-btn" data-bs-toggle="modal"
                                                data-bs-target="#deletePositionModal{{ $position->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                        <button class="action-btn" data-bs-toggle="modal"
                                                data-bs-target="#editPositionModal{{ $position->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-0 border-bottom-0">
                                    <div class="empty-state">
                                        <div class="empty-icon-wrapper"><i class="bi bi-inbox"></i></div>
                                        <h4 class="empty-title">Нет должностей</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                </div>
                @if($positions->hasPages())
                    <div class="pagination-wrapper">{{ $positions->appends(request()->query())->links() }}</div>
                @endif
            </div>
        @endif
    </div>


    @foreach($departments as $department)
        @if($department->users_count > 0)
            <div class="modal fade" id="viewDepartmentUsersModal{{ $department->id }}" tabindex="-1"
                 data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title">
                                <i class="bi bi-people me-2" style="color: var(--accent);"></i>
                                Сотрудники отдела: {{ $department->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            @if($department->users->count() > 0)
                                <div class="table-responsive">
                                    <table class="custom-table" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>Сотрудник</th>
                                            <th>Email</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($department->users as $user)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                                       class="equipment-name" style="text-decoration: underline;">
                                                        {{ $user->surname }} {{ $user->name }}{{ $user->patronymic ? ' ' . $user->patronymic : '' }}
                                                    </a>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-secondary py-4">
                                    <i class="bi bi-inbox"></i> Нет сотрудников
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach


    @foreach($positions as $position)
        @if($position->users_count > 0)
            <div class="modal fade" id="viewPositionUsersModal{{ $position->id }}" tabindex="-1"
                 data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title">
                                <i class="bi bi-people me-2" style="color: var(--accent);"></i>
                                Сотрудники должности: {{ $position->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            @if($position->users->count() > 0)
                                <div class="table-responsive">
                                    <table class="custom-table" style="width: 100%;">
                                        <thead>
                                        <tr>
                                            <th>Сотрудник</th>
                                            <th>Email</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($position->users as $user)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                                       class="equipment-name" style="text-decoration: underline;">
                                                        {{ $user->surname }} {{ $user->name }}{{ $user->patronymic ? ' ' . $user->patronymic : '' }}
                                                    </a>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-secondary py-4">
                                    <i class="bi bi-inbox"></i> Нет сотрудников
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach


    <div class="modal fade" id="addDepartmentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Новый отдел</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.departments.store') }}" method="POST" id="addDepartmentForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control-custom @error('name') is-invalid @enderror"
                                   placeholder="Например: IT-отдел"
                                   value="{{ old('name') }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                <form action="{{ route('admin.positions.store') }}" method="POST" id="addPositionForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   class="form-control-custom @error('name') is-invalid @enderror"
                                   placeholder="Например: Разработчик"
                                   value="{{ old('name') }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Отдел <span class="text-danger">*</span></label>
                            <select name="department_id"
                                    class="form-control-custom custom-dark-select @error('department_id') is-invalid @enderror">
                                <option value="">Выберите отдел</option>
                                @foreach($departments as $department)
                                    <option
                                        value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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


    @foreach($departments as $department)
        <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Редактировать отдел</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.departments.update', $department) }}" method="POST"
                          class="edit-department-form">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Название <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control-custom"
                                       value="{{ $department->name }}">
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
        @if($department->users_count == 0)
            <div class="modal fade" id="deleteDepartmentModal{{ $department->id }}" tabindex="-1"
                 data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title text-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            <p>Вы уверены, что хотите удалить отдел?</p>
                            <p class="text-secondary">
                                <strong>{{ $department->name }}</strong>
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <form action="{{ route('admin.departments.destroy', $department) }}" method="POST"
                                  class="delete-department-form">
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


    @foreach($positions as $position)
        <div class="modal fade" id="editPositionModal{{ $position->id }}" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Редактировать должность</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.positions.update', $position) }}" method="POST"
                          class="edit-position-form">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Название <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control-custom @error('name') is-invalid @enderror"
                                       value="{{ $position->name }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Отдел <span class="text-danger">*</span></label>
                                <select name="department_id"
                                        class="form-control-custom custom-dark-select @error('department_id') is-invalid @enderror">
                                    <option value="">Выберите отдел</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ ($position->department_id == $department->id) ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
        @if($position->users_count == 0)
            <div class="modal fade" id="deletePositionModal{{ $position->id }}" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title text-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Закрыть"></button>
                        </div>
                        <div class="modal-body">
                            <p>Вы уверены, что хотите удалить должность?</p>
                            <p class="text-secondary">
                                <strong>{{ $position->name }}</strong>
                            </p>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <form action="{{ route('admin.positions.destroy', $position) }}" method="POST"
                                  class="delete-position-form">
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
@endsection

@push('scripts')
    <script>
        const initLiveSearch = (inputId) => {
            const searchInput = document.getElementById(inputId);
            if (!searchInput) return;
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.custom-table tbody tr').forEach(row => {
                    const name = row.querySelector('.equipment-name')?.textContent.toLowerCase() || '';
                    row.style.display = name.includes(term) ? '' : 'none';
                });
            });
        };

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initCustomSelects === 'function') initCustomSelects();
            initLiveSearch('searchDepartment');
            initLiveSearch('searchPosition');


            const addDeptForm = document.getElementById('addDepartmentForm');
            if (addDeptForm) {
                addDeptForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (typeof submitAjaxForm === 'function') {
                        submitAjaxForm(addDeptForm, 'addDepartmentModal', {reloadOnSuccess: true});
                    } else {
                        addDeptForm.submit();
                    }
                });
            }


            const addPosForm = document.getElementById('addPositionForm');
            if (addPosForm) {
                addPosForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (typeof submitAjaxForm === 'function') {
                        submitAjaxForm(addPosForm, 'addPositionModal', {reloadOnSuccess: true});
                    } else {
                        addPosForm.submit();
                    }
                });
            }


            document.querySelectorAll('.edit-department-form').forEach(f => {
                f.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (typeof submitAjaxForm === 'function') {
                        submitAjaxForm(f, f.closest('.modal').id, {reloadOnSuccess: true});
                    } else {
                        f.submit();
                    }
                });
            });

            document.querySelectorAll('.delete-department-form').forEach(f => {
                f.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (typeof submitAjaxForm === 'function') {
                        submitAjaxForm(f, f.closest('.modal').id, {reloadOnSuccess: true});
                    } else {
                        f.submit();
                    }
                });
            });


            document.querySelectorAll('.edit-position-form').forEach(f => {
                f.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (typeof submitAjaxForm === 'function') {
                        submitAjaxForm(f, f.closest('.modal').id, {reloadOnSuccess: true});
                    } else {
                        f.submit();
                    }
                });
            });


            document.querySelectorAll('.delete-position-form').forEach(f => {
                f.addEventListener('submit', (e) => {
                    e.preventDefault();
                    if (typeof submitAjaxForm === 'function') {
                        submitAjaxForm(f, f.closest('.modal').id, {reloadOnSuccess: true});
                    } else {
                        f.submit();
                    }
                });
            });
        });
    </script>
@endpush
