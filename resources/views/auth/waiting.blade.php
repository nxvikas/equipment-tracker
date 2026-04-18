@extends('layouts.app')

@section('title', 'Заявка на рассмотрении')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/waiting.css') }}">
@endpush

@section('content')
    <div class="waiting-wrapper d-flex align-items-center justify-content-center min-vh-100">
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
                <p class="small mb-3 notification-text">
                    <i class="bi bi-envelope me-1"></i>
                    Мы отправим уведомление на вашу почту после подтверждения
                </p>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('auth.login') }}" class="btn btn-outline-custom">
                        <i class="bi bi-arrow-left me-1"></i> На страницу входа
                    </a>
                    <button type="button" class="btn btn-primary-custom" id="checkStatusBtn" onclick="window.location.reload()">
                        <i class="bi bi-arrow-repeat me-1"></i> Проверить статус
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
