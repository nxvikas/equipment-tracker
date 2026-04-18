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
                    <div class="col-md-6 col-12 position-relative">
                        <label for="surname" class="form-label-custom">Фамилия *</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" name="surname" id="surname"
                                   class="form-control form-control-lg form-control-custom @error('surname') is-invalid @enderror">
                        </div>

                        @error('surname')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                    <div class="col-md-6 col-12 position-relative">
                        <label for="name" class="form-label-custom">Имя *</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" name="name" id="name"
                                   class="form-control form-control-lg form-control-custom @error('name') is-invalid @enderror">
                        </div>

                        @error('name')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 position-relative">
                    <label for="patronymic" class="form-label-custom">Отчество</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="patronymic" id="patronymic"
                               class="form-control form-control-lg form-control-custom @error('patronymic') is-invalid @enderror">
                    </div>

                    @error('patronymic')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>


                <div class="mb-3 position-relative">
                    <label for="email" class="form-label-custom">Email *</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" id="email"
                               class="form-control form-control-lg form-control-custom @error('email') is-invalid @enderror">
                    </div>

                    @error('email')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>


                <div class="mb-3 position-relative">
                    <label for="phone" class="form-label-custom">Телефон *</label>
                    <div class="input-wrapper">
                        <i class="bi bi-telephone input-icon"></i>
                        <input type="tel" name="phone" id="phone"
                               class="form-control form-control-lg form-control-custom @error('phone') is-invalid @enderror">
                    </div>

                    @error('phone')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>


                <div class="mb-3 position-relative">
                    <label for="password" class="form-label-custom">Пароль *</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" id="password"
                               class="form-control form-control-lg form-control-custom @error('password') is-invalid @enderror">
                    </div>

                    @error('password')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>


                <div class="mb-3 position-relative">
                    <label for="password_confirmation" class="form-label-custom">Подтверждение пароля *</label>
                    <div class="input-wrapper">
                        <i class="bi bi-shield-check input-icon"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control form-control-lg form-control-custom @error('password') is-invalid @enderror">
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
