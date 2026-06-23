@extends('layouts.app')

@section('content')
<div class="financial-position">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="page-title">{{ __('Financial Position') }}</h1>
            <p class="page-subtitle">{{ __('A simple look at what your business owns, owes, and is worth') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.analytics') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> {{ __('Back to Analytics') }}
            </a>
            <button onclick="location.reload()" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-clockwise"></i> {{ __('Refresh') }}
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <section class="card mb-4">
        <div class="fp-stats-grid">
            <div class="fp-stat-box">
                <div class="fp-stat-icon bg-blue-soft">
                    <i class="bi bi-box-seam text-blue-600"></i>
                </div>
                <div class="fp-stat-body">
                    <p class="fp-stat-label">{{ __('Stock Value') }}</p>
                    <p class="fp-stat-value text-blue-600">{{ businessCurrency() }} {{ number_format($inventoryValue, 2) }}</p>
                    <p class="fp-stat-sub">{{ __('Total value of products you have in stock') }}</p>
                </div>
            </div>
            <div class="fp-stat-box">
                <div class="fp-stat-icon bg-amber-soft">
                    <i class="bi bi-credit-card-2-front text-amber-600"></i>
                </div>
                <div class="fp-stat-body">
                    <p class="fp-stat-label">{{ __('Unpaid Customer Credit') }}</p>
                    <p class="fp-stat-value text-amber-600">{{ businessCurrency() }} {{ number_format($outstandingCredit, 2) }}</p>
                    <p class="fp-stat-sub">{{ __('Money customers still owe you') }}</p>
                </div>
            </div>
            <div class="fp-stat-box">
                <div class="fp-stat-icon {{ $netCashFlow >= 0 ? 'bg-green-soft' : 'bg-red-soft' }}">
                    <i class="bi bi-cash-stack {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}"></i>
                </div>
                <div class="fp-stat-body">
                    <p class="fp-stat-label">{{ __("This Month's Cash Flow") }}</p>
                    <p class="fp-stat-value {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ businessCurrency() }} {{ number_format($netCashFlow, 2) }}</p>
                    <p class="fp-stat-sub">{{ __('Sales this month minus expenses this month') }}</p>
                </div>
            </div>
            <div class="fp-stat-box">
                <div class="fp-stat-icon bg-indigo-soft">
                    <i class="bi bi-graph-up-arrow text-indigo-600"></i>
                </div>
                <div class="fp-stat-body">
                    <p class="fp-stat-label">{{ __('Estimated Business Worth') }}</p>
                    <p class="fp-stat-value text-indigo-600">{{ businessCurrency() }} {{ number_format($estNetWorth, 2) }}</p>
                    <p class="fp-stat-sub">{{ __('What you own minus what you have spent') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Business Progress --}}
    <section class="card mb-4">
        <h2 class="section-title mb-0 pb-3 border-bottom">
            {{ __('Business Progress') }}
            <span class="fp-legend-hint">{{ __('How your business is performing and growing over time') }}</span>
        </h2>

        <div class="fp-progress-grid pt-3">
            {{-- Score Gauge --}}
            <div class="fp-gauge-col">
                <div class="fp-gauge-wrapper">
                    <canvas id="progressGauge" width="200" height="200"></canvas>
                    <div class="fp-gauge-center">
                        <span class="fp-gauge-score {{ $progressScore >= 70 ? 'text-green-600' : ($progressScore >= 40 ? 'text-amber-600' : 'text-red-600') }}">{{ $progressScore }}</span>
                        <span class="fp-gauge-label">{{ __('out of 100') }}</span>
                    </div>
                </div>
                <p class="fp-gauge-title">{{ __('Business Health Score') }}</p>
                <p class="fp-gauge-sub">
                    @if($progressScore >= 70)
                        {{ __('Your business is growing well') }}
                    @elseif($progressScore >= 40)
                        {{ __('Your business needs attention') }}
                    @else
                        {{ __('Your business needs improvement') }}
                    @endif
                </p>
            </div>

            {{-- Score Breakdown & Trend --}}
            <div class="fp-score-details">
                <div class="fp-score-row">
                    <div class="fp-score-header">
                        <span class="fp-score-title">{{ __('Profit Margin') }}</span>
                        <span class="fp-score-weight">40pts</span>
                    </div>
                    <div class="fp-score-bar-track">
                        <div class="fp-score-bar-fill profit-fill" style="width: {{ min(100, ($profitMargin / 25) * 100) }}%"></div>
                    </div>
                    <div class="fp-score-footer">
                        <span>{{ $profitMargin }}%</span>
                        <span class="fp-score-pts">+{{ round($scoreFromMargin) }}</span>
                    </div>
                </div>
                <div class="fp-score-row">
                    <div class="fp-score-header">
                        <span class="fp-score-title">{{ __('Sales Trend') }}</span>
                        <span class="fp-score-weight">35pts</span>
                    </div>
                    <div class="fp-score-bar-track">
                        <div class="fp-score-bar-fill sales-fill" style="width: {{ min(100, max(0, ($salesTrendPct / 60) * 100 + 50)) }}%"></div>
                    </div>
                    <div class="fp-score-footer">
                        <span class="{{ $salesTrendPct >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <i class="bi bi-{{ $salesTrendPct >= 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ $salesTrendPct >= 0 ? '+' : '' }}{{ $salesTrendPct }}%
                        </span>
                        <span class="fp-score-pts">+{{ round($scoreFromSales) }}</span>
                    </div>
                </div>
                <div class="fp-score-row">
                    <div class="fp-score-header">
                        <span class="fp-score-title">{{ __('Expense Control') }}</span>
                        <span class="fp-score-weight">25pts</span>
                    </div>
                    <div class="fp-score-bar-track">
                        <div class="fp-score-bar-fill exp-fill" style="width: {{ min(100, max(0, 100 - ($expTrendPct / 60) * 100)) }}%"></div>
                    </div>
                    <div class="fp-score-footer">
                        <span class="{{ $expTrendPct <= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <i class="bi bi-{{ $expTrendPct <= 0 ? 'arrow-down' : 'arrow-up' }}"></i> {{ $expTrendPct >= 0 ? '+' : '' }}{{ $expTrendPct }}%
                        </span>
                        <span class="fp-score-pts">+{{ round($scoreFromExpenses) }}</span>
                    </div>
                </div>
            </div>

            {{-- Direction Summary --}}
            <div class="fp-direction-col">
                <div class="fp-direction-card direction-{{ $netWorthGrowth >= 0 ? 'up' : 'down' }}">
                    <p class="fp-direction-label">{{ __('Net Worth Direction') }}</p>
                    <p class="fp-direction-pct {{ $netWorthGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netWorthGrowth >= 0 ? '+' : '' }}{{ $netWorthGrowth }}%
                    </p>
                    <p class="fp-direction-sub">{{ __('over the last 6 months') }}</p>
                </div>
                <div class="fp-direction-card direction-{{ $salesTrendPct >= 0 ? 'up' : 'down' }}">
                    <p class="fp-direction-label">{{ __('Sales Direction') }}</p>
                    <p class="fp-direction-pct {{ $salesTrendPct >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $salesTrendPct >= 0 ? '+' : '' }}{{ $salesTrendPct }}%
                    </p>
                    <p class="fp-direction-sub">{{ __('avg last 3 months vs older 3') }}</p>
                </div>
                <div class="fp-direction-card direction-{{ $expTrendPct <= 0 ? 'up' : 'down' }}">
                    <p class="fp-direction-label">{{ __('Expense Direction') }}</p>
                    <p class="fp-direction-pct {{ $expTrendPct <= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $expTrendPct >= 0 ? '+' : '' }}{{ $expTrendPct }}%
                    </p>
                    <p class="fp-direction-sub">{{ __('expenses going down is good') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Net Worth Trend Chart --}}
    <section class="card mb-4">
        <h3 class="section-title mb-3">{{ __('Net Worth Trend (6 Months)') }}</h3>
        <div class="fp-chart-single" style="max-width:700px;margin:0 auto;">
            <div class="chart-box" style="position:relative;height:220px;">
                <canvas id="netWorthChart"></canvas>
            </div>
        </div>
    </section>

    {{-- Where Your Money Is --}}
    <section class="card mb-4">
        <h2 class="section-title mb-0 pb-3 border-bottom">
            {{ __('Where Your Money Is') }}
            <span class="fp-legend-hint">{{ __('A simple breakdown of what you own, what customers owe you, and what your business is worth') }}</span>
        </h2>

        <div class="fp-legend-box mt-3 mb-3">
            <div class="fp-legend-row">
                <span class="fp-legend-dot dot-blue"></span>
                <span><strong>{{ __('What You Own (Assets)') }}</strong> — {{ __('Everything your business has of value — your stock and the total sales you have ever made') }}</span>
            </div>
            <div class="fp-legend-row">
                <span class="fp-legend-dot dot-amber"></span>
                <span><strong>{{ __('What Customers Owe You (Liabilities)') }}</strong> — {{ __('Money that customers still have not paid you') }}</span>
            </div>
            <div class="fp-legend-row">
                <span class="fp-legend-dot dot-indigo"></span>
                <span><strong>{{ __('Your Business Worth (Equity)') }}</strong> — {{ __('What is left after taking what you own and subtracting what you have spent in total') }}</span>
            </div>
        </div>

        <div class="fp-balance-grid pt-1">
            <div class="fp-balance-col">
                <h3 class="fp-balance-heading text-blue-600">
                    <i class="bi bi-building"></i> {{ __('What You Own') }}
                    <span class="fp-term-badge">{{ __('Assets') }}</span>
                    <i class="bi bi-question-circle fp-tip-icon" data-tip="{{ __('Everything your business has of value — your stock and the total sales you have ever made') }}"></i>
                </h3>
                <div class="fp-balance-row">
                    <span class="fp-balance-label">
                        {{ __('Stock Value') }}
                        <i class="bi bi-question-circle fp-tip-icon-sm" data-tip="{{ __('Total value of products you currently have in stock') }}"></i>
                    </span>
                    <span class="fp-balance-amount">{{ businessCurrency() }} {{ number_format($inventoryValue, 2) }}</span>
                </div>
                <div class="fp-balance-row">
                    <span class="fp-balance-label">
                        {{ __('Total Sales Ever Made') }}
                        <i class="bi bi-question-circle fp-tip-icon-sm" data-tip="{{ __('All the money your business has ever earned from sales') }}"></i>
                    </span>
                    <span class="fp-balance-amount">{{ businessCurrency() }} {{ number_format($allTimeSales, 2) }}</span>
                </div>
                <div class="fp-balance-row fp-balance-total">
                    <span class="fp-balance-label fw-bold">{{ __('Total Value of What You Own') }}</span>
                    @php $totalAssets = $inventoryValue + $allTimeSales; @endphp
                    <span class="fp-balance-amount fw-bold">{{ businessCurrency() }} {{ number_format($totalAssets, 2) }}</span>
                </div>
            </div>

            <div class="fp-balance-divider"></div>

            <div class="fp-balance-col">
                <h3 class="fp-balance-heading text-amber-600">
                    <i class="bi bi-credit-card"></i> {{ __('What Customers Owe You') }}
                    <span class="fp-term-badge">{{ __('Liabilities') }}</span>
                    <i class="bi bi-question-circle fp-tip-icon" data-tip="{{ __('Money that customers still have not paid you') }}"></i>
                </h3>
                <div class="fp-balance-row">
                    <span class="fp-balance-label">
                        {{ __('Unpaid Customer Credit') }}
                        <i class="bi bi-question-circle fp-tip-icon-sm" data-tip="{{ __('Total amount customers bought on credit but have not paid yet') }}"></i>
                    </span>
                    <span class="fp-balance-amount">{{ businessCurrency() }} {{ number_format($outstandingCredit, 2) }}</span>
                </div>
                <div class="fp-balance-row fp-balance-total">
                    <span class="fp-balance-label fw-bold">{{ __('Total Money Owed to You') }}</span>
                    <span class="fp-balance-amount fw-bold">{{ businessCurrency() }} {{ number_format($outstandingCredit, 2) }}</span>
                </div>
            </div>

            <div class="fp-balance-divider"></div>

            <div class="fp-balance-col">
                <h3 class="fp-balance-heading text-indigo-600">
                    <i class="bi bi-piggy-bank"></i> {{ __('Your Business Worth') }}
                    <span class="fp-term-badge">{{ __('Equity') }}</span>
                    <i class="bi bi-question-circle fp-tip-icon" data-tip="{{ __('What your business is worth after accounting for what you own and what you have spent') }}"></i>
                </h3>
                <div class="fp-balance-row">
                    <span class="fp-balance-label">
                        {{ __('Total Costs Ever Spent') }}
                        <i class="bi bi-question-circle fp-tip-icon-sm" data-tip="{{ __('All the money your business has ever spent on expenses') }}"></i>
                    </span>
                    <span class="fp-balance-amount">{{ businessCurrency() }} {{ number_format($allTimeExpenses, 2) }}</span>
                </div>
                <div class="fp-balance-row fp-balance-total">
                    <span class="fp-balance-label fw-bold">{{ __('Estimated Business Value') }}</span>
                    <span class="fp-balance-amount fw-bold text-indigo-600">{{ businessCurrency() }} {{ number_format($estNetWorth, 2) }}</span>
                </div>
                <p class="fp-balance-formula mt-2">{{ __('What You Own minus Total Costs = Your Business Worth') }}</p>
            </div>
        </div>
    </section>

    {{-- Assets vs Liabilities Chart --}}
    <section class="card">
        <h3 class="section-title mb-3">{{ __('What You Own vs What Customers Owe You') }}</h3>
        <div class="fp-chart-single">
            <div class="chart-box" style="position:relative;height:260px;max-width:480px;margin:0 auto;">
                <canvas id="assetsLiabilitiesChart"></canvas>
            </div>
        </div>
    </section>
</div>

<style>
    .financial-position { display: flex; flex-direction: column; gap: 0; }
    .financial-position .page-title { font-size: 1.75rem; font-weight: 700; margin-bottom: .25rem; }
    .financial-position .page-subtitle { color: #6b7280; font-size: 0.9rem; margin-bottom: 0; }
    [data-theme="dark"] .financial-position .page-subtitle { color: #9ca3af; }
    .financial-position .card {
        background: #fff; color: #1f2937; padding: 1.5rem;
        border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); margin-bottom: 1.25rem;
    }
    [data-theme="dark"] .financial-position .card { background: #1f2937; color: #e5e7eb; }
    .financial-position .section-title { font-size: 1.1rem; font-weight: 600; display: flex; flex-direction: column; gap: 0.25rem; }
    [data-theme="dark"] .financial-position .section-title { color: #f3f4f6; }
    .fp-legend-hint { font-size: 0.78rem; font-weight: 400; color: #6b7280; }
    [data-theme="dark"] .fp-legend-hint { color: #9ca3af; }

    /* ---- Summary Stats Grid ---- */
    .fp-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
    @media (max-width: 992px) { .fp-stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .fp-stats-grid { grid-template-columns: 1fr; } }
    .fp-stat-box {
        background: #f9fafb; border-radius: 12px; padding: 1.25rem;
        display: flex; align-items: flex-start; gap: 1rem;
    }
    [data-theme="dark"] .fp-stat-box { background: rgba(255,255,255,0.04); }
    .fp-stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        font-size: 1.25rem;
    }
    .fp-stat-body { flex: 1; min-width: 0; }
    .fp-stat-label { color: #6b7280; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 2px; }
    [data-theme="dark"] .fp-stat-label { color: #9ca3af; }
    .fp-stat-value { font-size: 1.25rem; font-weight: 700; margin: 0; line-height: 1.3; }
    .fp-stat-sub { font-size: 0.7rem; color: #9ca3af; margin-top: 2px; }
    [data-theme="dark"] .fp-stat-sub { color: #6b7280; }

    /* ---- Color utility classes ---- */
    .bg-blue-soft { background: #dbeafe; }
    .bg-green-soft { background: #d1fae5; }
    .bg-amber-soft { background: #fef3c7; }
    .bg-red-soft { background: #fee2e2; }
    .bg-indigo-soft { background: #e0e7ff; }
    [data-theme="dark"] .bg-blue-soft { background: rgba(37,99,235,0.15); }
    [data-theme="dark"] .bg-green-soft { background: rgba(16,185,129,0.15); }
    [data-theme="dark"] .bg-amber-soft { background: rgba(245,158,11,0.15); }
    [data-theme="dark"] .bg-red-soft { background: rgba(239,68,68,0.15); }
    [data-theme="dark"] .bg-indigo-soft { background: rgba(99,102,241,0.15); }

    .text-blue-600 { color: #2563eb !important; }
    .text-green-600 { color: #16a34a !important; }
    .text-amber-600 { color: #d97706 !important; }
    .text-red-600 { color: #dc2626 !important; }
    .text-indigo-600 { color: #4f46e5 !important; }
    [data-theme="dark"] .text-blue-600 { color: #60a5fa !important; }
    [data-theme="dark"] .text-green-600 { color: #34d399 !important; }
    [data-theme="dark"] .text-amber-600 { color: #fbbf24 !important; }
    [data-theme="dark"] .text-red-600 { color: #f87171 !important; }
    [data-theme="dark"] .text-indigo-600 { color: #818cf8 !important; }
    .fw-bold { font-weight: 700; }

    /* ---- Legend / Key ---- */
    .fp-legend-box {
        background: #f9fafb; border-radius: 10px; padding: 0.85rem 1rem;
        display: flex; flex-direction: column; gap: 0.5rem;
    }
    [data-theme="dark"] .fp-legend-box { background: rgba(255,255,255,0.04); }
    .fp-legend-row { display: flex; align-items: center; gap: 0.6rem; font-size: 0.82rem; color: #4b5563; }
    [data-theme="dark"] .fp-legend-row { color: #d1d5db; }
    .fp-legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .dot-blue { background: #2563eb; }
    .dot-amber { background: #d97706; }
    .dot-indigo { background: #4f46e5; }

    /* ---- Balance Sheet ---- */
    .fp-balance-grid { display: grid; grid-template-columns: 1fr auto 1fr auto 1fr; gap: 0; align-items: start; }
    @media (max-width: 768px) { .fp-balance-grid { grid-template-columns: 1fr; gap: 1rem; } }
    .fp-balance-col { padding: 0 0.5rem; }
    .fp-balance-heading { font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; }
    .fp-term-badge {
        font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;
        padding: 1px 7px; border-radius: 4px; background: #f3f4f6; color: #6b7280;
    }
    [data-theme="dark"] .fp-term-badge { background: rgba(255,255,255,0.08); color: #9ca3af; }
    .fp-balance-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.5rem 0; border-bottom: 1px solid #f3f4f6;
    }
    [data-theme="dark"] .fp-balance-row { border-color: #374151; }
    .fp-balance-row.fp-balance-total { border-bottom: none; border-top: 2px solid #e5e7eb; padding-top: 0.75rem; margin-top: 0.25rem; }
    [data-theme="dark"] .fp-balance-row.fp-balance-total { border-color: #4b5563; }
    .fp-balance-label { font-size: 0.85rem; color: #6b7280; display: flex; align-items: center; gap: 0.3rem; }
    [data-theme="dark"] .fp-balance-label { color: #9ca3af; }
    .fp-balance-amount { font-size: 0.9rem; font-weight: 600; }
    [data-theme="dark"] .fp-balance-amount { color: #f3f4f6; }
    .fp-balance-divider {
        width: 1px; height: 100%; min-height: 120px;
        background: #e5e7eb; align-self: stretch;
    }
    [data-theme="dark"] .fp-balance-divider { background: #374151; }
    @media (max-width: 768px) { .fp-balance-divider { display: none; } }
    .fp-balance-formula { font-size: 0.7rem; color: #9ca3af; font-style: italic; }
    [data-theme="dark"] .fp-balance-formula { color: #6b7280; }

    /* ---- Tooltip Icons ---- */
    .fp-tip-icon { font-size: 0.8rem; color: #9ca3af; cursor: pointer; }
    .fp-tip-icon:hover { color: #4b5563; }
    [data-theme="dark"] .fp-tip-icon { color: #6b7280; }
    [data-theme="dark"] .fp-tip-icon:hover { color: #d1d5db; }
    .fp-tip-icon-sm { font-size: 0.7rem; color: #9ca3af; cursor: pointer; opacity: 0.6; }
    .fp-tip-icon-sm:hover { opacity: 1; color: #4b5563; }
    [data-theme="dark"] .fp-tip-icon-sm { color: #6b7280; }
    [data-theme="dark"] .fp-tip-icon-sm:hover { color: #d1d5db; }

    /* ---- Progress Section ---- */
    .fp-progress-grid { display: grid; grid-template-columns: auto 1fr auto; gap: 2rem; align-items: start; }
    @media (max-width: 992px) { .fp-progress-grid { grid-template-columns: 1fr; gap: 1.5rem; } }
    .fp-gauge-col { text-align: center; }
    .fp-gauge-wrapper { position: relative; width: 180px; height: 180px; margin: 0 auto; }
    .fp-gauge-wrapper canvas { width: 100% !important; height: 100% !important; }
    .fp-gauge-center {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        text-align: center; pointer-events: none;
    }
    .fp-gauge-score { font-size: 2.5rem; font-weight: 800; line-height: 1; display: block; }
    .fp-gauge-label { font-size: 0.65rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.04em; }
    .fp-gauge-title { font-size: 0.9rem; font-weight: 600; margin-top: 0.5rem; margin-bottom: 0; }
    .fp-gauge-sub { font-size: 0.78rem; color: #6b7280; margin-top: 2px; }

    /* Score details */
    .fp-score-details { display: flex; flex-direction: column; gap: 1rem; padding-top: 0.5rem; }
    .fp-score-row { padding: 0.75rem 1rem; background: #f9fafb; border-radius: 8px; }
    [data-theme="dark"] .fp-score-row { background: rgba(255,255,255,0.03); }
    .fp-score-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; }
    .fp-score-title { font-size: 0.85rem; font-weight: 600; }
    .fp-score-weight { font-size: 0.7rem; color: #9ca3af; }
    .fp-score-bar-track { height: 6px; background: #e5e7eb; border-radius: 3px; overflow: hidden; }
    [data-theme="dark"] .fp-score-bar-track { background: #374151; }
    .fp-score-bar-fill { height: 100%; border-radius: 3px; transition: width 0.6s ease; }
    .profit-fill { background: linear-gradient(90deg, #2563eb, #3b82f6); }
    .sales-fill { background: linear-gradient(90deg, #16a34a, #22c55e); }
    .exp-fill { background: linear-gradient(90deg, #d97706, #f59e0b); }
    .fp-score-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 0.35rem; font-size: 0.8rem; }
    .fp-score-pts { font-size: 0.75rem; font-weight: 600; color: #9ca3af; }

    /* Direction cards */
    .fp-direction-col { display: flex; flex-direction: column; gap: 0.75rem; }
    .fp-direction-card {
        padding: 0.85rem 1rem; border-radius: 10px; background: #f9fafb;
        border-left: 4px solid #d1d5db;
    }
    [data-theme="dark"] .fp-direction-card { background: rgba(255,255,255,0.03); }
    .fp-direction-card.direction-up { border-left-color: #16a34a; }
    .fp-direction-card.direction-down { border-left-color: #dc2626; }
    .fp-direction-label { font-size: 0.7rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.03em; font-weight: 600; margin-bottom: 1px; }
    [data-theme="dark"] .fp-direction-label { color: #9ca3af; }
    .fp-direction-pct { font-size: 1.15rem; font-weight: 700; margin: 0; line-height: 1.3; }
    .fp-direction-sub { font-size: 0.7rem; color: #9ca3af; margin-top: 1px; }
    [data-theme="dark"] .fp-direction-sub { color: #6b7280; }

    /* ---- Chart ---- */
    .fp-chart-single { width: 100%; }
    .chart-box canvas { display: block; width: 100% !important; height: 100% !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    var textColor = isDark ? '#9ca3af' : '#6b7280';
    var gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';

    function formatCurrency(v) { return window.businessCurrency + ' ' + Number(v).toLocaleString(); }

    // 1. Progress Score Gauge (semi-circle)
    var gaugeCtx = document.getElementById('progressGauge');
    if (gaugeCtx) {
        var score = {{ $progressScore }};
        var color = score >= 70 ? '#16a34a' : (score >= 40 ? '#d97706' : '#dc2626');
        var bgColor = isDark ? '#374151' : '#e5e7eb';

        new Chart(gaugeCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [score, 100 - score],
                    backgroundColor: [color, bgColor],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '75%',
                circumference: 270,
                rotation: 225,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    }

    // 2. Net Worth Trend Line Chart
    var nwCtx = document.getElementById('netWorthChart');
    if (nwCtx) {
        var nwMonths = {!! json_encode(array_column($netWorthHistory, 'month')) !!};
        var nwValues = {!! json_encode(array_column($netWorthHistory, 'netWorth')) !!};
        var nwProfits = {!! json_encode(array_column($netWorthHistory, 'profit')) !!};

        new Chart(nwCtx, {
            type: 'line',
            data: {
                labels: nwMonths,
                datasets: [{
                    label: '{{ __("Cumulative Net Worth") }}',
                    data: nwValues,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: function(ctx) {
                        var i = ctx.dataIndex;
                        return nwProfits[i] >= 0 ? '#16a34a' : '#dc2626';
                    },
                    pointBorderColor: isDark ? '#1f2937' : '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                var i = ctx.dataIndex;
                                var p = nwProfits[i];
                                var sign = p >= 0 ? '+' : '';
                                return ctx.parsed.y >= 0 ? window.businessCurrency + ' ' + Number(ctx.parsed.y).toLocaleString() + ' (' + sign + window.businessCurrency + ' ' + Number(p).toLocaleString() + ')' : window.businessCurrency + ' ' + Number(ctx.parsed.y).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: { ticks: { color: textColor, font: { size: 10 } }, grid: { display: false } },
                    y: { ticks: { color: textColor, font: { size: 10 }, callback: function(v) { return window.businessCurrency + ' ' + Number(v/1000).toFixed(0) + 'k'; } }, grid: { color: gridColor } }
                }
            }
        });
    }

    // 3. What You Own vs What Customers Owe You — Doughnut
    var assetsCtx = document.getElementById('assetsLiabilitiesChart');
    if (assetsCtx) {
        var ownVal = {{ $inventoryValue }} + {{ $allTimeSales }};
        var oweVal = {{ $outstandingCredit }};
        var eqVal = {{ $estNetWorth }};

        var total = ownVal + oweVal + eqVal;

        new Chart(assetsCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    '{{ __("What You Own (Assets)") }}',
                    '{{ __("What Customers Owe You (Liabilities)") }}',
                    '{{ __("Your Business Worth (Equity)") }}',
                ],
                datasets: [{
                    data: [ownVal, oweVal, eqVal],
                    backgroundColor: ['#2563eb', '#f59e0b', '#4f46e5'],
                    borderWidth: 2,
                    borderColor: isDark ? '#1f2937' : '#fff',
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 }, padding: 12 } },
                    tooltip: { callbacks: { label: function(ctx) { return ctx.label.split(' (')[0] + ': ' + window.businessCurrency + ' ' + Number(ctx.raw).toLocaleString(); } } }
                }
            }
        });
    }

    // Tooltip functionality
    document.querySelectorAll('[data-tip]').forEach(function(el) {
        el.addEventListener('mouseenter', function(e) {
            var tip = document.createElement('div');
            tip.className = 'fp-custom-tooltip';
            tip.textContent = this.getAttribute('data-tip');
            tip.style.cssText = 'position:fixed;background:#1f2937;color:#fff;font-size:0.78rem;padding:0.5rem 0.75rem;border-radius:6px;max-width:280px;z-index:9999;line-height:1.4;pointer-events:none;';
            document.body.appendChild(tip);
            var rect = this.getBoundingClientRect();
            tip.style.left = rect.left + 'px';
            tip.style.top = (rect.bottom + 6) + 'px';
            this._tooltip = tip;
        });
        el.addEventListener('mouseleave', function() {
            if (this._tooltip) { this._tooltip.remove(); this._tooltip = null; }
        });
    });
});
</script>
@endsection
