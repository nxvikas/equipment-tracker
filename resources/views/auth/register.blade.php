@extends('layouts.app')

@section('title', 'Регистрация')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
@endpush

@section('content')
    <div class="register-wrapper d-flex align-items-center justify-content-center">

        <div class="register-card">
            <div class="text-center mb-4">
                <div class="logo-text">
                    {{ config('app.company.name') }}
                </div>
                <div class="logo-sub">
                    {{ config('app.company.description') }}
                </div>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                @method('post')


                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-12">
                        <label for="surname" class="form-label-custom">Фамилия *</label>
                        <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-person"></i>
                </span>
                            <input type="text"
                                   name="surname"
                                   id="surname"
                                   class="form-control @error('surname') is-invalid @enderror"
                                   value="{{old('surname')}}">
                        </div>
                        @error('surname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 col-12">
                        <label for="name" class="form-label-custom">Имя *</label>
                        <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-person"></i>
                </span>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}">
                        </div>
                        @error('name')
                        <div class=" invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="mb-3">
                    <label for="patronymic" class="form-label-custom">Отчество</label>
                    <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-person"></i>
            </span>
                        <input type="text"
                               name="patronymic"
                               id="patronymic"
                               class="form-control @error('patronymic') is-invalid @enderror" value="{{old('patronymic')}}">
                    </div>
                    @error('patronymic')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="email" class="form-label-custom">Email *</label>
                    <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>
                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control @error('email') is-invalid @enderror" value="{{old('email')}}">
                    </div>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="phone" class="form-label-custom">Телефон *</label>
                    <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-telephone"></i>
            </span>
                        <input type="tel"
                               name="phone"
                               id="phone"
                               class="form-control @error('phone') is-invalid @enderror" value="{{old('phone')}}">
                    </div>
                    <small class="form-hint">Формат: +7XXXXXXXXXX или 8XXXXXXXXXX (10 цифр после кода)</small>
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="password" class="form-label-custom">Пароль *</label>
                    <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror">
                        <button class="btn btn-outline-secondary border-start-0"
                                type="button"
                                id="togglePassword"
                                style="border-color: var(--border); border-radius: 0 14px 14px 0; background: rgba(255,255,255,0.04); color: var(--text-secondary);">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <small class="form-hint">Минимум 8 символов, только латиница и цифры</small>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="password_confirmation" class="form-label-custom">Подтверждение пароля *</label>
                    <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-shield-check"></i>
            </span>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control">
                        <button class="btn btn-outline-secondary border-start-0"
                                type="button"
                                id="togglePasswordConfirm"
                                style="border-color: var(--border); border-radius: 0 14px 14px 0; background: rgba(255,255,255,0.04); color: var(--text-secondary);">
                            <i class="bi bi-eye" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                </div>


                <button class="btn btn-lg w-100 btn-primary-custom mt-4">
                    <i class="bi bi-send me-2"></i> Отправить заявку
                </button>


                <div class="text-center mt-3 small">
                    <span class="text-secondary">Уже есть аккаунт?</span>
                    <a href="{{ route('auth.login') }}" class="auth-link ms-1">Войти</a>
                </div>
            </form>
        </div>

    </div>
@endsection
@push('scripts')
    <script src="{{asset('js/pages/auth/eyePassword.js')}}"></script>
@endpush
