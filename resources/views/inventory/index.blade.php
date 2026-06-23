@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-0">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Inventory Report') }}</h1>
        <p class="text-gray-600">{{ __('Monitor and manage your product inventory') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-50">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __("Total Products") }}</p>
                    <p class="text-2xl font-bold text-gray-900" id="inv-total">{{ $products->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-50">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __("In Stock") }}</p>
                    <p class="text-2xl font-bold text-gray-900" id="inv-instock">
                        {{ $products->where('quantity', '>', 10)->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-50">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __("Low Stock") }}</p>
                    <p class="text-2xl font-bold text-gray-900" id="inv-lowstock">
                        {{ $products->where('quantity', '<=', 10)->where('quantity', '>', 0)->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-red-50">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">{{ __("Out of Stock") }}</p>
                    <p class="text-2xl font-bold text-gray-900" id="inv-outofstock">
                        {{ $products->where('quantity', 0)->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col lg:flex-row gap-4 items-start lg:items-center">
            <div class="flex-1 w-full">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __("Search Products") }}</label>
                <input
                    type="text"
                    name="search"
                    placeholder="{{ __("Enter product name...") }}"
                    value="{{ request('search') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                >
            </div>

            <div class="w-full lg:w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __("Stock Status") }}</label>
                <select name="stock_status" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">{{ __("All Stock Status") }}</option>
                    <option value="in" @selected(request('stock_status') == 'in')>{{ __("In Stock") }}</option>
                    <option value="low" @selected(request('stock_status') == 'low')>{{ __("Low Stock") }}</option>
                    <option value="out" @selected(request('stock_status') == 'out')>{{ __("Out of Stock") }}</option>
                </select>
            </div>

            <div class="w-full lg:w-auto pt-6 lg:pt-0">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium text-sm">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __("Product") }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __("Stock (Original)") }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __("Unit") }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __("Buy Price") }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __("Sell Price") }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __("Stock Status") }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __("Stock Level") }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-semibold text-sm">{{ substr($product->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($product->quantity) }}</div>
                                @if($product->original_quantity && $product->unit !== 'piece')
                                    <div class="text-xs text-gray-500">({{ number_format($product->original_quantity) }} {{ $product->unit }}s)</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                                    {{ $product->unit }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">UGx {{ number_format($product->purchase_price) }}</div>
                                @if($product->purchase_price_bulk)
                                    <div class="text-xs text-gray-500">UGx {{ number_format($product->purchase_price_bulk) }}/{{ $product->unit }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">UGx {{ number_format($product->price) }}</div>
                                @if($product->selling_price_bulk)
                                    <div class="text-xs text-gray-500">UGx {{ number_format($product->selling_price_bulk) }}/{{ $product->unit }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($product->quantity == 0)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                        {{ __("Out of Stock") }}</span>
                                @elseif ($product->quantity <= 10)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                        {{ __("Low Stock") }}</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        {{ __("In Stock") }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                        @php
                                            $maxQuantity = max($products->max('quantity'), 100);
                                            $percentage = $maxQuantity > 0 ? ($product->quantity / $maxQuantity) * 100 : 0;
                                        @endphp
                                        <div
                                            class="h-2 rounded-full @if($product->quantity == 0) bg-red-500 @elseif($product->quantity <= 10) bg-yellow-500 @else bg-green-500 @endif"
                                            style="width: {{ max($percentage, 5) }}%"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __("No products found") }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">{{ __("Try adjusting your search or filter criteria.") }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            {{ $products->withQueryString()->links() }}
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
        border-radius: 6px;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination li a:hover {
        background-color: #f3f4f6;
        border-color: #d1d5db;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    function refreshInventoryStats() {
        fetch('/api/inventory/stats')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                var el = function(id) { return document.getElementById(id); };
                if (el('inv-total')) el('inv-total').textContent = d.total;
                if (el('inv-instock')) el('inv-instock').textContent = d.inStock;
                if (el('inv-lowstock')) el('inv-lowstock').textContent = d.lowStock;
                if (el('inv-outofstock')) el('inv-outofstock').textContent = d.outOfStock;
            })
            .catch(function() {});
    }
    setInterval(refreshInventoryStats, 30000);
});
</script>
@endsection


