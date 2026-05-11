@extends('layouts.app')

@section('title', 'Моя панель')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/aggregator/admin/dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <div class="employee-dashboard">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title mt-2">Моя панель</h1>
                <p class="dashboard-subtitle">
                    Добро пожаловать, {{ $user->surname }} {{ $user->name }}!
                </p>
            </div>
            <div class="dashboard-actions">
                <a href="{{ url()->current() }}" class="btn-icon" title="Обновить">
                    <i class="bi bi-arrow-repeat"></i>
                </a>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-icon-wrapper green">
                    <i class="bi bi-laptop"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Моё оборудование</span>
                    <span class="stat-number accent">{{ $totalMyEquipment }}</span>
                    <span class="stat-change">единиц в использовании</span>
                </div>
            </div>
        </div>

        <div class="charts-grid-2cols">

            <div class="chart-panel">
                <div class="chart-head">
                    <h3><i class="bi bi-graph-up"></i> Динамика операций</h3>
                    <span class="chart-badge">последние 6 месяцев</span>
                </div>
                <div class="chart-body">
                    @php

                        $hasEmployeeData = !empty($chartAssigned) && !empty($chartReturned) && (array_sum($chartAssigned) + array_sum($chartReturned)) > 0;
                    @endphp

                    @if($hasEmployeeData)
                        <canvas id="employeeStatsChart"></canvas>
                    @else
                        <div class="empty-chart-state">
                            <i class="bi bi-bar-chart" style="font-size: 48px; opacity: 0.5;"></i>
                            <p class="mt-2 text-secondary">Нет данных по операциям</p>
                            <small class="text-secondary">Здесь появится история ваших выдач и возвратов</small>
                        </div>
                    @endif
                </div>
                <div class="chart-legend">
                    <div class="legend-dot green"></div>
                    <span>Выдано техники</span>
                    <div class="legend-dot blue"></div>
                    <span>Возвращено</span>
                </div>
            </div>


            <div class="top-users-card">
                <div class="top-users-header">
                    <div>
                        <h3><i class="bi bi-clock-history"></i> Последние выдачи</h3>
                        <p>Недавние операции с вашим оборудованием</p>
                    </div>
                    <a href="{{ route('employee.equipment') }}" class="link-btn">
                        Всё оборудование <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="top-users-list">
                    @forelse($recentAssigns as $assign)
                        <a href="{{ route('public.equipment', ['id' => $assign->equipment_id, 'from' => 'employee_dashboard']) }}"
                           class="user-rank-item">
                            <div class="user-rank-info">
                                <div class="recent-assign-icon">
                                    <i class="bi bi-laptop"></i>
                                </div>
                                <div class="user-rank-details">
                                    <div class="user-rank-name">{{ $assign->equipment->name ?? '—' }}</div>
                                    <div class="user-rank-dept">
                                        Инв. номер: {{ $assign->equipment->inventory_number ?? '—' }}
                                    </div>
                                </div>
                            </div>
                            <div class="user-rank-count">
                                {{ $assign->created_at->format('d.m.Y') }}
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-secondary py-4">
                            <i class="bi bi-inbox"></i> Нет выдач
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @if($totalRepairEquipment > 0)
            <div class="section-wrapper">
                <div class="section-head">
                    <div>
                        <h2 class="section-title">
                            <i class="bi bi-tools me-2"></i>В ремонте
                        </h2>
                        <p class="section-desc">Техника, которая находится в сервисном центре</p>
                    </div>
                    <a href="{{ route('employee.equipment', ['status' => 'repair']) }}" class="link-btn">
                        Все в ремонте <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const chartMonths = @json($chartMonths);
            const chartAssigned = @json($chartAssigned);
            const chartReturned = @json($chartReturned);

            const ctx = document.getElementById('employeeStatsChart')?.getContext('2d');
            if (ctx && chartMonths.length > 0) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartMonths,
                        datasets: [
                            {
                                label: 'Выдано',
                                data: chartAssigned,
                                backgroundColor: 'rgba(190, 242, 100, 0.7)',
                                borderColor: '#bef264',
                                borderWidth: 1,
                                borderRadius: 8
                            },
                            {
                                label: 'Возвращено',
                                data: chartReturned,
                                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                                borderColor: '#3b82f6',
                                borderWidth: 1,
                                borderRadius: 8
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {color: '#94a3b8', font: {size: 11}}
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17, 20, 31, 0.96)',
                                titleColor: '#f1f5f9',
                                bodyColor: '#94a3b8',
                                borderColor: 'rgba(190, 242, 100, 0.3)',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {stepSize: 1, color: '#94a3b8'},
                                grid: {color: 'rgba(255, 255, 255, 0.05)'}
                            },
                            x: {
                                ticks: {color: '#94a3b8'},
                                grid: {display: false}
                            }
                        }
                    }
                });
            }
        })();
    </script>
@endpush
