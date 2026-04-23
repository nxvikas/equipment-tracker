@extends('layouts.app')

@section('title', 'Вход')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
@endpush

@section('content')
    <div class="register-wrapper d-flex align-items-center justify-content-center">
        <div class="theme-toggle-auth">
            <button class="theme-toggle-btn" id="themeToggleAuth">
                <i class="bi bi-moon"></i>
            </button>
        </div>

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

                <div class="mb-3">
                    <label for="email" class="form-label-custom">Email</label>
                    <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-envelope"></i>
            </span>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               class="form-control form-control-lg @error('email') is-invalid @enderror">
                    </div>
                    @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="password" class="form-label-custom">Пароль</label>
                    <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-lock"></i>
            </span>
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror">
                        <button class="btn btn-outline-secondary border-start-0"
                                type="button"
                                id="togglePassword"
                                style="border-color: var(--border); border-radius: 0 14px 14px 0; background: rgba(255,255,255,0.04); color: var(--text-secondary);">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
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
                            <input type="checkbox" name="remember" value="1">
                            <span class="remember-check"></span>
                            <span class="small text-secondary">Запомнить меня</span>
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
@push('scripts')
    <script src="{{ asset('js/pages/auth/eyePassword.js') }}"></script>
    <script>
        (function () {
            const themeToggle = document.getElementById('themeToggleAuth');
            if (!themeToggle) return;

            function applyTheme(theme) {
                if (theme === 'light') {
                    document.documentElement.classList.add('light');
                    document.body.classList.add('light');
                } else {
                    document.documentElement.classList.remove('light');
                    document.body.classList.remove('light');
                }
            }

            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light') {
                applyTheme('light');
                themeToggle.innerHTML = '<i class="bi bi-sun"></i>';
            } else {
                themeToggle.innerHTML = '<i class="bi bi-moon"></i>';
            }

            themeToggle.addEventListener('click', function () {
                const isLight = document.documentElement.classList.contains('light');
                if (isLight) {
                    applyTheme('dark');
                    localStorage.setItem('theme', 'dark');
                    this.innerHTML = '<i class="bi bi-moon"></i>';
                } else {
                    applyTheme('light');
                    localStorage.setItem('theme', 'light');
                    this.innerHTML = '<i class="bi bi-sun"></i>';
                }
            });
        })();
    </script>
@endpush
