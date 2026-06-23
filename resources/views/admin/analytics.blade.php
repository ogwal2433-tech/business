@extends('layouts.app')

@section('content')
<div class="analytics">
    <h1 class="text-2xl font-bold mb-1">{{ __('Analytics') }}</h1>
    <p class="text-gray-500 text-sm mb-6">{{ __('Comprehensive business performance overview') }}</p>

    <div class="flex items-center gap-3 mb-6">
        @if(Auth::user()->planHasFeature('financial_position'))
        <a href="{{ route('admin.financial-position') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('Financial Position') }}
        </a>
        @endif
        <button onclick="location.reload()" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            {{ __('Refresh') }}
        </button>
        <span class="text-xs text-gray-400 ml-auto live-indicator">● {{ __('Auto-refresh every 60s') }}</span>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $totalSales = array_sum(array_column($monthlyComparison, 'sales'));
            $totalExp = array_sum(array_column($monthlyComparison, 'expenses'));
            $netProfit = $totalSales - $totalExp;
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Total Sales (6mo)') }}</p>
            <p class="text-xl font-bold text-blue-600 mt-1" id="a-total-sales">UGX {{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Total Expenses (6mo)') }}</p>
            <p class="text-xl font-bold text-red-600 mt-1" id="a-total-expenses">UGX {{ number_format($totalExp, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Net Profit (6mo)') }}</p>
            <p class="text-xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1" id="a-net-profit">UGX {{ number_format($netProfit, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ __('Top Products') }}</p>
            <p class="text-xl font-bold text-indigo-600 mt-1" id="a-top-count">{{ $topProducts->count() }}</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Sales Trend (30 Days)') }}</h3>
            <div class="chart-box" style="position:relative;height:260px">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Sales vs Expenses (6 Months)') }}</h3>
            <div class="chart-box" style="position:relative;height:260px">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Monthly Sales (12 Months)') }}</h3>
            <div class="chart-box" style="position:relative;height:260px">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ __('Expense Breakdown') }}</h3>
            <div class="chart-box" style="position:relative;height:260px">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Top Selling Products') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-5 py-3">{{ __('Product') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Qty Sold') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($topProducts as $sale)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3">{{ $sale->product?->name ?? __('Unknown') }}</td>
                            <td class="px-5 py-3 text-right font-medium">{{ number_format($sale->total_qty) }}</td>
                            <td class="px-5 py-3 text-right font-medium">UGX {{ number_format($sale->total_revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">{{ __('No sales data yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Sales by Employee') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-5 py-3">{{ __('Employee') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Sales Count') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Total Revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($salesByEmployee as $s)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3">{{ $s->employee?->name ?? __('Unknown') }}</td>
                            <td class="px-5 py-3 text-right font-medium">{{ $s->count }}</td>
                            <td class="px-5 py-3 text-right font-medium">UGX {{ number_format($s->total, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">{{ __('No sales data yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    var gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    var textColor = isDark ? '#9ca3af' : '#6b7280';

    function num(v) { return v || 0; }

    function formatUGX(v) { return 'UGX ' + Number(v).toLocaleString(); }

    // 1. Daily Sales Trend
    new Chart(document.getElementById('dailySalesChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($dailySales, 'date')) !!},
            datasets: [{
                data: {!! json_encode(array_column($dailySales, 'total')) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                fill: true,
                tension: 0.35,
                pointRadius: 2,
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: textColor, font: { size: 10 }, maxTicksLimit: 10 }, grid: { color: gridColor } },
                y: { ticks: { color: textColor, font: { size: 10 }, callback: v => formatUGX(v) }, grid: { color: gridColor } }
            }
        }
    });

    // 2. Sales vs Expenses (bar chart)
    new Chart(document.getElementById('comparisonChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyComparison, 'month')) !!},
            datasets: [
                { label: '{{ __("Sales") }}', data: {!! json_encode(array_column($monthlyComparison, 'sales')) !!}, backgroundColor: '#2563eb', borderRadius: 4 },
                { label: '{{ __("Expenses") }}', data: {!! json_encode(array_column($monthlyComparison, 'expenses')) !!}, backgroundColor: '#ef4444', borderRadius: 4 }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { labels: { color: textColor, font: { size: 11 }, boxWidth: 12 } } },
            scales: {
                x: { ticks: { color: textColor, font: { size: 10 } }, grid: { color: gridColor } },
                y: { ticks: { color: textColor, font: { size: 10 }, callback: v => formatUGX(v) }, grid: { color: gridColor } }
            }
        }
    });

    // 3. Monthly Sales (12 months)
    new Chart(document.getElementById('monthlySalesChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlySales, 'month')) !!},
            datasets: [{
                data: {!! json_encode(array_column($monthlySales, 'total')) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,0.08)',
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: textColor, font: { size: 10 } }, grid: { color: gridColor } },
                y: { ticks: { color: textColor, font: { size: 10 }, callback: v => formatUGX(v) }, grid: { color: gridColor } }
            }
        }
    });

    // 4. Expense Breakdown (doughnut)
    var categories = {!! json_encode(array_keys($expenseCategories)) !!};
    var totals = {!! json_encode(array_values($expenseCategories)) !!};
    var colors = ['#2563eb','#ef4444','#f59e0b','#10b981','#8b5cf6','#ec4899','#14b8a6','#f97316','#6366f1','#84cc16'];

    if (categories.length === 0) { categories = ['{{ __("No expenses") }}']; totals = [1]; colors = ['#e5e7eb']; }

    new Chart(document.getElementById('expenseChart'), {
        type: 'doughnut',
        data: {
            labels: categories,
            datasets: [{ data: totals, backgroundColor: colors.slice(0, categories.length), borderWidth: 2, borderColor: isDark ? '#1f2937' : '#fff' }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 }, padding: 12 } }
            }
        }
    });

    // Auto-refresh summary stats
    setInterval(function() {
        fetch('/api/admin/dashboard/stats')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                var totalSales = 0, totalExp = 0;
                // Estimate 6mo totals from monthly data - fall back to page reload
            })
            .catch(function() {});
    }, 60000);
});
</script>
@endsection
