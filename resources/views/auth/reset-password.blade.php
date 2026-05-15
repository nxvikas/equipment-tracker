@extends('layouts.app')

@section('title', 'Сброс пароля')

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
                <div class="logo-text">{{ config('app.company.name') }}</div>
                <div class="logo-sub">Создание нового пароля</div>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="form-label-custom">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email"
                               name="email"
                               class="form-control form-control-lg @error('email') is-invalid @enderror"
                               value="{{ old('email') }}">
                    </div>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Новый пароль</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
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
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Подтверждение пароля</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control form-control-lg">
                        <button class="btn btn-outline-secondary border-start-0"
                                type="button"
                                id="togglePasswordConfirm"
                                style="border-color: var(--border); border-radius: 0 14px 14px 0; background: rgba(255,255,255,0.04); color: var(--text-secondary);">
                            <i class="bi bi-eye" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                </div>

                <button class="btn btn-primary-custom w-100 mt-2">Сбросить пароль</button>

                <div class="text-center mt-3">
                    <a href="{{ route('auth.login') }}" class="auth-link">← Вернуться ко входу</a>
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
                applyTheme('dark');
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
