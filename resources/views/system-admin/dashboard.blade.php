@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ __('System Administration') }}</h1>
        <p class="text-gray-600 text-sm sm:text-base">{{ __('Manage businesses, subscriptions, and platform settings') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Total Businesses') }}</h3>
                <div class="p-2 bg-blue-100 rounded-lg"><i class="fas fa-store text-blue-600"></i></div>
            </div>
            <p class="text-3xl font-bold text-gray-900" id="sa-total-businesses">{{ number_format($totalBusinesses) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Total Employees') }}</h3>
                <div class="p-2 bg-blue-100 rounded-lg"><i class="fas fa-users text-blue-600"></i></div>
            </div>
            <p class="text-3xl font-bold text-gray-900" id="sa-total-employees">{{ number_format($totalEmployees) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Active Subs') }}</h3>
                <div class="p-2 bg-blue-100 rounded-lg"><i class="fas fa-check-circle text-blue-600"></i></div>
            </div>
            <p class="text-3xl font-bold text-gray-900" id="sa-active-subs">{{ number_format($activeSubscriptions) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Pending Subs') }}</h3>
                <div class="p-2 bg-yellow-100 rounded-lg"><i class="fas fa-clock text-yellow-600"></i></div>
            </div>
            <p class="text-3xl font-bold text-gray-900" id="sa-pending-subs">{{ number_format($pendingSubscriptions) }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">{{ __('Total Revenue') }}</h3>
                <div class="p-2 bg-blue-100 rounded-lg"><i class="fas fa-money-bill-wave text-blue-600"></i></div>
            </div>
            <p class="text-3xl font-bold text-gray-900" id="sa-total-revenue">{{ businessCurrency() }} {{ number_format($totalRevenue) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Subscription Plans -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-box mr-2"></i> {{ __('Subscription Plans') }}
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($plans as $plan)
                        <div class="border border-gray-200 rounded-xl p-4 {{ $plan->is_active ? '' : 'opacity-60' }}">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-900">{{ $plan->name }}</h3>
                                @if(!$plan->is_active)
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                            <p class="text-2xl font-bold text-blue-600 mb-2">
                                {{ businessCurrency() }} {{ number_format($plan->price) }} <span class="text-sm font-normal text-gray-500">/{{ $plan->duration_days }} {{ __('days') }}</span>
                            </p>
                            <p class="text-sm text-gray-600 mb-1">{{ __('Max Employees') }}: {{ $plan->max_employees > 0 ? $plan->max_employees : __('Unlimited') }}</p>
                            <p class="text-xs text-gray-500">{{ $plan->description }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('system-admin.plans') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                        <i class="fas fa-edit mr-1"></i> {{ __('Manage Plans') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-credit-card mr-2"></i> {{ __('Recent Payments') }}
                </h2>
            </div>
            <div class="p-0">
                @forelse($recentPayments as $payment)
                    <div class="px-5 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $payment->subscription?->businessAdmin?->business_name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $payment->subscription?->plan?->name ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-blue-600">{{ businessCurrency() }} {{ number_format($payment->amount) }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->payment_date?->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center text-gray-500 text-sm">{{ __('No payments recorded yet.') }}</div>
                @endforelse
            </div>
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('system-admin.subscriptions') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> {{ __('View All Subscriptions') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Maintenance -->
    <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-tools mr-2"></i> {{ __('Maintenance') }}
            </h2>
        </div>
        <div class="p-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ __('Check Expired Subscriptions') }}</p>
                    <p class="text-sm text-gray-500">{{ __('Manually check and mark subscriptions as expired if their end date has passed. This runs automatically every night at 2 AM.') }}</p>
                </div>
                <form method="POST" action="{{ route('system-admin.run-subscription-check') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm">
                        <i class="fas fa-play mr-2"></i> {{ __('Run Now') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Businesses -->
    <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-store mr-2"></i> {{ __('Recent Businesses') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Business') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Contact') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Registered') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentBusinesses as $biz)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">
                                        {{ substr($biz->business_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $biz->business_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $biz->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $biz->email ?? $biz->username }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $biz->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($biz->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $biz->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('system-admin.businesses.detail', $biz->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-lg text-blue-700 bg-white hover:bg-blue-50 transition-colors">
                                    <i class="fas fa-eye mr-1"></i> {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('No businesses registered yet.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('system-admin.businesses') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                <i class="fas fa-arrow-right mr-1"></i> {{ __('View All Businesses') }}
            </a>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function numberLocale(v) { return Number(v).toLocaleString('en-UG', {minimumFractionDigits:0}); }

    function refreshSysAdminDashboard() {
        fetch('/api/system-admin/dashboard/stats')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                var el = function(id) { return document.getElementById(id); };
                if (el('sa-total-businesses')) el('sa-total-businesses').textContent = numberLocale(d.totalBusinesses);
                if (el('sa-total-employees')) el('sa-total-employees').textContent = numberLocale(d.totalEmployees);
                if (el('sa-active-subs')) el('sa-active-subs').textContent = numberLocale(d.activeSubscriptions);
                if (el('sa-pending-subs')) el('sa-pending-subs').textContent = numberLocale(d.pendingSubscriptions);
                if (el('sa-total-revenue')) el('sa-total-revenue').textContent = window.businessCurrency + ' ' + numberLocale(d.totalRevenue);
            })
            .catch(function() {});
    }

    setInterval(refreshSysAdminDashboard, 60000);
});
</script>
@endsection
