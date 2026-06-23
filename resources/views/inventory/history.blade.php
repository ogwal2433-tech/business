{{-- @extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
    <div class="flex items-center justify-between mb-6">
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <h1 class="text-2xl font-bold">Inventory Adjustment History</h1>
        <a href="{{ route('inventory.list') }}"
           class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
           view stock level
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm border border-gray-200">
            <thead class="bg-gray-100 text-left">
                <tr class="text-gray-700 font-semibold">
                    <th class="px-4 py-2 border-b">Date</th>
                    <th class="px-4 py-2 border-b">Product</th>
                    <th class="px-4 py-2 border-b">Type</th>
                    <th class="px-4 py-2 border-b">Qty sold</th>
                    <th class="px-4 py-2 border-b">Before → After</th>
                    <th class="px-4 py-2 border-b">By</th>
                    <th class="px-4 py-2 border-b">Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-t hover:bg-gray-50 text-gray-800">
                        <td class="px-4 py-2">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">{{ $log->product->name }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded text-white
                                {{ $log->type === 'increase' ? 'bg-green-600' : 'bg-red-600' }}">
                                {{ ucfirst($log->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $log->quantity }}</td>
                        <td class="px-4 py-2">{{ $log->previous_quantity }} → {{ $log->new_quantity }}</td>
                        <td class="px-4 py-2">{{ $log->user->name ?? 'System' }}</td>
                        <td class="px-4 py-2">{{ $log->note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center px-4 py-6 text-gray-500">
                            No adjustment history found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
@endsection --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-0">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-6 0h6m-6 0a2 2 0 01-2-2v-6a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2m-6 0h6" />
    </svg>
    {{ __('Inventory Adjustment History') }}
</h1>

                <p class="text-gray-600">{{ __('Track all inventory changes, adjustments, and stock movements') }}</p>
            </div>

        </div>
    </div>

    <!-- Alerts Section -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-red-800">{{ __('There were :count error(s) with your submission:', ['count' => $errors->count()]) }}</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 mt-o">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">{{ __('Total Adjustments') }}</p>
                    <p class="text-2xl font-bold mt-1">{{ $logs->total() }}</p>
                </div>
                <div class="p-3 bg-blue-400/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">{{ __('Stock Increases') }}</p>
                    <p class="text-2xl font-bold mt-1">{{ $logs->where('type', 'increase')->count() }}</p>
                </div>
                <div class="p-3 bg-green-400/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">{{ __('Stock Decreases') }}</p>
                    <p class="text-2xl font-bold mt-1">{{ $logs->where('type', 'decrease')->count() }}</p>
                </div>
                <div class="p-3 bg-red-400/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-2xl p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">{{ __('Unique Products') }}</p>
                    <p class="text-2xl font-bold mt-1">{{ $logs->unique('product_id')->count() }}</p>
                </div>
                <div class="p-3 bg-purple-400/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Adjustment History Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">{{ __('Recent Adjustments') }}</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Date & Time') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Product') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Adjustment Type') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Quantity Changed') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Stock Movement') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('User') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <!-- Date & Time -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $log->created_at->format('M j, Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $log->created_at->format('g:i A') }}
                                </div>
                            </td>

                            <!-- Product -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-semibold text-xs">{{ substr($log->product->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->product->name }}</div>
                                        <div class="text-xs text-gray-500">SKU: {{ $log->product->sku }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Adjustment Type -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $log->type === 'increase'
                                        ? 'bg-green-100 text-green-800 border border-green-200'
                                        : 'bg-red-100 text-red-800 border border-red-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5
                                        {{ $log->type === 'increase' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ ucfirst($log->type) }}
                                </span>
                            </td>

                            <!-- Quantity Changed -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 {{ $log->type === 'increase' ? 'text-green-500' : 'text-red-500' }}"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        @if($log->type === 'increase')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        @endif
                                    </svg>
                                    <span class="text-sm font-semibold {{ $log->type === 'increase' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $log->type === 'increase' ? '+' : '-' }}{{ $log->quantity }}
                                    </span>
                                </div>
                            </td>

                            <!-- Stock Movement -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500 font-medium">{{ $log->previous_quantity }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">{{ $log->new_quantity }}</span>
                                </div>
                                @php
                                    $changePercent = $log->previous_quantity ? (($log->new_quantity - $log->previous_quantity) / $log->previous_quantity) * 100 : 0;
                                @endphp
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $changePercent > 0 ? '+' : '' }}{{ number_format($changePercent, 1) }}%
                                </div>
                            </td>

                            <!-- User -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-6 w-6 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">{{ substr($log->user->name ?? 'S', 0, 1) }}</span>
                                    </div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->user->name ?? __('System') }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Notes -->
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 max-w-xs">
                                    {{ $log->note ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No adjustment history found') }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ __('Inventory adjustments will appear here once made.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<style>
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
    }

    .pagination li {
        margin: 0 2px;
    }

    .pagination li a,
    .pagination li span {
        display: inline-block;
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 500;
    }

    .pagination li a:hover {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    .pagination li.active span {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    .pagination li.disabled span {
        color: #9ca3af;
        background-color: #f9fafb;
        border-color: #e5e7eb;
    }
</style>
@endsection
