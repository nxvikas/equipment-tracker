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
                <button class="btn-icon" onclick="window.refreshData && refreshData()" title="Обновить">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-icon-wrapper blue">
                    <i class="bi bi-cpu"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Всего активов</span>
                    <span class="stat-number">1,248</span>
                    <span class="stat-change positive"><i class="bi bi-arrow-up-short"></i> +12 за месяц</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper green">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">В работе</span>
                    <span class="stat-number accent">892</span>
                    <span class="stat-change">активно используется</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper orange">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">На складе</span>
                    <span class="stat-number">356</span>
                    <span class="stat-change success">готово к выдаче</span>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon-wrapper red">
                    <i class="bi bi-tools"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">В ремонте</span>
                    <span class="stat-number">12</span>
                    <span class="stat-change warning">требуют внимания</span>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="section-wrapper">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Категории оборудования</h2>
                    <p class="section-desc">Распределение активов по типам</p>
                </div>
            </div>
            <div class="categories-container">
                <div class="category-item" onclick="window.filterCategory && filterCategory('notebooks')">
                    <div class="category-icon"><i class="bi bi-laptop"></i></div>
                    <div class="category-details">
                        <span class="category-name">Ноутбуки</span>
                        <span class="category-count">342 <small>ед.</small></span>
                    </div>
                </div>
                <div class="category-item" onclick="window.filterCategory && filterCategory('monitors')">
                    <div class="category-icon"><i class="bi bi-display"></i></div>
                    <div class="category-details">
                        <span class="category-name">Мониторы</span>
                        <span class="category-count">187 <small>ед.</small></span>
                    </div>
                </div>
                <div class="category-item" onclick="window.filterCategory && filterCategory('printers')">
                    <div class="category-icon"><i class="bi bi-printer"></i></div>
                    <div class="category-details">
                        <span class="category-name">Принтеры</span>
                        <span class="category-count">56 <small>ед.</small></span>
                    </div>
                </div>
                <div class="category-item" onclick="window.filterCategory && filterCategory('peripherals')">
                    <div class="category-icon"><i class="bi bi-mouse"></i></div>
                    <div class="category-details">
                        <span class="category-name">Периферия</span>
                        <span class="category-count">234 <small>ед.</small></span>
                    </div>
                </div>
                <div class="category-item" onclick="window.filterCategory && filterCategory('network')">
                    <div class="category-icon"><i class="bi bi-router"></i></div>
                    <div class="category-details">
                        <span class="category-name">Сетевое</span>
                        <span class="category-count">78 <small>ед.</small></span>
                    </div>
                </div>
                <div class="category-item" onclick="window.filterCategory && filterCategory('tablets')">
                    <div class="category-icon"><i class="bi bi-tablet"></i></div>
                    <div class="category-details">
                        <span class="category-name">Планшеты</span>
                        <span class="category-count">89 <small>ед.</small></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
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
                    <div class="legend-dot green"></div><span>Выдано <strong>892</strong></span>
                    <div class="legend-dot blue"></div><span>На складе <strong>356</strong></span>
                    <div class="legend-dot orange"></div><span>В ремонте <strong>12</strong></span>
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

        <!-- Recent Activity Table -->
        <div class="table-wrapper">
            <div class="table-head">
                <div>
                    <h3><i class="bi bi-clock-history"></i> Последние операции</h3>
                    <p class="table-desc">Недавние действия с оборудованием</p>
                </div>
                <button class="link-btn" onclick="window.viewAllOperations && viewAllOperations()">
                    Все операции <i class="bi bi-arrow-right"></i>
                </button>
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
                    <tr>
                        <td><span class="equipment-name">MacBook Pro 14"</span></td>
                        <td class="inv-number">INV-992-01</td>
                        <td>К. Станиславский</td>
                        <td><span class="operation assign">Выдача</span></td>
                        <td class="date">25.03.2026</td>
                        <td><span class="status-badge success">Выдано</span></td>
                    </tr>
                    <tr>
                        <td><span class="equipment-name">iPhone 15 Pro</span></td>
                        <td class="inv-number">INV-441-08</td>
                        <td>—</td>
                        <td><span class="operation receive">Поступление</span></td>
                        <td class="date">24.03.2026</td>
                        <td><span class="status-badge neutral">На складе</span></td>
                    </tr>
                    <tr>
                        <td><span class="equipment-name">Dell U2720Q</span></td>
                        <td class="inv-number">INV-203-45</td>
                        <td>А. Петрова</td>
                        <td><span class="operation return">Возврат</span></td>
                        <td class="date">23.03.2026</td>
                        <td><span class="status-badge info">Возврат</span></td>
                    </tr>
                    <tr>
                        <td><span class="equipment-name">Logitech MX Master 3S</span></td>
                        <td class="inv-number">INV-567-12</td>
                        <td>—</td>
                        <td><span class="operation repair">Ремонт</span></td>
                        <td class="date">22.03.2026</td>
                        <td><span class="status-badge warning">В ремонте</span></td>
                    </tr>
                    <tr>
                        <td><span class="equipment-name">iPad Pro 12.9"</span></td>
                        <td class="inv-number">INV-789-34</td>
                        <td>Д. Волков</td>
                        <td><span class="operation assign">Выдача</span></td>
                        <td class="date">21.03.2026</td>
                        <td><span class="status-badge success">Выдано</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js')}}"></script>
    <script src="{{ asset('js/pages/dashboard.js') }}"></script>
@endpush
