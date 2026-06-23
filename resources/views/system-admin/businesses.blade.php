@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">{{ __('Businesses') }}</h1>
            <p class="text-gray-600 text-sm">{{ __('All registered businesses on the platform') }}</p>
        </div>
        <a href="{{ route('system-admin.dashboard') }}" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to Dashboard') }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by business name, owner, or email...') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full sm:w-48">
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                <i class="fas fa-search mr-1"></i> {{ __('Filter') }}
            </button>
        </form>
    </div>

    <!-- Businesses Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Business') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Owner') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Employees') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Subscription') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">{{ __('Joined') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($businesses as $biz)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">
                                        {{ substr($biz->business_name, 0, 1) }}
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $biz->business_name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $biz->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $biz->employees_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($biz->subscription && $biz->subscription->plan)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $biz->subscription->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $biz->subscription->status === 'trial' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $biz->subscription->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $biz->subscription->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($biz->subscription->status) }} - {{ $biz->subscription->plan->name }}
                                    </span>
                                    @if($biz->subscription->end_date)
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Expires') }}: {{ $biz->subscription->end_date->format('d M Y') }}</p>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">{{ __('No subscription') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $biz->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($biz->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $biz->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('system-admin.businesses.detail', $biz->id) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-sm font-medium rounded-lg text-blue-700 bg-white hover:bg-blue-50 transition-colors">
                                    <i class="fas fa-eye mr-1"></i> {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">{{ __('No businesses found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($businesses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $businesses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
