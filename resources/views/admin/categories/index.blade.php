@extends('layouts.app')

@section('title', 'Управление категориями')

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
                <h1 class="page-title mt-2">Категории оборудования</h1>
                <p class="page-subtitle">Управление категориями для классификации техники</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.export.categories') }}" class="btn-outline" title="Экспорт в Excel"
                   style="margin-right: 10px">
                    <i class="bi bi-download"></i> Экспорт
                </a>
                <button class="btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-lg"></i> Добавить категорию
                </button>
            </div>
        </div>


        <div class="filters-bar">
            <form method="GET" action="{{ route('admin.categories.index') }}" id="filterForm"
                  class="d-flex w-100 gap-3 justify-content-between">
                <div class="search-input-wrapper">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           id="searchCategory"
                           class="search-input"
                           placeholder="Поиск по названию...">
                </div>

                <div class="filters-group">

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
                    <a href="{{ route('admin.categories.index') }}" class="btn-outline" style="padding: 10px 20px;">
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
                        <th>Описание</th>
                        <th>Кол-во оборудования</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td class="date">{{ $category->created_at->format('d.m.y H:i') }}</td>
                            <td class="equipment-name">
                                <a href="{{ route('admin.categories.show', $category->id) }}">
                                    {{ $category->name }}
                                </a>
                            </td>
                            <td>{{ $category->description ?: '—' }}</td>
                            <td>{{ $category->equipment_count }}</td>
                            <td>
                                <button class="action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editCategoryModal{{ $category->id }}"
                                        title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($category->equipment_count == 0)
                                    <button class="action-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCategoryModal{{ $category->id }}"
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
                                    <h4 class="empty-title">Нет категорий</h4>
                                    <p class="empty-desc">Добавьте первую категорию для классификации оборудования</p>
                                    <button class="btn-outline mt-3" data-bs-toggle="modal"
                                            data-bs-target="#addCategoryModal">
                                        <i class="bi bi-plus-lg me-2"></i>Добавить категорию
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
                <div class="pagination-wrapper">
                    {{ $categories->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>


    <div class="modal fade" id="addCategoryModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">Новая категория</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.categories.store') }}" method="POST" id="addCategoryForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Название <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control-custom" placeholder="Например: Мониторы">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Описание</label>
                            <textarea name="description" class="form-control-custom" rows="2"
                                      placeholder="Дополнительная информация"></textarea>
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


    @foreach($categories as $category)

        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title">Редактировать категорию</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST"
                          class="edit-category-form">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Название <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control-custom"
                                       value="{{ $category->name }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Описание</label>
                                <textarea name="description" class="form-control-custom"
                                          rows="2">{{ $category->description }}</textarea>
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
            <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" data-bs-backdrop="static">
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
                            <p>Вы уверены, что хотите удалить категорию?</p>
                            <p class="text-secondary"><strong>{{ $category->name }}</strong></p>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                  class="delete-category-form">
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
            const searchInput = document.getElementById('searchCategory');
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
            initLiveSearch();

            const addForm = document.getElementById('addCategoryForm');
            if (addForm) {
                addForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    submitAjaxForm(addForm, 'addCategoryModal', {reloadOnSuccess: true});
                });
            }

            document.querySelectorAll('.edit-category-form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const modalId = form.closest('.modal').id;
                    submitAjaxForm(form, modalId, {reloadOnSuccess: true});
                });
            });

            document.querySelectorAll('.delete-category-form').forEach(form => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const modalId = form.closest('.modal').id;
                    submitAjaxForm(form, modalId, {reloadOnSuccess: true});
                });
            });
        });
    </script>
@endpush
