@extends('layouts.app')

@section('content')
<div class="dashboard">
    <h1 class="page-title">{{ __('Admin Dashboard') }}</h1>
    <p class="page-subtitle">{{ __('Manage products, monitor sales, and oversee your team efficiently.') }}</p>

    <!-- Stats Grid -->
    <section class="card">
        <div class="stats-grid">
            <div class="stat-box">
                <i class="fas fa-calendar-day stat-icon text-blue-600"></i>
                <div>
                    <h3>{{ __("Today's Sales") }}</h3>
                    <p class="stat-value" id="stat-today">UGX {{ number_format($totalSalesToday, 2) }}</p>
                    <p class="stat-sub">
                        <span class="text-blue-600" id="stat-today-admin">{{ __('Admin') }}: UGX {{ number_format($todayAdminSales, 2) }}</span>
                        <span class="mx-1">|</span>
                        <span class="text-green-600" id="stat-today-emp">{{ __('Team') }}: UGX {{ number_format($todayEmployeeSales, 2) }}</span>
                    </p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-chart-line stat-icon text-green-600"></i>
                <div>
                    <h3>{{ __('Monthly Sales') }}</h3>
                    <p class="stat-value" id="stat-monthly">UGX {{ number_format($totalMonthlySales, 2) }}</p>
                    <p class="stat-sub">
                        <span class="text-blue-600" id="stat-monthly-admin">{{ __('Admin') }}: UGX {{ number_format($monthAdminSales, 2) }}</span>
                        <span class="mx-1">|</span>
                        <span class="text-green-600" id="stat-monthly-emp">{{ __('Team') }}: UGX {{ number_format($monthEmployeeSales, 2) }}</span>
                    </p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-wallet stat-icon text-red-600"></i>
                <div>
                    <h3>{{ __('Monthly Expenses') }}</h3>
                    <p class="stat-value" id="stat-expenses">UGX {{ number_format($totalMonthlyExpenses, 2) }}</p>
                    <p class="stat-sub">
                        <span class="text-red-600" id="stat-expenses-admin">{{ __('Admin') }}: UGX {{ number_format($monthAdminExpenses ?? 0, 2) }}</span>
                        <span class="mx-1">|</span>
                        <span class="text-orange-600" id="stat-expenses-emp">{{ __('Team') }}: UGX {{ number_format($monthEmployeeExpenses ?? 0, 2) }}</span>
                    </p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-chart-pie stat-icon text-{{ $netProfit >= 0 ? 'green' : 'red' }}-600"></i>
                <div>
                    <h3>{{ __('Net Profit') }}</h3>
                    <p class="stat-value {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}" id="stat-profit">UGX {{ number_format($netProfit, 2) }}</p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-users stat-icon text-indigo-600"></i>
                <div>
                    <h3>{{ __('My Employees') }}</h3>
                    <p class="stat-value">{{ $totalEmployees }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts Row -->
    <section class="card">
        <div class="charts-grid">
            <div class="chart-box">
                <h2 class="section-title">{{ __('Sales Trend (30 Days)') }}</h2>
                <canvas id="dashboardSalesChart" height="160"></canvas>
            </div>
            <div class="chart-box">
                <h2 class="section-title">{{ __('Expense Breakdown') }}</h2>
                <canvas id="dashboardExpenseChart" height="160"></canvas>
            </div>
        </div>
    </section>

    <!-- Low Stock Products -->
    <section class="card">
        <h2 class="section-title">{{ __('Low Stock Products') }}</h2>
        @if($lowStockProducts->count())
            <ul class="product-list" id="low-stock-list">
                @foreach($lowStockProducts as $product)
                    <li class="product-item">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <span>{{ $product->name }}</span>
                        <span class="badge">{{ $product->quantity }} {{ __('In Stock') }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="empty-text">{{ __('sufficiently stock All products') }}</p>
        @endif
    </section>
</div>

<style>
    .dashboard { display: flex; flex-direction: column; gap: 1.5rem; }
    .page-title { font-size: 1.75rem; font-weight: 700; margin-bottom: .25rem; }
    .page-subtitle { color: #6b7280; font-size: 0.95rem; margin-bottom: 0.5rem; }
    [data-theme="dark"] .page-subtitle { color: #9ca3af; }
    .card {
        background: #fff; color: #1f2937; padding: 1.5rem;
        border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    [data-theme="dark"] .card { background: #1f2937; color: #e5e7eb; }
    .section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; }
    [data-theme="dark"] .section-title { color: #f3f4f6; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; }
    .stat-box {
        background: #f9fafb; border-radius: 10px; padding: 1rem;
        display: flex; align-items: center; gap: 0.75rem;
    }
    [data-theme="dark"] .stat-box { background: rgba(255,255,255,0.04); }
    .stat-icon { font-size: 1.4rem; width: 28px; text-align: center; }
    .stat-box h3 { color: #6b7280; font-size: 0.78rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
    [data-theme="dark"] .stat-box h3 { color: #9ca3af; }
    .stat-value { font-size: 1.15rem; font-weight: 700; margin: 0; }
    .stat-sub { font-size: 0.7rem; margin-top: 2px; line-height: 1.3; }
    [data-theme="dark"] .stat-sub .text-blue-600 { color: #60a5fa; }
    [data-theme="dark"] .stat-sub .text-green-600 { color: #34d399; }
    [data-theme="dark"] .stat-sub .text-red-600 { color: #f87171; }
    [data-theme="dark"] .stat-sub .text-orange-600 { color: #fb923c; }
    .product-list { list-style: none; padding: 0; margin: 0; }
    .product-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.5rem 0; border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    [data-theme="dark"] .product-item { border-color: #374151; }
    [data-theme="dark"] .product-item span { color: #d1d5db; }
    .badge { background: #f97316; color: #fff; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    [data-theme="dark"] .badge { background: #fb923c; color: #111827; }
    .empty-text { font-style: italic; color: #6b7280; }
    [data-theme="dark"] .empty-text { color: #9ca3af; }
    .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 768px) { .charts-grid { grid-template-columns: 1fr; } }
    .chart-box { position: relative; height: 180px; }
    .chart-box canvas { display: block; width: 100% !important; height: 100% !important; max-height: 200px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    var gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    var textColor = isDark ? '#9ca3af' : '#6b7280';
    var chartsInitialized = false;
    var salesChart = null, expenseChart = null;

    function formatUGX(v) { return 'UGX ' + Number(v).toLocaleString('en-UG', {minimumFractionDigits:2}); }

    function numberLocale(v) { return Number(v).toLocaleString('en-UG', {minimumFractionDigits:2}); }

    // ---- Stats refresh ----
    function refreshStats() {
        fetch('/api/admin/dashboard/stats')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                var el = function(id) { return document.getElementById(id); };
                if (el('stat-today')) {
                    el('stat-today').textContent = formatUGX(d.totalSalesToday);
                    el('stat-today-admin').textContent = 'UGX ' + numberLocale(d.todayAdminSales);
                    el('stat-today-emp').textContent = 'UGX ' + numberLocale(d.todayEmployeeSales);
                }
                if (el('stat-monthly')) {
                    el('stat-monthly').textContent = formatUGX(d.totalMonthlySales);
                    el('stat-monthly-admin').textContent = 'UGX ' + numberLocale(d.monthAdminSales);
                    el('stat-monthly-emp').textContent = 'UGX ' + numberLocale(d.monthEmployeeSales);
                }
                if (el('stat-expenses')) {
                    el('stat-expenses').textContent = formatUGX(d.totalMonthlyExpenses);
                    el('stat-expenses-admin').textContent = 'UGX ' + numberLocale(d.monthAdminExpenses);
                    el('stat-expenses-emp').textContent = 'UGX ' + numberLocale(d.monthEmployeeExpenses);
                }
                if (el('stat-profit')) {
                    var profit = d.netProfit;
                    el('stat-profit').textContent = formatUGX(profit);
                    el('stat-profit').className = 'stat-value ' + (profit >= 0 ? 'text-green-600' : 'text-red-600');
                }
            })
            .catch(function() {});
    }

    // ---- Charts refresh ----
    function initCharts(data) {
        if (typeof Chart === 'undefined') return;
        if (chartsInitialized) return;

        var salesCtx = document.getElementById('dashboardSalesChart');
        if (salesCtx) {
            var labels = data.dailyAdminSales.map(function(s) { return s.date; });
            var adminData = data.dailyAdminSales.map(function(s) { return s.total; });
            var empData = data.dailyEmployeeSales.map(function(s) { return s.total; });

            salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: '{{ __("Admin Sales") }}', data: adminData, borderColor: '#2563eb', backgroundColor: 'transparent', tension: 0.35, pointRadius: 3, pointHoverRadius: 6, borderWidth: 2 },
                        { label: '{{ __("Employee Sales") }}', data: empData, borderColor: '#10b981', backgroundColor: 'transparent', tension: 0.35, pointRadius: 3, pointHoverRadius: 6, borderWidth: 2 },
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, labels: { color: textColor, font: { size: 11 }, boxWidth: 12, padding: 12 } }
                    },
                    scales: {
                        x: { ticks: { color: textColor, font: { size: 10 }, maxTicksLimit: 10 }, grid: { color: gridColor } },
                        y: { ticks: { color: textColor, font: { size: 10 }, callback: function(v) { return formatUGX(v); } }, grid: { color: gridColor } }
                    }
                }
            });
        }

        var expCtx = document.getElementById('dashboardExpenseChart');
        if (expCtx) {
            var cats = Object.keys(data.expenseCategories);
            var vals = Object.values(data.expenseCategories);
            var colors = ['#2563eb','#ef4444','#f59e0b','#10b981','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#84cc16'];

            if (cats.length === 0) { cats = ['{{ __("No expenses") }}']; vals = [1]; colors = ['#e5e7eb']; }

            expenseChart = new Chart(expCtx, {
                type: 'doughnut',
                data: {
                    labels: cats,
                    datasets: [{ data: vals, backgroundColor: colors.slice(0, cats.length), borderWidth: 2, borderColor: isDark ? '#1f2937' : '#fff' }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 }, padding: 12 } }
                    }
                }
            });
        }

        // Low stock products
        if (data.lowStockProducts && data.lowStockProducts.length) {
            var lowStockEl = document.getElementById('low-stock-list');
            if (lowStockEl) {
                lowStockEl.innerHTML = data.lowStockProducts.map(function(p) {
                    return '<li class="product-item"><i class="fas fa-exclamation-triangle text-warning"></i><span>' + p.name + '</span><span class="badge">' + p.quantity + ' {{ __("In Stock") }}</span></li>';
                }).join('');
            }
        }

        chartsInitialized = true;
    }

    // ---- Charts refresh (update existing) ----
    function refreshCharts() {
        fetch('/api/admin/dashboard/charts')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!chartsInitialized) {
                    initCharts(data);
                    return;
                }
                // Update sales chart
                if (salesChart) {
                    salesChart.data.labels = data.dailyAdminSales.map(function(s) { return s.date; });
                    salesChart.data.datasets[0].data = data.dailyAdminSales.map(function(s) { return s.total; });
                    salesChart.data.datasets[1].data = data.dailyEmployeeSales.map(function(s) { return s.total; });
                    salesChart.update('none');
                }
                // Update expense chart
                if (expenseChart) {
                    var cats = Object.keys(data.expenseCategories);
                    var vals = Object.values(data.expenseCategories);
                    if (cats.length) {
                        expenseChart.data.labels = cats;
                        expenseChart.data.datasets[0].data = vals;
                    }
                    expenseChart.update('none');
                }
                // Update low stock
                if (data.lowStockProducts && data.lowStockProducts.length) {
                    var lowStockEl = document.getElementById('low-stock-list');
                    if (lowStockEl) {
                        lowStockEl.innerHTML = data.lowStockProducts.map(function(p) {
                            return '<li class="product-item"><i class="fas fa-exclamation-triangle text-warning"></i><span>' + p.name + '</span><span class="badge">' + p.quantity + ' {{ __("In Stock") }}</span></li>';
                        }).join('');
                    }
                }
            })
            .catch(function() {});
    }

    // ---- Initial load and polling ----
    fetch('/api/admin/dashboard/charts')
        .then(function(r) { return r.json(); })
        .then(function(data) { initCharts(data); })
        .catch(function() {});

    setInterval(refreshStats, 30000);
    setInterval(refreshCharts, 60000);
});
</script>
@endsection
