@extends('layouts.app')

@section('title', 'Мой профиль')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/equipment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/profile.css') }}">
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
                <h1 class="page-title">Мой профиль</h1>
                <p class="page-subtitle">Управление личными данными</p>
            </div>
        </div>

        {{-- ОДНА ФОРМА НА ВСЮ СТРАНИЦУ --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{-- ЛЕВАЯ КОЛОНКА: АВАТАР --}}
                <div class="col-lg-4 col-md-5">
                    <div class="equipment-card">
                        <div class="equipment-card-body text-center">

                            <div class="avatar-container mb-3">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                         alt="Аватар"
                                         class="profile-avatar"
                                         id="avatarPreview">
                                @else
                                    <div class="avatar-placeholder profile-avatar" id="avatarPreview">
                                        <i class="bi bi-person"
                                           style="font-size: 64px; color: var(--text-secondary);"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="avatarInput" class="btn-outline w-100" style="cursor: pointer;">
                                    <i class="bi bi-upload"></i> Выбрать фото
                                </label>
                                <input type="file"
                                       name="avatar"
                                       id="avatarInput"
                                       class="d-none">
                                <small class="form-hint d-block mt-2">Поддерживаемые форматы: JPEG, PNG, JPG, GIF.
                                    Максимум 5MB</small>
                                @error('avatar')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <span
                                    class="status-badge {{ $user->status->value === 'active' ? 'success' : 'warning' }}">
                                    {{ \App\Http\Enums\UserStatus::ruValues()[$user->status->value ?? $user->status] ?? $user->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ПРАВАЯ КОЛОНКА: ДАННЫЕ --}}
                <div class="col-lg-8 col-md-7">
                    <div class="equipment-card">
                        <div class="equipment-card-body">

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Фамилия <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="surname"
                                           class="form-control-custom @error('surname') is-invalid @enderror"
                                           value="{{ old('surname', $user->surname) }}">
                                    @error('surname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Имя <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="name"
                                           class="form-control-custom @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Отчество</label>
                                    <input type="text"
                                           name="patronymic"
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
                                    <input type="email"
                                           name="email"
                                           class="form-control-custom @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}">
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Телефон <span class="text-danger">*</span></label>
                                    <input type="tel"
                                           name="phone"
                                           class="form-control-custom @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $user->phone) }}">
                                    <small class="form-hint">Формат: +7XXXXXXXXXX или 8XXXXXXXXXX</small>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Отдел</label>
                                    <input type="text"
                                           class="form-control-custom"
                                           value="{{ $user->department->name ?? 'Не назначен' }}"
                                           disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Должность</label>
                                    <input type="text"
                                           class="form-control-custom"
                                           value="{{ $user->position->name ?? 'Не назначена' }}"
                                           disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Роль в системе</label>
                                    <input type="text"
                                           class="form-control-custom"
                                           value="{{ $user->role->display_name ?? ($user->role->name === 'admin' ? 'Администратор' : 'Сотрудник') }}"
                                           disabled>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Дата регистрации</label>
                                    <input type="text"
                                           class="form-control-custom"
                                           value="{{ $user->created_at->format('d.m.Y H:i') }}"
                                           disabled>
                                </div>
                            </div>

                            <div class="border-top pt-3 mt-2">
                                <h6 class="mb-3" style="color: var(--accent);">
                                    <i class="bi bi-lock"></i> Смена пароля
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Новый пароль</label>
                                        <input type="password"
                                               name="password"
                                               class="form-control-custom @error('password') is-invalid @enderror"
                                               placeholder="Оставьте пустым, если не хотите менять">
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Подтверждение пароля</label>
                                        <input type="password"
                                               name="password_confirmation"
                                               class="form-control-custom"
                                               placeholder="Повторите новый пароль">
                                    </div>
                                </div>
                                <small class="form-hint">Пароль должен содержать минимум 8 символов (только латиница и
                                    цифры)</small>
                            </div>

                            <div class="mt-4 d-flex justify-content-end gap-3">
                                <button type="submit" class="btn-primary">
                                    <i class="bi bi-save"></i> Сохранить изменения
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // Только предпросмотр аватара (форма отправляется при нажатии "Сохранить изменения")
        document.getElementById('avatarInput')?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = event.target.result;
                    } else {
                        const parent = preview.parentElement;
                        const img = document.createElement('img');
                        img.id = 'avatarPreview';
                        img.className = 'profile-avatar';
                        img.src = event.target.result;
                        parent.replaceChild(img, preview);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
