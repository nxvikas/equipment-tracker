@extends('layouts.app')

@section('title', 'Мой профиль')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/aggregator/admin/equipment.css') }}">
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
                <h1 class="page-title mt-2">Мой профиль</h1>
                <p class="page-subtitle">Управление личными данными</p>
            </div>
        </div>


        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">

                <div class="col-lg-4 col-md-5">
                    <div class="equipment-card">
                        <div class="equipment-card-body text-center">

                            <div class="avatar-container mb-3">
                                @php
                                    $avatarSrc = null;
                                    if (old('_avatar_preview')) {
                                        $avatarSrc = old('_avatar_preview');
                                    } elseif ($user->avatar) {
                                        $avatarSrc = asset('storage/' . $user->avatar);
                                    }
                                @endphp

                                @if($avatarSrc)
                                    <img src="{{ $avatarSrc }}"
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

                            <div class="mb-3 d-flex gap-2 justify-content-center">
                                <label for="avatarInput" class="btn-outline" style="cursor: pointer;">
                                    <i class="bi bi-upload"></i> Выбрать фото
                                </label>
                                <input type="file"
                                       name="avatar"
                                       id="avatarInput"
                                       class="d-none"
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <input type="hidden" name="_avatar_preview" id="avatarPreviewInput"
                                       value="{{ old('_avatar_preview') }}">

                                @if($user->avatar)
                                    <button type="button" class="btn-outline text-danger" id="removeAvatarBtn"
                                            style="border-color: rgba(239, 68, 68, 0.3);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteAvatarModal">
                                        <i class="bi bi-trash"></i> Удалить
                                    </button>
                                @endif
                            </div>


                            <small class="form-hint d-block mt-2">Поддерживаемые форматы: JPEG, PNG, JPG, GIF. Максимум
                                5MB</small>
                            @error('avatar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror

                            <div class="mt-3">
                                <span
                                    class="status-badge {{ $user->status->value === 'active' ? 'success' : 'warning' }}">
                                    {{ \App\Http\Enums\UserStatus::ruValues()[$user->status->value ?? $user->status] ?? $user->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

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
                                <p class="mb-3" style="color: var(--accent);">
                                    <i class="bi bi-lock"></i> Смена пароля
                                </p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Новый пароль</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-lock"></i>
                                            </span>
                                            <input type="password"
                                                   name="password"
                                                   id="password"
                                                   class="form-control @error('password') is-invalid @enderror"
                                                   placeholder="Оставьте пустым, если не хотите менять">
                                            <button class="btn btn-outline-secondary border-start-0"
                                                    type="button"
                                                    id="togglePassword"
                                                    style="border-color: var(--border); border-radius: 0 14px 14px 0; background: rgba(255,255,255,0.04); color: var(--text-secondary);">
                                                <i class="bi bi-eye" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Подтверждение пароля</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-shield-check"></i>
                                            </span>
                                            <input type="password"
                                                   name="password_confirmation"
                                                   id="password_confirmation"
                                                   class="form-control"
                                                   placeholder="Повторите новый пароль">
                                            <button class="btn btn-outline-secondary border-start-0"
                                                    type="button"
                                                    id="togglePasswordConfirm"
                                                    style="border-color: var(--border); border-radius: 0 14px 14px 0; background: rgba(255,255,255,0.04); color: var(--text-secondary);">
                                                <i class="bi bi-eye" id="toggleIconConfirm"></i>
                                            </button>
                                        </div>
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
    @if($user->avatar)
        <div class="modal fade" id="deleteAvatarModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>Подтверждение удаления
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <p>Вы уверены, что хотите удалить фото профиля?</p>
                        <p class="text-secondary">Фото будет удалено без возможности восстановления</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn-outline" data-bs-dismiss="modal">Отмена</button>
                        <form action="{{ route('profile.avatar.destroy') }}" method="POST">
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
    <script src="{{ asset('js/pages/auth/eyePassword.js') }}"></script>
    <script>
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
                    document.getElementById('avatarPreviewInput').value = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

    </script>
@endpush
