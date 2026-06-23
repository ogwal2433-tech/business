{{-- Removed old commented-out template --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-semibold text-blue-700 mb-6">{{ __('Inventory List') }}</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('products.index') }}" class="mb-6 max-w-md">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="{{ __('Search by SKU or Name') }}"
                class="w-full border border-blue-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
    </form>

    <!-- Mobile Card View -->
    <div class="sm:hidden space-y-3">
        @forelse ($products as $product)
        <div class="bg-white border border-blue-200 rounded-lg shadow-sm p-4">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $product->sku }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 capitalize">{{ $product->unit }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div><span class="text-gray-500">{{ __('In Stock') }}:</span> <span class="font-semibold">{{ number_format($product->quantity) }} pcs</span></div>
                <div><span class="text-gray-500">{{ __('Buy') }}:</span> UGx {{ number_format($product->purchase_price, 0) }}</div>
                <div><span class="text-gray-500">{{ __('Sell') }}:</span> UGx {{ number_format($product->price, 0) }}</div>
                <div><span class="text-gray-500">{{ __('Profit') }}:</span> <span class="{{ ($product->expected_profit ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">UGx {{ number_format($product->expected_profit ?? 0, 0) }}</span></div>
            </div>
            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="openEditModal(this)" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-sku="{{ $product->sku }}" data-unit="{{ $product->unit }}" data-pp="{{ $product->purchase_price }}" data-ppb="{{ $product->purchase_price_bulk }}" data-sp="{{ $product->price }}" data-spb="{{ $product->selling_price_bulk }}" class="flex-1 px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 text-center">{{ __('Edit') }}</button>
                <form action="{{ route('inventory.destroy', $product->id) }}" method="POST" class="flex-1" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-3 py-1.5 bg-red-600 text-white text-xs rounded hover:bg-red-700 text-center">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-6 text-gray-500 font-semibold">{{ __('No products were uploaded to the inventory.') }}</div>
        @endforelse
    </div>

    <!-- Desktop Table -->
    <div class="hidden sm:block overflow-x-auto">
        <table class="min-w-full bg-white border border-blue-300 rounded-lg shadow-md">
            <thead class="bg-blue-100 text-blue-700 uppercase text-sm leading-normal">
                <tr>
                    <th class="py-3 px-4 text-left whitespace-nowrap">{{ __('SKU') }}</th>
                    <th class="py-3 px-4 text-left whitespace-nowrap">{{ __('Name') }}</th>
                    <th class="py-3 px-4 text-right whitespace-nowrap">{{ __('In Stock (Pieces)') }}</th>
                    <th class="py-3 px-4 text-left whitespace-nowrap">{{ __('Original Qty') }}</th>
                    <th class="py-3 px-4 text-left whitespace-nowrap">{{ __('Unit') }}</th>
                    <th class="py-3 px-4 text-right whitespace-nowrap">{{ __('Buy Price/Unit') }}</th>
                    <th class="py-3 px-4 text-right whitespace-nowrap">{{ __('Sell Price/Unit') }}</th>
                    <th class="py-3 px-4 text-right whitespace-nowrap">{{ __('Bulk Buy Price') }}</th>
                    <th class="py-3 px-4 text-right whitespace-nowrap">{{ __('Bulk Sell Price') }}</th>
                    <th class="py-3 px-4 text-right whitespace-nowrap">{{ __('Total Purchase Value') }}</th>
                    <th class="py-3 px-4 text-right whitespace-nowrap">{{ __('Total Sell Value') }}</th>
                    <th class="py-3 px-4 text-right whitespace-nowrap">{{ __('Expected Profit') }}</th>
                    <th class="py-3 px-4 text-center whitespace-nowrap">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse ($products as $product)
                <tr class="border-b border-blue-200 hover:bg-blue-50">
                    <td class="py-3 px-4">{{ $product->sku }}</td>
                    <td class="py-3 px-4">{{ $product->name }}</td>
                    <td class="py-3 px-4 text-right font-semibold">{{ number_format($product->quantity) }}</td>
                    <td class="py-3 px-4 text-right">
                        {{ $product->original_quantity ? number_format($product->original_quantity) : '-' }}
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                            {{ $product->unit }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">UGx {{ number_format($product->purchase_price, 0) }}</td>
                    <td class="py-3 px-4 text-right">UGx {{ number_format($product->price, 0) }}</td>
                    <td class="py-3 px-4 text-right">
                        {{ $product->purchase_price_bulk ? 'UGx ' . number_format($product->purchase_price_bulk, 0) : '-' }}
                    </td>
                    <td class="py-3 px-4 text-right">
                        {{ $product->selling_price_bulk ? 'UGx ' . number_format($product->selling_price_bulk, 0) : '-' }}
                    </td>
                    <td class="py-3 px-4 text-right font-semibold">
                        UGx {{ number_format($product->total_purchase_value ?? 0, 0) }}
                    </td>
                    <td class="py-3 px-4 text-right font-semibold">
                        UGx {{ number_format($product->total_sell_value ?? 0, 0) }}
                    </td>
                    <td class="py-3 px-4 text-right font-semibold {{ ($product->expected_profit ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        UGx {{ number_format($product->expected_profit ?? 0, 0) }}
                    </td>
                    <td class="py-3 px-4 text-center space-x-2 whitespace-nowrap">
                        <button type="button"
                                onclick="openEditModal(this)"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-sku="{{ $product->sku }}"
                                data-unit="{{ $product->unit }}"
                                data-pp="{{ $product->purchase_price }}"
                                data-ppb="{{ $product->purchase_price_bulk }}"
                                data-sp="{{ $product->price }}"
                                data-spb="{{ $product->selling_price_bulk }}"
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-150">
                            {{ __('Edit') }}
                        </button>
                        <form action="{{ route('inventory.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('Are you sure you want to delete this product?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition duration-150">
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center py-6 text-gray-500 font-semibold">
                        {{ __('No products were uploaded to the inventory.') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($products->count())
            <tfoot class="bg-blue-100 text-blue-700 font-semibold">
                <tr>
                    <td colspan="9" class="py-3 px-4 text-right whitespace-nowrap">{{ __('Totals:') }}</td>
                    <td class="py-3 px-4 text-right whitespace-nowrap">UGx {{ number_format($totalPurchaseValue ?? 0, 0) }}</td>
                    <td class="py-3 px-4 text-right whitespace-nowrap">UGx {{ number_format($totalSellValue ?? 0, 0) }}</td>
                    <td class="py-3 px-4 text-right whitespace-nowrap {{ ($expectedProfit ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        UGx {{ number_format($expectedProfit ?? 0, 0) }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    </div>

    <div class="mt-6">
        {{ $products->withQueryString()->links() }}
    </div>
</div>

{{-- Edit Price Modal --}}
<div id="editPriceModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40" style="display:none;">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">{{ __('Edit Prices') }}</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>
        <p id="editProductName" class="text-sm text-gray-500 mb-4"></p>

        <form id="editPriceForm" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div id="piecePrices">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Buy Price/Unit') }}</label>
                        <input type="text" name="purchase_price" id="edit_pp"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-number" step="0.01" min="0"
                               oninput="formatComma(this)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Sell Price/Unit') }}</label>
                        <input type="text" name="price" id="edit_sp"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-number" step="0.01" min="0"
                               oninput="formatComma(this)">
                    </div>
                </div>
                <div id="bulkPrices">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Bulk Buy Price') }}</label>
                        <input type="text" name="purchase_price_bulk" id="edit_ppb"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-number" step="0.01" min="0"
                               oninput="formatComma(this)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Bulk Sell Price') }}</label>
                        <input type="text" name="selling_price_bulk" id="edit_spb"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-number" step="0.01" min="0"
                               oninput="formatComma(this)">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    {{ __('Update Prices') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function formatComma(input) {
        var val = input.value.replace(/[^\d.]/g, '');
        var parts = val.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        input.value = parts.join('.');
    }

    function openEditModal(btn) {
        document.getElementById('editPriceForm').action = '/inventory/' + btn.getAttribute('data-id');
        document.getElementById('editProductName').textContent = btn.getAttribute('data-name') + ' (' + btn.getAttribute('data-sku') + ')';
        document.getElementById('edit_pp').value = Number(btn.getAttribute('data-pp')).toLocaleString('en');
        document.getElementById('edit_ppb').value = btn.getAttribute('data-ppb') ? Number(btn.getAttribute('data-ppb')).toLocaleString('en') : '';
        document.getElementById('edit_sp').value = Number(btn.getAttribute('data-sp')).toLocaleString('en');
        document.getElementById('edit_spb').value = btn.getAttribute('data-spb') ? Number(btn.getAttribute('data-spb')).toLocaleString('en') : '';

        var unit = btn.getAttribute('data-unit');
        var isBulk = unit === 'dozen' || unit === 'carton';
        document.getElementById('piecePrices').style.display = isBulk ? 'none' : '';
        document.getElementById('bulkPrices').style.display = isBulk ? '' : 'none';

        document.getElementById('editPriceModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editPriceModal').style.display = 'none';
    }

    document.addEventListener('click', function(e) {
        var modal = document.getElementById('editPriceModal');
        if (modal.style.display === 'flex' && e.target === modal) {
            closeEditModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditModal();
    });
</script>
@endsection
