@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4">
    <div class="mb-6">
        <a href="{{ route('system-admin.businesses') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to Businesses') }}
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">{{ $business->business_name }}</h1>
                <p class="text-gray-600 text-sm">{{ $business->name }} &middot; {{ $business->email ?? $business->username }}</p>
            </div>
            <form action="{{ route('system-admin.businesses.toggle-status', $business->id) }}" method="POST" onsubmit="return confirm('{{ __('Toggle status for this business?') }}');">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors
                    {{ $business->status === 'active' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                    <i class="fas {{ $business->status === 'active' ? 'fa-ban' : 'fa-check-circle' }} mr-2"></i>
                    {{ $business->status === 'active' ? __('Deactivate') : __('Activate') }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Business Info Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-info-circle mr-2"></i> {{ __('Business Info') }}
                </h2>
            </div>
            <div class="p-5 space-y-4 text-sm">
                <div><span class="text-gray-500">{{ __('Owner') }}:</span> <span class="text-gray-900 font-medium">{{ $business->name }}</span></div>
                <div><span class="text-gray-500">{{ __('Username') }}:</span> <span class="text-gray-900 font-mono">{{ $business->username }}</span></div>
                <div><span class="text-gray-500">{{ __('Email') }}:</span> <span class="text-gray-900">{{ $business->email ?? 'N/A' }}</span></div>
                <div><span class="text-gray-500">{{ __('Employees') }}:</span> <span class="text-gray-900 font-medium">{{ $business->employees_count }}</span></div>
                <div><span class="text-gray-500">{{ __('Status') }}:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $business->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($business->status) }}
                    </span>
                </div>
                <div><span class="text-gray-500">{{ __('Registered') }}:</span> <span class="text-gray-900">{{ $business->created_at->format('d M Y H:i') }}</span></div>
            </div>
        </div>

        <!-- Current Subscription -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-tag mr-2"></i> {{ __('Current Subscription') }}
                </h2>
            </div>
            <div class="p-5">
                @if($business->subscription && $business->subscription->plan)
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900">{{ $business->subscription->plan->name }}</p>
                        <p class="text-sm text-gray-500 mt-1">UGX {{ number_format($business->subscription->plan->price) }} / {{ $business->subscription->plan->duration_days }} {{ __('days') }}</p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $business->subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $business->subscription->status === 'trial' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $business->subscription->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $business->subscription->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $business->subscription->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($business->subscription->status) }}
                            </span>
                        </div>
                        @if($business->subscription->start_date)
                            <p class="text-xs text-gray-500 mt-2">{{ __('Started') }}: {{ $business->subscription->start_date->format('d M Y') }}</p>
                        @endif
                        @if($business->subscription->end_date)
                            <p class="text-xs text-gray-500">{{ __('Expires') }}: {{ $business->subscription->end_date->format('d M Y') }}</p>
                        @endif
                    </div>
                @else
                    <div class="text-center text-gray-500 text-sm py-4">{{ __('No active subscription') }}</div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-bolt mr-2"></i> {{ __('Quick Actions') }}
                </h2>
            </div>
            <div class="p-5 space-y-3">
                <button onclick="openAssignModal({{ $business->id }}, '{{ $business->business_name }}')" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                    <i class="fas fa-tag mr-2"></i> {{ __('Assign Subscription') }}
                </button>
                @if($business->subscription)
                <button onclick="openPaymentModal({{ $business->subscription->id }}, '{{ $business->business_name }}')" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                    <i class="fas fa-credit-card mr-2"></i> {{ __('Record Payment') }}
                </button>
                @else
                <button disabled class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-gray-300 text-gray-500 rounded-lg text-sm font-medium cursor-not-allowed">
                    <i class="fas fa-credit-card mr-2"></i> {{ __('Record Payment') }}
                </button>
                <p class="text-xs text-gray-400 text-center">{{ __('Assign a subscription first') }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Subscription History -->
    <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-5 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-history mr-2 text-gray-600"></i> {{ __('Subscription History') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Plan') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Start') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('End') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($business->allSubscriptions as $sub)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $sub->plan?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $sub->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $sub->status === 'trial' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $sub->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $sub->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $sub->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->start_date?->format('d M Y') ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $sub->end_date?->format('d M Y') ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $sub->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">{{ __('No subscription history.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment History -->
    <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-5 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-receipt mr-2 text-gray-600"></i> {{ __('Payment History') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Method') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Reference') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Recorded By') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-blue-600">UGX {{ number_format($payment->amount) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->payment_date?->format('d M Y H:i') ?? $payment->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->payment_method ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ $payment->reference ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->recorder?->name ?? 'System' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">{{ __('No payments recorded.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assign Subscription Modal -->
<div id="assignModal" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="if(event.target===this)closeAssignModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-tag mr-2"></i> {{ __('Assign Subscription') }}</h3>
            <button onclick="closeAssignModal()" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <form method="POST" action="{{ route('system-admin.subscriptions.assign') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="business_admin_id" id="assignUserId">
            <p class="text-sm text-gray-600" id="assignUserLabel">{{ __('Assigning subscription to...') }}</p>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Plan') }}</label>
                <select name="plan_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach($plans ?? \App\Models\SubscriptionPlan::where('is_active', true)->get() as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }} (UGX {{ number_format($plan->price) }} / {{ $plan->duration_days }}d)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                <select name="status" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="active">{{ __('Active') }}</option>
                    <option value="trial">{{ __('Trial') }}</option>
                    <option value="expired">{{ __('Expired') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Date') }}</label>
                <input type="date" name="start_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }}</label>
                <input type="date" name="end_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">{{ __('Assign Subscription') }}</button>
        </form>
    </div>
</div>

<!-- Record Payment Modal -->
<div id="paymentModal" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="if(event.target===this)closePaymentModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-credit-card mr-2"></i> {{ __('Record Payment') }}</h3>
            <button onclick="closePaymentModal()" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <form method="POST" action="{{ route('system-admin.payments.record') }}" class="p-5 space-y-4">
            @csrf
            <input type="hidden" name="subscription_id" id="paymentSubId">
            <p class="text-sm text-gray-600" id="paymentSubLabel">{{ __('Recording payment for...') }}</p>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Amount') }}</label>
                <input type="number" name="amount" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Payment Method') }}</label>
                <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('Select method') }}</option>
                    <option value="cash">{{ __('Cash') }}</option>
                    <option value="mpesa">M-Pesa</option>
                    <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                    <option value="airtel_money">Airtel Money</option>
                    <option value="other">{{ __('Other') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Reference') }}</label>
                <input type="text" name="reference" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500" placeholder="{{ __('Transaction ID or reference') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">{{ __('Record Payment') }}</button>
        </form>
    </div>
</div>

<script>
function openAssignModal(id, name) {
    document.getElementById('assignUserId').value = id;
    document.getElementById('assignUserLabel').textContent = '{{ __('Assign subscription to') }}: ' + name;
    document.getElementById('assignModal').style.display = 'flex';
}
function closeAssignModal() {
    document.getElementById('assignModal').style.display = 'none';
}
function openPaymentModal(id, name) {
    document.getElementById('paymentSubId').value = id;
    document.getElementById('paymentSubLabel').textContent = '{{ __('Record payment for') }}: ' + name;
    document.getElementById('paymentModal').style.display = 'flex';
}
function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}
</script>
@endsection
