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
                <h1 class="page-title">Структура компании</h1>
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
                            <th>Название</th>
                            <th>Кол-во сотрудников</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td>{{ $department->id }}</td>
                                <td class="equipment-name">{{ $department->name }}</td>
                                <td>{{ $department->users_count }}</td>
                                <td>
                                    <button class="action-btn" data-bs-toggle="modal"
                                            data-bs-target="#editDepartmentModal{{ $department->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($department->users_count == 0)
                                        <button class="action-btn" data-bs-toggle="modal"
                                                data-bs-target="#deleteDepartmentModal{{ $department->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-0 border-bottom-0">
                                    <div class="empty-state">
                                        <div class="empty-icon-wrapper"><i class="bi bi-inbox"></i></div>
                                        <h4 class="empty-title">Нет отделов</h4></div>
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
                            <th>Название</th>
                            <th>Кол-во сотрудников</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($positions as $position)
                            <tr>
                                <td>{{ $position->id }}</td>
                                <td class="equipment-name">{{ $position->name }}</td>
                                <td>{{ $position->users_count }}</td>
                                <td>
                                    <button class="action-btn" data-bs-toggle="modal"
                                            data-bs-target="#editPositionModal{{ $position->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @if($position->users_count == 0)
                                        <button class="action-btn" data-bs-toggle="modal"
                                                data-bs-target="#deletePositionModal{{ $position->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-0 border-bottom-0">
                                    <div class="empty-state">
                                        <div class="empty-icon-wrapper"><i class="bi bi-inbox"></i></div>
                                        <h4 class="empty-title">Нет должностей</h4></div>
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


    <div class="modal fade" id="addDepartmentModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0"><h5 class="modal-title">Новый отдел</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.departments.store') }}" method="POST" id="addDepartmentForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Название <span
                                    class="text-danger">*</span></label><input type="text" name="name"
                                                                               class="form-control-custom"
                                                                               placeholder="Например: IT-отдел"></div>
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
                <div class="modal-header border-0 pb-0"><h5 class="modal-title">Новая должность</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.positions.store') }}" method="POST" id="addPositionForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Название <span
                                    class="text-danger">*</span></label><input type="text" name="name"
                                                                               class="form-control-custom"
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


    @foreach($departments as $department)
        <div class="modal fade" id="editDepartmentModal{{ $department->id }}" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0"><h5 class="modal-title">Редактировать отдел</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.departments.update', $department) }}" method="POST"
                          class="edit-department-form">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3"><label class="form-label">Название <span
                                        class="text-danger">*</span></label><input type="text" name="name"
                                                                                   class="form-control-custom"
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
                        <div class="modal-header border-0 pb-0"><h5 class="modal-title text-danger"><i
                                    class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center py-4"><i class="bi bi-trash"
                                                                    style="font-size: 48px; color: var(--danger);"></i>
                            <p class="mt-3 mb-0">Вы уверены, что хотите удалить отдел?</p>
                            <p class="text-secondary mt-2"><strong>{{ $department->name }}</strong></p>
                            <p class="text-danger small mt-3"><i class="bi bi-exclamation-circle"></i> Это действие
                                нельзя отменить.</p></div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <form action="{{ route('admin.departments.destroy', $department) }}" method="POST"
                                  class="delete-department-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-primary"
                                        style="background: var(--danger); color: white;"><i class="bi bi-trash"></i>
                                    Удалить
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
                    <div class="modal-header border-0 pb-0"><h5 class="modal-title">Редактировать должность</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.positions.update', $position) }}" method="POST"
                          class="edit-position-form">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3"><label class="form-label">Название <span
                                        class="text-danger">*</span></label><input type="text" name="name"
                                                                                   class="form-control-custom"
                                                                                   value="{{ $position->name }}"></div>
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
                        <div class="modal-header border-0 pb-0"><h5 class="modal-title text-danger"><i
                                    class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center py-4"><i class="bi bi-trash"
                                                                    style="font-size: 48px; color: var(--danger);"></i>
                            <p class="mt-3 mb-0">Вы уверены, что хотите удалить должность?</p>
                            <p class="text-secondary mt-2"><strong>{{ $position->name }}</strong></p>
                            <p class="text-danger small mt-3"><i class="bi bi-exclamation-circle"></i> Это действие
                                нельзя отменить.</p></div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <form action="{{ route('admin.positions.destroy', $position) }}" method="POST"
                                  class="delete-position-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-primary"
                                        style="background: var(--danger); color: white;"><i class="bi bi-trash"></i>
                                    Удалить
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
        const initLiveSearch = (inputId, columnIndex) => {
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
            initCustomSelects();
            initLiveSearch('searchDepartment');
            initLiveSearch('searchPosition');


            const addDeptForm = document.getElementById('addDepartmentForm');
            if (addDeptForm) addDeptForm.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(addDeptForm, 'addDepartmentModal', {reloadOnSuccess: true});
            });


            const addPosForm = document.getElementById('addPositionForm');
            if (addPosForm) addPosForm.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(addPosForm, 'addPositionModal', {reloadOnSuccess: true});
            });

            document.querySelectorAll('.edit-department-form').forEach(f => f.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(f, f.closest('.modal').id, {reloadOnSuccess: true});
            }));

            document.querySelectorAll('.delete-department-form').forEach(f => f.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(f, f.closest('.modal').id, {reloadOnSuccess: true});
            }));

            document.querySelectorAll('.edit-position-form').forEach(f => f.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(f, f.closest('.modal').id, {reloadOnSuccess: true});
            }));

            document.querySelectorAll('.delete-position-form').forEach(f => f.addEventListener('submit', (e) => {
                e.preventDefault();
                submitAjaxForm(f, f.closest('.modal').id, {reloadOnSuccess: true});
            }));
        });
    </script>
@endpush
