@extends('layouts.app')

@section('title', 'Панель управления')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">
@endpush

@section('content')
    <div class="admin-dashboard">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Панель управления</h1>
                <p class="dashboard-subtitle">Добро пожаловать! Вот сводка по оборудованию и активам</p>
            </div>
            <div class="dashboard-actions">
                <button class="btn-icon" onclick="window.exportReport && exportReport()" title="Экспорт отчета">
                    <i class="bi bi-download"></i>
                </button>
                <button class="btn-icon" onclick="window.location.reload()" title="Обновить">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </div>
        </div>


        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-icon-wrapper blue">
                    <i class="bi bi-cpu"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Всего активов</span>
                    <span class="stat-number">{{ $totalEquipments }}</span>
                    <span class="stat-change">единиц техники</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper green">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">В работе</span>
                    <span class="stat-number accent">{{ $inUseEquipments }}</span>
                    <span class="stat-change">активно используется</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper orange">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">На складе</span>
                    <span class="stat-number">{{ $inStockEquipments }}</span>
                    <span class="stat-change success">готово к выдаче</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper red">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">В ремонте</span>
                    <span class="stat-number">{{ $inRepairEquipments }}</span>
                    <span class="stat-change warning">требуют внимания</span>
                </div>
            </div>
        </div>


        <div class="section-wrapper">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Категории оборудования</h2>
                    <p class="section-desc">Распределение активов по типам</p>
                </div>
            </div>
            <div class="categories-container">
                @forelse($categories as $category)
                    <div class="category-item"
                         onclick="window.location.href='{{ route('admin.equipment', ['category_id' => $category->id,'from'=>'dashboard']) }}'">
                        <div class="category-icon">
                            <i class="bi bi-{{
                                str_contains(strtolower($category->name), 'ноут') ? 'laptop' :
                                (str_contains(strtolower($category->name), 'монитор') ? 'display' :
                                (str_contains(strtolower($category->name), 'принтер') ? 'printer' :
                                (str_contains(strtolower($category->name), 'планшет') ? 'tablet' :
                                (str_contains(strtolower($category->name), 'сетево') ? 'router' :
                                (str_contains(strtolower($category->name), 'перифери') ? 'mouse' : 'grid')))))
                            }}"></i>
                        </div>
                        <div class="category-details">
                            <span class="category-name">{{ $category->name }}</span>
                            <span class="category-count">{{ $category->equipment_count }} <small>ед.</small></span>
                        </div>
                    </div>
                @empty
                    <div class="text-secondary py-3">Нет категорий</div>
                @endforelse
            </div>
        </div>


        <div class="charts-grid">
            <div class="chart-panel">
                <div class="chart-head">
                    <h3><i class="bi bi-pie-chart-fill"></i> Статус активов</h3>
                    <span class="chart-badge">текущее распределение</span>
                </div>
                <div class="chart-body">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-legend">
                    <div class="legend-dot green"></div>
                    <span>Выдано <strong>{{ $inUseEquipments }}</strong></span>
                    <div class="legend-dot blue"></div>
                    <span>На складе <strong>{{ $inStockEquipments }}</strong></span>
                    <div class="legend-dot orange"></div>
                    <span>В ремонте <strong>{{ $inRepairEquipments }}</strong></span>
                    <div class="legend-dot red"></div>
                    <span>Списано <strong>{{ $writtenEquipments }}</strong></span>
                </div>
            </div>

            <div class="chart-panel">
                <div class="chart-head">
                    <h3><i class="bi bi-graph-up"></i> Динамика выдачи</h3>
                    <span class="chart-badge">последние 6 месяцев</span>
                </div>
                <div class="chart-body">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>


        <div class="table-wrapper">
            <div class="table-head">
                <div>
                    <h3><i class="bi bi-clock-history"></i> Последние операции</h3>
                    <p class="table-desc">Недавние действия с оборудованием</p>
                </div>
                <a href="{{ route('admin.history',['from'=>'dashboard']) }}" class="link-btn">
                    Все операции <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                    <tr>
                        <th>Оборудование</th>
                        <th>Инв. номер</th>
                        <th>Ответственный</th>
                        <th>Операция</th>
                        <th>Дата</th>
                        <th>Статус</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($recentOperations as $operation)
                        <tr>
                            <td><span class="equipment-name">{{ $operation->equipment->name ?? '—' }}</span></td>
                            <td class="inv-number">{{ $operation->equipment->inventory_number ?? '—' }}</td>
                            <td>{{ $operation->toUser->name ?? $operation->user->name ?? '—' }}</td>
                            <td>
                                @php
                                    $operationClass = match($operation->action_type) {
                                        'assigned' => 'assign',
                                        'returned' => 'return',
                                        'repair' => 'repair',
                                        'written' => 'repair',
                                        default => 'receive'
                                    };
                                    $operationText = \App\Http\Enums\TypeEquipmentHistory::ruValues()[$operation->action_type] ?? $operation->action_type;
                                @endphp
                                <span class="operation {{ $operationClass }}">{{ $operationText }}</span>
                            </td>
                            <td class="date">{{ $operation->created_at->format('d.m.Y') }}</td>
                            <td>
                                @php
                                    $statusClass = match($operation->new_status) {
                                        'in_use' => 'success',
                                        'in_stock' => 'neutral',
                                        'repair' => 'warning',
                                        'written' => 'danger',
                                        default => 'info'
                                    };
                                    $statusText = \App\Http\Enums\StatusEquipment::ruValues()[$operation->new_status] ?? $operation->new_status;
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText ?? '—' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">
                                <i class="bi bi-inbox"></i> Нет операций
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{asset('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js')}}"></script>
    <script>
        (function () {

            const statusData = {
                in_use: {{ $inUseEquipments }},
                in_stock: {{ $inStockEquipments }},
                repair: {{ $inRepairEquipments }},
                written: {{ $writtenEquipments }}
            };

            const monthlyData = @json($monthlyAssigns);


            const statusCtx = document.getElementById('statusChart')?.getContext('2d');
            if (statusCtx) {
                const total = statusData.in_use + statusData.in_stock + statusData.repair + statusData.written;

                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Выдано', 'На складе', 'В ремонте', 'Списано'],
                        datasets: [{
                            data: [statusData.in_use, statusData.in_stock, statusData.repair, statusData.written],
                            backgroundColor: ['#bef264', '#3b82f6', '#f59e0b', '#ef4444'],
                            borderWidth: 0,
                            hoverOffset: 8,
                            cutout: '68%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17, 20, 31, 0.96)',
                                titleColor: '#f1f5f9',
                                bodyColor: '#94a3b8',
                                borderColor: 'rgba(190, 242, 100, 0.3)',
                                borderWidth: 1,
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }


            const trendCtx = document.getElementById('trendChart')?.getContext('2d');
            if (trendCtx) {
                let labels = [];
                let values = [];

                if (monthlyData.length > 0) {
                    monthlyData.forEach(item => {
                        const [year, month] = item.month.split('-');
                        const date = new Date(year, month - 1);
                        labels.push(date.toLocaleString('ru', { month: 'short' }));
                        values.push(item.count);
                    });
                } else {
                    labels = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'];
                    values = [0, 0, 0, 0, 0, 0];
                }

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Выдано оборудования',
                            data: values,
                            borderColor: '#bef264',
                            backgroundColor: 'rgba(190, 242, 100, 0.05)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: '#bef264',
                            pointBorderColor: '#11141f',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                labels: { color: '#94a3b8', font: { size: 11 } }
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
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#94a3b8' },
                                beginAtZero: true
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#94a3b8' }
                            }
                        }
                    }
                });
            }
        })();
    </script>
@endpush
