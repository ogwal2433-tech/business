@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">{{ __('Subscriptions') }}</h1>
            <p class="text-gray-600 text-sm">{{ __('Manage all business subscriptions') }}</p>
        </div>
        <a href="{{ route('system-admin.dashboard') }}" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex gap-3">
            <div class="w-full sm:w-48">
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>{{ __('Trial') }}</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                </select>
            </div>
            <a href="{{ route('system-admin.subscriptions') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm transition-colors">
                <i class="fas fa-redo mr-1"></i> {{ __('Reset') }}
            </a>
        </form>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Business') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Plan') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Start') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('End') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscriptions as $sub)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('system-admin.businesses.detail', $sub->businessAdmin?->id) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    {{ $sub->businessAdmin?->business_name ?? 'N/A' }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $sub->businessAdmin?->name }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $sub->plan?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $sub->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $sub->status === 'trial' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $sub->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $sub->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $sub->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $sub->start_date?->format('d M Y') ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $sub->end_date?->format('d M Y') ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($sub->status === 'pending')
                                    <button type="button"
                                        data-id="{{ $sub->id }}"
                                        data-name="{{ $sub->businessAdmin?->business_name ?? 'N/A' }}"
                                        data-biz="{{ $sub->businessAdmin?->id ?? '' }}"
                                        data-price="{{ $sub->plan?->price ?? 0 }}"
                                        onclick="openApproveModal(this)"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-check mr-1"></i> {{ __('Approve') }}
                                    </button>
                                @else
                                    <button onclick="openSubStatusModal({{ $sub->id }}, '{{ $sub->status }}')" class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-lg text-blue-700 bg-white hover:bg-blue-50 transition-colors">
                                        <i class="fas fa-edit mr-1"></i> {{ __('Update') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">{{ __('No subscriptions found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">{{ $subscriptions->links() }}</div>
        @endif
    </div>
</div>

<!-- Update Subscription Status Modal -->
<div id="subStatusModal" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="if(event.target===this)closeSubStatusModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-edit mr-2"></i> {{ __('Update Status') }}</h3>
            <button onclick="closeSubStatusModal()" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <form method="POST" id="subStatusForm" class="p-5 space-y-4">
            @csrf
            @method('POST')
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
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }}</label>
                <input type="date" name="end_date" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">{{ __('Update') }}</button>
        </form>
    </div>
</div>

<!-- Approve Subscription Modal -->
<div id="approveModal" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="if(event.target===this)closeApproveModal()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden" onclick="event.stopPropagation()">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-5 py-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white"><i class="fas fa-check-circle mr-2"></i> {{ __('Approve Subscription') }}</h3>
            <button onclick="closeApproveModal()" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <form method="POST" id="approveForm" class="p-5 space-y-4">
            @csrf
            <p class="text-sm text-gray-600" id="approveLabel">{{ __('Approving subscription for...') }}</p>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Amount') }} <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="approveAmount" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Payment Method') }}</label>
                <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500">
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
                <div class="relative">
                    <input type="text" name="reference" id="approveReference" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-20 text-sm focus:ring-2 focus:ring-green-500" placeholder="{{ __('Transaction ID or reference') }}">
                    <button type="button" onclick="generateReference()" class="absolute right-1.5 top-1/2 -translate-y-1/2 text-xs bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1 rounded transition-colors">
                        <i class="fas fa-sync-alt mr-1"></i> {{ __('Generate') }}
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1" id="referenceHint">{{ __('Auto-generated for cash. Type your own for mobile/bank payments.') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors">
                <i class="fas fa-check mr-1"></i> {{ __('Approve & Record Payment') }}
            </button>
        </form>
    </div>
</div>

<script>
function openSubStatusModal(id, currentStatus) {
    document.getElementById('subStatusForm').action = '/system-admin/subscriptions/' + id + '/status';
    document.getElementById('subStatusForm').querySelector('[name="status"]').value = currentStatus;
    document.getElementById('subStatusModal').style.display = 'flex';
}
function closeSubStatusModal() {
    document.getElementById('subStatusModal').style.display = 'none';
}
function openApproveModal(btn) {
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name');
    var bizId = btn.getAttribute('data-biz');
    var price = btn.getAttribute('data-price');
    document.getElementById('approveForm').action = '/system-admin/subscriptions/' + id + '/approve';
    document.getElementById('approveForm').setAttribute('data-biz', bizId);
    document.getElementById('approveLabel').textContent = '{{ __('Approving subscription for') }}: ' + name;
    document.getElementById('approveAmount').value = price;
    document.getElementById('approveModal').style.display = 'flex';
    generateReference();
}
function closeApproveModal() {
    document.getElementById('approveModal').style.display = 'none';
}
function generateReference() {
    var form = document.getElementById('approveForm');
    var action = form.getAttribute('action');
    var parts = action.split('/');
    var subId = parts[parts.length - 1];
    var bizId = form.getAttribute('data-biz');
    var d = new Date();
    var ts = d.getFullYear().toString() +
        String(d.getMonth() + 1).padStart(2, '0') +
        String(d.getDate()).padStart(2, '0') +
        String(d.getHours()).padStart(2, '0') +
        String(d.getMinutes()).padStart(2, '0') +
        String(d.getSeconds()).padStart(2, '0');
    document.getElementById('approveReference').value = 'INV-' + bizId + '-' + subId + '-' + ts;
}
</script>
@endsection
