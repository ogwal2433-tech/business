@extends('layouts.app')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
  #receipt { display: none; }

  .ts-wrapper .ts-control {
    border-radius: 0.5rem;
    border-color: #d1d5db;
    padding: 0.625rem 0.75rem;
    font-size: 0.875rem;
    min-height: 42px;
  }
  .ts-wrapper .ts-control:focus {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
    border-color: #3b82f6;
  }
  .ts-dropdown .active {
    background: #eff6ff;
  }

  @media print {
    body * { visibility: hidden; }
    #receipt { display: block; position: absolute; top: 0; left: 0; width: 100%; padding: 20px; font-size: 16px; }
    #receipt, #receipt * { visibility: visible; }
  }
</style>
@endsection

@section('content')
<div class="p-6">
  <div class="bg-white shadow-sm rounded-xl border border-gray-200">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between rounded-t-xl">
      <h4 class="text-white font-semibold text-lg flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ __('Record New Sale') }}
      </h4>
      <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('Dashboard') }}
      </a>
    </div>

    <div class="p-6">
      @if (session('success'))
      <div class="mb-4 flex items-center justify-between gap-3">
        <div class="flex-1 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
          <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <span>{{ session('success') }}</span>
        </div>
        <button onclick="window.print()" class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
          </svg>
          {{ __('Print Receipt') }}
        </button>
      </div>

      {{-- Receipt --}}
      <div id="receipt" class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
        <h5 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          {{ __('Receipt') }}
        </h5>
        <p class="text-sm text-gray-600">{{ __('Thanks for purchasing') }} <strong>{{ session('receipt.product') }}</strong>.</p>
        <ul class="mt-3 space-y-1.5 text-sm">
          <li class="flex justify-between"><span class="text-gray-500">{{ __('Product:') }}</span><span class="font-medium">{{ session('receipt.product') }}</span></li>
          <li class="flex justify-between"><span class="text-gray-500">{{ __('Unit:') }}</span><span class="font-medium">{{ session('receipt.unit') }}</span></li>
          <li class="flex justify-between"><span class="text-gray-500">{{ __('Price per piece:') }}</span><span class="font-medium">{{ businessCurrency() }} {{ number_format(session('receipt.price')) }}</span></li>
          <li class="flex justify-between"><span class="text-gray-500">{{ __('Quantity Sold (pieces):') }}</span><span class="font-medium">{{ session('receipt.total_pieces') }}</span></li>
          <li class="flex justify-between"><span class="text-gray-500">{{ __('Total Paid:') }}</span><span class="font-medium text-green-700">{{ businessCurrency() }} {{ number_format(session('receipt.amount')) }}</span></li>
          <li class="flex justify-between"><span class="text-gray-500">{{ __('Date:') }}</span><span class="font-medium">{{ session('receipt.date') }}</span></li>
          <li class="flex justify-between"><span class="text-gray-500">{{ __('Processed by:') }}</span><span class="font-medium">{{ auth()->user()->name }}</span></li>
        </ul>
      </div>
      @endif

      @if (session('error'))
      <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
      </div>
      @endif

      @if ($errors->any())
      <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
        <strong class="block mb-1">{{ __('Validation Errors:') }}</strong>
        <ul class="list-disc pl-4 space-y-0.5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- Sale Form --}}
      <form id="saleForm" action="{{ route('admin.sales.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
        @csrf

        {{-- Product Select --}}
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            {{ __('Product') }}
          </label>
          <select id="product_id" name="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required onchange="updateProductDetails()">
            <option value="">{{ __('-- Select Product --') }}</option>
            @foreach ($products as $product)
              <option value="{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-price="{{ $product->price }}"
                data-unit="{{ $product->unit ?? 'piece' }}"
                data-quantity="{{ $product->quantity }}"
                data-purchase-price-bulk="{{ $product->purchase_price_bulk ?? 0 }}"
                data-selling-price-bulk="{{ $product->selling_price_bulk ?? 0 }}"
                data-units-per-carton="{{ $product->units_per_carton ?? 24 }}"
              >
                {{ $product->name }} ({{ ucfirst($product->unit ?? __('piece')) }}, {{ __('Stock:') }} {{ $product->quantity }} {{ __('pcs') }})
              </option>
            @endforeach
          </select>
        </div>

        {{-- Unit Select --}}
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            {{ __('Unit') }}
          </label>
          <select id="unit_select" name="unit" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required onchange="updateProductDetails()">
            <option value="piece" selected>{{ __('Piece') }}</option>
            <option value="dozen">{{ __('Dozen') }}</option>
            <option value="carton">{{ __('Carton') }}</option>
          </select>
        </div>

        {{-- Stock display --}}
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            {{ __('In-Stock Qty (pieces)') }}
          </label>
          <input type="text" id="stock_display" class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2.5 text-sm text-gray-700" readonly placeholder="{{ __('Choose product & unit') }}">
        </div>

        {{-- Price display --}}
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ currency_label('Price per Piece (UGX)') }}
          </label>
          <input type="text" id="price_display" class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2.5 text-sm text-gray-700" readonly placeholder="{{ __('Choose product') }}">
          <input type="hidden" id="price_value" name="price_value">
        </div>

        {{-- Quantity --}}
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            {{ __('Quantity Sold') }}
          </label>
          <input type="number" id="quantity" name="quantity" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" min="1" required oninput="updateSuggestedTotal(); validateForm();">
        </div>

        {{-- Total Pieces Sold --}}
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            {{ __('Total Pieces Sold') }}
          </label>
          <input type="text" id="total_pieces_sold" class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2.5 text-sm text-gray-700" readonly placeholder="{{ __('Computed pieces sold') }}">
          <input type="hidden" id="total_pieces_value" name="total_pieces_value">
        </div>

        {{-- Bulk Discount --}}
        <div id="discount_container" class="hidden">
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01"/>
            </svg>
            {{ __('Bulk Discount (%)') }}
          </label>
          <input type="number" id="discount_input" name="discount" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" min="0" max="100" value="0" oninput="updateSuggestedTotal(); validateForm();">
        </div>

        {{-- Amount Paid --}}
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            {{ currency_label('Amount Paid (UGX)') }}
          </label>
          <input type="text" id="amount_display" name="amount_display" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" required oninput="formatAmountInput(this)">
          <input type="hidden" id="amount_sold" name="amount_sold">
        </div>

        {{-- Suggested total --}}
        <div>
          <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            {{ currency_label('Minimum Expected (UGX)') }}
          </label>
          <input type="text" id="suggested_total" class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2.5 text-sm text-green-700 font-medium" readonly>
        </div>

        {{-- Submit --}}
        <div class="md:col-span-2 flex justify-end pt-2">
          <button id="submitBtn" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors shadow-sm" disabled onclick="confirmSubmit()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            {{ __('Submit Sale') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Confirmation Modal --}}
<div id="confirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40">
  <div class="bg-white rounded-xl shadow-xl max-w-sm w-full mx-4 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-blue-100 p-2 rounded-full">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h5 class="text-lg font-semibold text-gray-800">{{ __('Confirm Sale') }}</h5>
    </div>
    <p class="text-sm text-gray-600 mb-6">{{ __('Are you sure you want to record this sale?') }}</p>
    <div class="flex justify-end gap-3">
      <button onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">{{ __('Cancel') }}</button>
      <button onclick="document.getElementById('saleForm').submit()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">{{ __('Yes, Submit') }}</button>
    </div>
  </div>
</div>

<script>
  const productSelect = document.getElementById('product_id');
  const unitSelect = document.getElementById('unit_select');
  const stockDisplay = document.getElementById('stock_display');
  const priceDisplay = document.getElementById('price_display');
  const priceValueInput = document.getElementById('price_value');
  const quantityInput = document.getElementById('quantity');
  const totalPiecesSoldInput = document.getElementById('total_pieces_sold');
  const totalPiecesValueInput = document.getElementById('total_pieces_value');
  const discountContainer = document.getElementById('discount_container');
  const discountInput = document.getElementById('discount_input');
  const amountDisplay = document.getElementById('amount_display');
  const amountSoldInput = document.getElementById('amount_sold');
  const suggestedTotal = document.getElementById('suggested_total');
  const submitBtn = document.getElementById('submitBtn');

  let selectedPrice = 0;
  let selectedStock = 0;
  let unitsPerCarton = 24;

  function formatCurrency(amount) {
    return new Intl.NumberFormat('en-UG', {
      minimumFractionDigits: 0, minimumFractionDigits: 0
    }).format(amount);
  }

  function updateProductDetails() {
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
      stockDisplay.value = '';
      priceDisplay.value = '';
      priceValueInput.value = '';
      quantityInput.value = '';
      totalPiecesSoldInput.value = '';
      totalPiecesValueInput.value = '';
      discountInput.value = '0';
      discountContainer.classList.add('hidden');
      suggestedTotal.value = '';
      amountDisplay.value = '';
      amountSoldInput.value = '';
      submitBtn.disabled = true;
      return;
    }

    selectedPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    unitsPerCarton = parseInt(selectedOption.getAttribute('data-units-per-carton')) || 24;

    // Fetch live stock via AJAX
    const productId = selectedOption.value;
    fetch('/api/stock/' + productId)
      .then(function(r) { return r.json(); })
      .then(function(data) {
        selectedStock = data.quantity;
        selectedOption.setAttribute('data-quantity', data.quantity);
        updateStockDisplay();
      })
      .catch(function() {
        selectedStock = parseInt(selectedOption.getAttribute('data-quantity')) || 0;
        updateStockDisplay();
      });

    function updateStockDisplay() {
      const unit = unitSelect.value;
      stockDisplay.value = selectedStock;
      priceDisplay.value = selectedPrice ? formatCurrency(selectedPrice) : '';
      priceValueInput.value = selectedPrice;

      if (unit === 'dozen' || unit === 'carton') {
        discountContainer.classList.remove('hidden');
      } else {
        discountContainer.classList.add('hidden');
        discountInput.value = '0';
      }

      quantityInput.value = '';
      totalPiecesSoldInput.value = '';
      totalPiecesValueInput.value = '';
      suggestedTotal.value = '';
      amountDisplay.value = '';
      amountSoldInput.value = '';
      submitBtn.disabled = true;
    }
  }

  function updateSuggestedTotal() {
    const qty = Math.max(0, parseInt(quantityInput.value) || 0);
    const unit = unitSelect.value;

    let multiplier = 1;
    if (unit === 'dozen') multiplier = 12;
    else if (unit === 'carton') multiplier = unitsPerCarton;

    const totalPieces = qty * multiplier;

    totalPiecesSoldInput.value = totalPieces;
    totalPiecesValueInput.value = totalPieces;

    let total = selectedPrice * totalPieces;

    if (!discountContainer.classList.contains('hidden')) {
      const discountPercent = Math.min(100, Math.max(0, parseInt(discountInput.value) || 0));
      total = total * (1 - discountPercent / 100);
    }

    suggestedTotal.value = qty ? formatCurrency(total) : '';
  }

  function formatAmountInput(input) {
    let val = input.value.replace(/[^\d]/g, '');
    input.value = val ? parseInt(val).toLocaleString('en-UG') : '';
    amountSoldInput.value = val ? parseInt(val) : 0;
    validateForm();
  }

  function validateForm() {
    const qty = parseInt(quantityInput.value) || 0;
    const unit = unitSelect.value;

    let multiplier = 1;
    if (unit === 'dozen') multiplier = 12;
    else if (unit === 'carton') multiplier = unitsPerCarton;

    const totalPieces = qty * multiplier;
    const amt = parseInt(amountSoldInput.value) || 0;
    const sufficientStock = qty > 0 && totalPieces <= selectedStock;

    let total = selectedPrice * totalPieces;
    if (!discountContainer.classList.contains('hidden')) {
      const discountPercent = Math.min(100, Math.max(0, parseInt(discountInput.value) || 0));
      total = total * (1 - discountPercent / 100);
    }

    const priceOk = amt >= total;
    submitBtn.disabled = !(sufficientStock && priceOk);
  }

  function confirmSubmit() {
    if (!submitBtn.disabled) {
      document.getElementById('confirmModal').classList.remove('hidden');
    }
  }

  function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
  }

  productSelect.addEventListener('change', () => updateProductDetails());
  unitSelect.addEventListener('change', () => { updateProductDetails(); updateSuggestedTotal(); validateForm(); });
  quantityInput.addEventListener('input', () => { updateSuggestedTotal(); validateForm(); });
  discountInput.addEventListener('input', () => { updateSuggestedTotal(); validateForm(); });
  amountDisplay.addEventListener('input', () => formatAmountInput(amountDisplay));

  updateProductDetails();

  document.addEventListener('click', function(event) {
    const modal = document.getElementById('confirmModal');
    if (!modal.classList.contains('hidden') && event.target === modal) {
      closeModal();
    }
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeModal();
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
  new TomSelect('#product_id', {
    create: false,
    sortField: { field: 'text', direction: 'asc' }
  });
</script>
@endsection
