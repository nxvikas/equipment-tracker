@extends('layouts.app')

@section('title', 'Восстановление пароля')

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
                <div class="logo-sub">Восстановление доступа</div>
            </div>

            @if(session('success'))
                <div class="alert custom-alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

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

                <button class="btn btn-primary-custom w-100 mt-2">Отправить ссылку</button>

                <div class="text-center mt-3">
                    <a href="{{ route('auth.login') }}" class="auth-link"><i class="bi bi-arrow-left"></i> Вернуться ко входу</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
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
