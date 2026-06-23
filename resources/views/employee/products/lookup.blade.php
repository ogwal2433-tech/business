@extends('layouts.app')

@section('content')
<div class="w-full p-6">
  <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
      <h4 class="text-white font-semibold text-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        {{ __('Product Price Lookup') }}
      </h4>
      <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('Dashboard') }}
      </a>
    </div>

    <div class="p-6">
      {{-- Search Section --}}
      <div class="relative mb-4">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
          <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          {{ __('Search Product') }}
        </label>
        <input
          type="text"
          id="searchInput"
          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-shadow"
          placeholder="{{ __('Type product name...') }}"
          autocomplete="off"
          autofocus
        >
        {{-- Results dropdown --}}
        <ul id="results" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-50 max-h-48 overflow-y-auto"></ul>
      </div>

      {{-- Product Info Card --}}
      <div id="productInfo" class="hidden mt-4 border border-blue-200 bg-blue-50/40 rounded-xl p-5">
        <div class="flex items-center gap-2 mb-4">
          <div class="bg-blue-100 p-2 rounded-lg">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <h5 class="text-sm font-semibold text-blue-800 uppercase tracking-wider">{{ __('Product Details') }}</h5>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          {{-- SKU --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('SKU') }}</span>
              <p id="productSku" class="text-sm font-medium text-gray-800"></p>
            </div>
          </div>

          {{-- Name --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('Name') }}</span>
              <p id="productName" class="text-sm font-medium text-gray-800"></p>
            </div>
          </div>

          {{-- Unit --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('Unit') }}</span>
              <p id="productUnit" class="mt-0.5"></p>
            </div>
          </div>

          {{-- Buy Price (per piece) --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('Buy Price (per piece)') }}</span>
              <p id="productBuyPrice" class="text-sm font-medium text-gray-800"></p>
            </div>
          </div>

          {{-- Sell Price (per piece) --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('Sell Price (per piece)') }}</span>
              <p id="productSellPrice" class="text-sm font-semibold text-blue-700"></p>
            </div>
          </div>

          {{-- Bulk Buy Price --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('Bulk Buy Price') }}</span>
              <p id="productBulkBuy" class="text-sm text-gray-800"></p>
            </div>
          </div>

          {{-- Bulk Sell Price --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('Bulk Sell Price') }}</span>
              <p id="productBulkSell" class="text-sm font-semibold text-blue-700"></p>
            </div>
          </div>

          {{-- Stock (current) --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('Current Stock (pieces)') }}</span>
              <p id="productStock" class="text-sm font-medium text-gray-800"></p>
            </div>
          </div>

          {{-- Original Quantity --}}
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <div>
              <span class="text-xs text-gray-500">{{ __('Original Quantity') }}</span>
              <p id="productOriginalQty" class="text-sm text-gray-800"></p>
            </div>
          </div>
        </div>

        {{-- Quick Action --}}
        <div class="mt-4 pt-3 border-t border-blue-200">
          <button onclick="clearSearch()" class="inline-flex items-center gap-1 text-xs font-medium text-blue-700 hover:text-blue-900 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            {{ __('New Search') }}
          </button>
        </div>
      </div>

      {{-- Empty State --}}
      <div id="emptyState" class="text-center py-8">
        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <p class="text-sm text-gray-400 mt-2">{{ __('Start typing to search for products') }}</p>
      </div>
    </div>
  </div>
</div>

{{-- JavaScript --}}
<script>
  const products = @json($products);
  const searchInput = document.getElementById('searchInput');
  const resultsList = document.getElementById('results');
  const productInfo = document.getElementById('productInfo');
  const emptyState = document.getElementById('emptyState');

  const skuEl = document.getElementById('productSku');
  const nameEl = document.getElementById('productName');
  const unitEl = document.getElementById('productUnit');
  const buyPriceEl = document.getElementById('productBuyPrice');
  const sellPriceEl = document.getElementById('productSellPrice');
  const bulkBuyEl = document.getElementById('productBulkBuy');
  const bulkSellEl = document.getElementById('productBulkSell');
  const stockEl = document.getElementById('productStock');
  const originalQtyEl = document.getElementById('productOriginalQty');

  let searchTimeout;

  const unitBadge = (unit) => {
    const colors = { piece: 'bg-gray-100 text-gray-800', dozen: 'bg-yellow-100 text-yellow-800', carton: 'bg-blue-100 text-blue-800' };
    return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${colors[unit] || colors.piece}">${unit.charAt(0).toUpperCase() + unit.slice(1)}</span>`;
  };

  const fmt = (v) => v ? window.businessCurrency + ' ' + new Intl.NumberFormat().format(v) : '—';

  searchInput.addEventListener('input', function () {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    searchTimeout = setTimeout(() => {
      resultsList.innerHTML = '';

      if (!query) {
        resultsList.classList.add('hidden');
        return;
      }

      const matched = products.filter(p =>
        p.name.toLowerCase().includes(query.toLowerCase())
      );

      if (matched.length) {
        resultsList.classList.remove('hidden');
        matched.slice(0, 5).forEach(product => {
          const item = document.createElement('li');
          item.className = 'flex items-center justify-between px-4 py-2.5 text-sm border-b border-gray-100 last:border-b-0 hover:bg-blue-50 cursor-pointer transition-colors';

          const regex = new RegExp(`(${query})`, 'gi');
          const highlightedName = product.name.replace(regex, '<mark class="bg-blue-100 text-blue-800 rounded px-0.5">$1</mark>');

          item.innerHTML = `
            <span class="text-gray-800">${highlightedName}</span>
            <div class="flex items-center gap-2">
              ${unitBadge(product.unit || 'piece')}
              <span class="text-blue-700 font-semibold">${window.businessCurrency} ${new Intl.NumberFormat().format(product.price)}</span>
            </div>
          `;

          item.onclick = () => showProduct(product);
          resultsList.appendChild(item);
        });
      } else {
        resultsList.classList.remove('hidden');
        const noMatch = document.createElement('li');
        noMatch.className = 'text-center text-gray-400 text-sm py-3';
        noMatch.textContent = '{{ __("No matching product found.") }}';
        resultsList.appendChild(noMatch);
      }
    }, 200);
  });

  function showProduct(product) {
    skuEl.textContent = product.sku || '—';
    nameEl.textContent = product.name;
    unitEl.innerHTML = unitBadge(product.unit || 'piece');
    buyPriceEl.textContent = fmt(product.purchase_price);
    sellPriceEl.textContent = fmt(product.price);
    bulkBuyEl.textContent = fmt(product.purchase_price_bulk);
    bulkSellEl.textContent = fmt(product.selling_price_bulk);
    stockEl.textContent = product.quantity + ' pcs';
    originalQtyEl.textContent = product.original_quantity ? product.original_quantity + ' pcs' : '—';

    productInfo.classList.remove('hidden');
    emptyState.classList.add('hidden');
    resultsList.classList.add('hidden');
    searchInput.value = product.name;
  }

  window.clearSearch = function() {
    searchInput.value = '';
    searchInput.focus();
    productInfo.classList.add('hidden');
    emptyState.classList.remove('hidden');
  };

  document.addEventListener('click', function(event) {
    if (!searchInput.contains(event.target) && !resultsList.contains(event.target)) {
      resultsList.classList.add('hidden');
    }
  });

  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      resultsList.classList.add('hidden');
    }
  });
</script>
@endsection
