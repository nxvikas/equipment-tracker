@extends('layouts.app')

@section('title', 'Вход')
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

            <form method="POST" action="{{ route('auth') }}">
                @csrf
                @method('post')

                <div class="mb-3 position-relative">
                    <label for="email" class="form-label-custom">Email</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               class="form-control form-control-lg form-control-custom @error('email') is-invalid @enderror">
                    </div>

                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="mb-3 position-relative">
                    <label for="password" class="form-label-custom">Пароль</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control form-control-lg form-control-custom @error('password') is-invalid @enderror">
                    </div>

                    @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="d-flex align-items-center justify-content-between mt-2 mb-3">

                    <div class="form-check" style="padding-left: 0 !important;">

                        <label class="remember-wrapper">
                            <input type="checkbox" name="remember">
                            <span class="remember-check"></span>

                            <span class="small text-secondary">
            Запомнить меня
        </span>
                        </label>
                    </div>


                </div>


                <button class="btn btn-lg w-100 btn-primary-custom mt-2">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Войти
                </button>


                <div class="text-center mt-3 small">
                    <span class="text-secondary">Нет аккаунта?</span>

                    <a href="{{ route('auth.register') }}" class="auth-link ms-1">
                        Отправить заявку
                    </a>
                </div>

            </form>

        </div>

    </div>
@endsection
