{{-- resources/views/auth/waiting.blade.php --}}
@extends('layouts.app')

@section('title', 'Заявка на рассмотрении')

@section('content')
    <div class="waiting-wrapper d-flex align-items-center justify-content-center min-vh-100">
        <div class="waiting-card text-center p-4">
            <div class="mb-4">
                <div class="status-icon mb-3">
                    <i class="bi bi-hourglass-split" style="font-size: 4rem; color: var(--accent);"></i>
                </div>
                <h2 class="mb-3" style="color: var(--text-primary);">Заявка отправлена!</h2>
                <p style="color: var(--text-secondary);">
                    Ваша заявка на регистрацию принята и ожидает подтверждения администратора.
                </p>
                <p style="color: var(--text-secondary);" class="small">
                    Email: <strong style="color: var(--accent);">{{ $user->email }}</strong>
                </p>
            </div>

            <div class="status-card p-3 mb-4" style="background: var(--bg-card); border-radius: 20px; border: 1px solid var(--border);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="color: var(--text-secondary);">Статус заявки:</span>
                    <span class="badge" id="statusBadge" style="background: rgba(190, 242, 100, 0.12); color: var(--accent); padding: 6px 12px; border-radius: 40px;">
                    <i class="bi bi-clock me-1"></i> На рассмотрении
                </span>
                </div>
                <div class="progress mb-2" style="height: 6px; background: rgba(255,255,255,0.05);">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar" style="width: 50%; background: var(--accent);"></div>
                </div>
                <small style="color: var(--text-secondary);" id="statusMessage">
                    Администратор рассмотрит заявку в ближайшее время
                </small>
            </div>

            <div class="mt-4">
                <p class="small mb-3" style="color: var(--text-secondary);">
                    <i class="bi bi-envelope me-1"></i>
                    Мы отправим уведомление на вашу почту после подтверждения
                </p>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('auth.login') }}" class="btn" style="background: transparent; border: 1px solid var(--border); color: var(--text-secondary); border-radius: 40px; padding: 8px 20px;">
                        <i class="bi bi-arrow-left me-1"></i> На страницу входа
                    </a>
                    <button type="button" class="btn" id="checkStatusBtn" style="background: var(--accent); color: #02040a; border-radius: 40px; padding: 8px 20px; font-weight: 600;">
                        <i class="bi bi-arrow-repeat me-1"></i> Проверить статус
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/pages/waiting.js') }}" defer></script>
@endpush
