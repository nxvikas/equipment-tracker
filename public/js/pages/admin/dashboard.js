// Графики главной страницы дашборда админа

let statusChart = null;
let trendChart = null;

// Круговая диаграмма - статус активов
function initStatusChart() {
    const ctx = document.getElementById('statusChart')?.getContext('2d');
    if (!ctx) return;

    if (statusChart) statusChart.destroy();

    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Выдано', 'На складе', 'В ремонте'],
            datasets: [{
                data: [892, 356, 12],
                backgroundColor: ['#bef264', '#3b82f6', '#f59e0b'],
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
                            const total = 1260;
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

// Линейный график - динамика выдачи
function initTrendChart() {
    const ctx = document.getElementById('trendChart')?.getContext('2d');
    if (!ctx) return;

    if (trendChart) trendChart.destroy();

    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Окт', 'Ноя', 'Дек', 'Янв', 'Фев', 'Мар'],
            datasets: [{
                label: 'Выдано оборудования',
                data: [45, 62, 78, 85, 102, 118],
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
                    ticks: { color: '#94a3b8', stepSize: 30 },
                    title: {
                        display: true,
                        text: 'Количество выдач',
                        color: '#64748b',
                        font: { size: 10 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initStatusChart();
    initTrendChart();
});
