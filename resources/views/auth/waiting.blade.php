@extends('layouts.app')

@section('title', 'Заявка на рассмотрении')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/waiting.css') }}">
@endpush

@section('content')
    <div class="waiting-wrapper d-flex align-items-center justify-content-center min-vh-100">
        <div class="theme-toggle-auth">
            <button class="theme-toggle-btn" id="themeToggleAuth">
                <i class="bi bi-moon"></i>
            </button>
        </div>
        <div class="waiting-card text-center p-4">
            <div class="mb-4">
                <div class="status-icon mb-3">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <h2 class="mb-3">Заявка отправлена!</h2>
                <p class="waiting-text">
                    Ваша заявка на регистрацию принята и ожидает подтверждения администратора.
                </p>
                <p class="small">
                    Email: <strong class="waiting-email">{{ $user->email }}</strong>
                </p>
            </div>

            <div class="status-card p-3 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="status-label">Статус заявки:</span>
                    <span class="status-badge" id="statusBadge">
                        <i class="bi bi-clock me-1"></i> <span id="statusText">На рассмотрении</span>
                    </span>
                </div>
            </div>

            <div class="mt-4">

                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('auth.login') }}" class="btn btn-outline-custom">
                        <i class="bi bi-arrow-left me-1"></i> На страницу входа
                    </a>
                    <button type="button" class="btn btn-primary-custom" id="checkStatusBtn"
                            onclick="window.location.reload()">
                        <i class="bi bi-arrow-repeat me-1"></i> Проверить статус
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
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

            themeToggle.addEventListener('click', function() {
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
