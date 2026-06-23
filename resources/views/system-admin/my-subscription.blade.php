@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto px-3 sm:px-4 lg:px-6 py-4">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ __('My Subscription') }}</h1>
        <p class="text-gray-600 text-sm">{{ __('View and manage your subscription plan') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Current Plan -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-tag mr-2"></i> {{ __('Current Plan') }}
            </h2>
        </div>
        <div class="p-6">
            @if($subscription && $subscription->plan)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $subscription->plan->name }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ businessCurrency() }} {{ number_format($subscription->plan->price) }} / {{ $subscription->plan->duration_days }} {{ __('days') }}</p>
                    </div>
                    <div class="mt-3 sm:mt-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $subscription->status === 'trial' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $subscription->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $subscription->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">{{ __('Started') }}</span>
                        <p class="font-medium text-gray-900">{{ $subscription->start_date?->format('d M Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">{{ __('Expires') }}</span>
                        <p class="font-medium text-gray-900">{{ $subscription->end_date?->format('d M Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">{{ __('Max Employees') }}</span>
                        <p class="font-medium text-gray-900">{{ $subscription->plan->max_employees > 0 ? $subscription->plan->max_employees : __('Unlimited') }}</p>
                    </div>
                </div>
                @if($subscription->plan->features && is_array($subscription->plan->features))
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm font-medium text-gray-700 mb-2">{{ __('Features') }}:</p>
                        <ul class="space-y-1">
                            @foreach($subscription->plan->features as $feature)
                                <li class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-check text-blue-500 mr-2"></i> {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @else
                <div class="text-center py-6">
                    <i class="fas fa-exclamation-circle text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">{{ __('You do not have an active subscription plan.') }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ __('Please contact the system administrator.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Billing History Trigger -->
    <div class="mt-6 flex justify-center">
        <button onclick="openBillingHistoryModal()" class="group relative inline-flex items-center px-8 py-3.5 bg-white border-2 border-blue-600 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white text-sm font-semibold transition-all duration-200 shadow-md hover:shadow-xl">
            <div class="absolute inset-0 rounded-xl bg-blue-600 scale-0 group-hover:scale-100 transition-transform duration-200"></div>
            <span class="relative flex items-center">
                <i class="fas fa-file-invoice-dollar mr-2.5 text-lg"></i>
                {{ __('View Billing Statement') }}
                @if($payments->count() > 0)
                    <span class="ml-2.5 inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 group-hover:bg-white text-white group-hover:text-blue-600 text-xs font-bold transition-colors duration-200">{{ $payments->count() }}</span>
                @endif
            </span>
        </button>
    </div>
</div>

<!-- Billing History Modal -->
<div id="billingHistoryModal" style="display:none" class="fixed inset-0 z-50 flex items-start justify-center bg-black/40 pt-10 pb-10 overflow-y-auto" onclick="if(event.target===this)closeBillingHistoryModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 overflow-hidden animate-fade-in-up" onclick="event.stopPropagation()" style="animation: fadeInUp 0.25s ease-out;">
        <style>
            @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
            @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
            .animate-fade-in { animation: fadeIn 0.2s ease-out; }
        </style>

        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">{{ __('Billing Statement') }}</h3>
                    <p class="text-xs text-blue-200">{{ __('All your payment records in one place') }}</p>
                </div>
            </div>
            <button onclick="closeBillingHistoryModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Plan Usage -->
        @php
            $currentCount = auth()->user()->currentEmployeeCount();
            $planObj = $subscription?->plan;
            $maxEmployees = $planObj?->max_employees;
            $isUnlimited = $maxEmployees === 0;
            $hasPlan = !is_null($maxEmployees);
            $usagePercent = $hasPlan && !$isUnlimited ? min(100, round(($currentCount / max($maxEmployees, 1)) * 100)) : 0;
        @endphp
        @if ($hasPlan)
        <div class="px-6 py-5 border-b border-gray-100">
            <div class="flex items-center justify-between mb-1">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-blue-500"></i> {{ __('Plan Usage') }}
                </h4>
                <span class="text-xs text-gray-400">{{ $planObj->name }}</span>
            </div>
            <div class="flex items-center justify-between text-sm mt-3">
                <span class="text-gray-600">{{ __('Employees') }}</span>
                <span class="font-medium text-gray-900">
                    @if ($isUnlimited)
                        <span class="text-green-600">{{ __('Unlimited') }}</span>
                    @else
                        {{ $currentCount }} / {{ $maxEmployees }}
                        <span class="text-gray-400 ml-1">({{ $usagePercent }}%)</span>
                    @endif
                </span>
            </div>
            @if (!$isUnlimited)
                <div class="w-full bg-gray-200 rounded-full h-2 mt-1.5">
                    <div class="h-2 rounded-full transition-all duration-500 {{ $usagePercent >= 100 ? 'bg-red-500' : ($usagePercent >= 80 ? 'bg-yellow-500' : 'bg-blue-500') }}"
                         style="width: {{ $usagePercent }}%"></div>
                </div>
                @if ($usagePercent >= 100)
                    <p class="text-xs text-red-600 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ __('Employee limit reached.') }}
                        <a href="{{ route('admin.subscription.my') }}#plans" class="underline font-medium">{{ __('Upgrade') }}</a>
                    </p>
                @endif
            @endif
        </div>
        @endif

        <!-- Summary Bar -->
        @php
            $totalPaid = $payments->where('status', 'paid')->sum('amount');
            $lastPayment = $payments->first();
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 border-b border-gray-100">
            <div class="px-6 py-4 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">{{ __('Total Paid') }}</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ businessCurrency() }} {{ number_format($totalPaid) }}</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">{{ __('Transactions') }}</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $payments->count() }}</p>
            </div>
            <div class="px-6 py-4 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">{{ __('Last Payment') }}</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $lastPayment ? $lastPayment->payment_date?->format('d M') ?? $lastPayment->created_at->format('d M') : '-' }}</p>
            </div>
        </div>

        <!-- Payment List -->
        <div class="overflow-y-auto max-h-[50vh]">
            @forelse($payments as $payment)
            <div onclick="openPaymentDetailModal({{ $payment->id }})" class="flex items-center gap-4 px-6 py-4 border-b border-gray-50 hover:bg-gray-50/80 cursor-pointer transition-all duration-150 group">
                <!-- Status Dot -->
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $payment->status === 'paid' ? 'bg-green-100' : 'bg-yellow-100' }}">
                    <i class="fas {{ $payment->status === 'paid' ? 'fa-check-circle text-green-600' : 'fa-clock text-yellow-600' }}"></i>
                </div>
                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-900">{{ $payment->payment_method ? ucfirst($payment->payment_method) : __('Payment') }}</span>
                        @if($payment->reference)
                            <span class="text-xs text-gray-400 font-mono">#{{ $payment->reference }}</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $payment->payment_date?->format('l, d M Y H:i') ?? $payment->created_at->format('l, d M Y H:i') }}</p>
                </div>
                <!-- Amount + Status -->
                <div class="text-right flex-shrink-0">
                    <p class="text-base font-bold {{ $payment->status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ businessCurrency() }} {{ number_format($payment->amount) }}</p>
                    <span class="inline-flex items-center gap-1 text-xs font-medium {{ $payment->status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $payment->status === 'paid' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
                <!-- Chevron -->
                <div class="flex-shrink-0 w-6 text-gray-300 group-hover:text-gray-500 transition-colors">
                    <i class="fas fa-chevron-right text-sm"></i>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 px-6">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <i class="fas fa-receipt text-2xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-medium">{{ __('No billing records yet') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('Payments will appear here once recorded') }}</p>
            </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400">{{ __('Showing') }} {{ $payments->count() }} {{ __('transaction(s)') }}</p>
            <button onclick="closeBillingHistoryModal()" class="text-xs text-gray-500 hover:text-gray-700 font-medium transition-colors">{{ __('Close') }}</button>
        </div>
    </div>
</div>

<!-- Payment Detail Modal -->
<div id="paymentDetailModal" style="display:none" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40" onclick="if(event.target===this)closePaymentDetailModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in-up" onclick="event.stopPropagation()" style="animation: fadeInUp 0.2s ease-out;">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-receipt text-white"></i>
                </div>
                <h3 class="text-lg font-bold text-white">{{ __('Payment Details') }}</h3>
            </div>
            <button onclick="closePaymentDetailModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6" id="paymentDetailBody">
            <div class="flex flex-col items-center justify-center py-10">
                <i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>
                <p class="text-sm text-gray-500 mt-3">{{ __('Loading...') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
var payments = @json($paymentsJson);

function openBillingHistoryModal() {
    document.getElementById('billingHistoryModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeBillingHistoryModal() {
    document.getElementById('billingHistoryModal').style.display = 'none';
    document.body.style.overflow = '';
}

function openPaymentDetailModal(id) {
    var p = payments.find(function(x) { return x.id === id; });
    if (!p) return;

    var statusColor = p.status === 'Paid' ? 'green' : 'yellow';
    var statusIcon = p.status === 'Paid' ? 'fa-check-circle' : 'fa-clock';

    var html =
    '<div class="flex flex-col items-center pb-5 border-b border-gray-100">' +
        '<div class="w-14 h-14 rounded-full bg-' + statusColor + '-100 flex items-center justify-center mb-3">' +
            '<i class="fas ' + statusIcon + ' text-' + statusColor + '-600 text-xl"></i>' +
        '</div>' +
        '<p class="text-2xl font-bold text-gray-900">' + p.amount + '</p>' +
        '<span class="mt-1.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-' + statusColor + '-100 text-' + statusColor + '-700">' +
            '<span class="w-1.5 h-1.5 rounded-full bg-' + statusColor + '-500"></span>' + p.status +
        '</span>' +
    '</div>' +
    '<div class="mt-5 space-y-3.5 text-sm">' +
        '<div class="flex items-center justify-between">' +
            '<span class="text-gray-500"><i class="fas fa-calendar w-5 text-gray-400"></i> {{ __("Date") }}</span>' +
            '<span class="font-medium text-gray-900">' + p.payment_date + '</span>' +
        '</div>' +
        '<div class="flex items-center justify-between">' +
            '<span class="text-gray-500"><i class="fas fa-credit-card w-5 text-gray-400"></i> {{ __("Method") }}</span>' +
            '<span class="font-medium text-gray-900">' + p.payment_method + '</span>' +
        '</div>' +
        '<div class="flex items-center justify-between">' +
            '<span class="text-gray-500"><i class="fas fa-hashtag w-5 text-gray-400"></i> {{ __("Reference") }}</span>' +
            '<span class="font-medium text-gray-900 font-mono text-xs">' + p.reference + '</span>' +
        '</div>' +
        '<div class="flex items-center justify-between">' +
            '<span class="text-gray-500"><i class="fas fa-user w-5 text-gray-400"></i> {{ __("Recorded By") }}</span>' +
            '<span class="font-medium text-gray-900">' + p.recorded_by + '</span>' +
        '</div>' +
        '<div class="flex items-start justify-between">' +
            '<span class="text-gray-500"><i class="fas fa-sticky-note w-5 text-gray-400"></i> {{ __("Notes") }}</span>' +
            '<span class="font-medium text-gray-900 text-right max-w-[60%]">' + p.notes + '</span>' +
        '</div>' +
    '</div>' +
    '<div class="mt-5 pt-4 border-t border-gray-100 text-xs text-gray-400 flex items-center justify-between">' +
        '<span>{{ __("Recorded at") }}: ' + p.created_at + '</span>' +
    '</div>';

    document.getElementById('paymentDetailBody').innerHTML = html;
    document.getElementById('paymentDetailModal').style.display = 'flex';
}

function closePaymentDetailModal() {
    document.getElementById('paymentDetailModal').style.display = 'none';
}
</script>
@endsection
