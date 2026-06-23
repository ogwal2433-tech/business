@extends('layouts.app')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  @media print {
    body * {
      visibility: hidden;
    }
    #receipt, #receipt * {
      visibility: visible;
    }
    #receipt {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      padding: 20px;
      font-size: 16px;
    }
  }
</style>
@endsection

@section('content')
<div class="container mt-0">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Record New Sale</h4>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
      </a>
    </div>

    <div class="card-body">
@if (session('success'))
    <div class="alert alert-success">
      {{ session('success') }}
    </div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">
    <strong>Validation Errors:</strong>
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif



      {{-- Success Alert --}}
      @if(session('success'))
      <div class="alert alert-success d-flex justify-content-between align-items-center">
        <div><i class="bi bi-check-circle"></i> Sale recorded successfully!</div>
        <button class="btn btn-outline-success btn-sm" onclick="window.print()">
          <i class="bi bi-printer"></i> Print Receipt
        </button>
      </div>

      {{-- Receipt Preview --}}
      <div id="receipt" class="p-3 bg-light border rounded">
        <h5 class="mb-3"><i class="bi bi-receipt"></i> Receipt Preview</h5>

        <p class="fw-semibold">Dear our Esteemed Customer,</p>
        <p>Thanks for purchasing our product <strong>{{ session('receipt.product') }}</strong>.</p>
        <p>Below is your transaction summary:</p>

        <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="bi bi-box-seam"></i> Product:</span>
            <span>{{ session('receipt.product') }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="bi bi-puzzle"></i> Unit:</span>
            <span>{{ session('receipt.unit') }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="bi bi-cash-stack"></i> Price per piece:</span>
            <span>UGX {{ number_format(session('receipt.price')) }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="bi bi-123"></i> Quantity Sold (pieces):</span>
            <span>{{ session('receipt.total_pieces') }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="bi bi-wallet2"></i> Total Paid:</span>
            <span>UGX {{ number_format(session('receipt.amount')) }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="bi bi-calendar-check"></i> Date:</span>
            <span>{{ session('receipt.date') }}</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            <span><i class="bi bi-person-check"></i> Processed by:</span>
            <span>{{ auth()->user()->name }}</span>
          </li>
        </ul>

        <p class="text-muted mt-2">We appreciate your business. Keep the receipt for reference.</p>
      </div>
      @endif

      {{-- Error Alert --}}
      @if(session('error'))
      <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
      </div>
      @endif

      {{-- Sale Form --}}
      <form id="saleForm" action="{{ route('admin.sales.store') }}" method="POST" class="row g-3 mt-3">
        @csrf

        {{-- Product Select --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold"><i class="bi bi-box"></i> Product</label>
          <select id="product_id" name="product_id" class="form-select" required onchange="updateProductDetails()">
            <option value="">-- Select Product --</option>
            @foreach ($products as $product)
              <option value="{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-price="{{ $product->price }}" {{-- price per piece --}}
                data-quantity="{{ $product->quantity }}" {{-- stock in pieces --}}
                data-units-per-carton="{{ $product->units_per_carton ?? 24 }}"
              >
                {{ $product->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Unit Select --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-grid"></i> Unit</label>
          <select id="unit_select" name="unit" class="form-select" required onchange="updateProductDetails()">
            <option value="piece" selected>Piece</option>
            <option value="dozen">Dozen</option>
            <option value="carton">Carton</option>
          </select>
        </div>

        {{-- Stock display --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-pie-chart"></i> In-Stock Qty (pieces)</label>
          <input type="text" id="stock_display" class="form-control bg-light" readonly placeholder="Choose product & unit">
        </div>

        {{-- Price display --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold"><i class="bi bi-cash-stack"></i> Price per Piece (UGX)</label>
          <input type="text" id="price_display" class="form-control bg-light" readonly placeholder="Choose product">
          <input type="hidden" id="price_value" name="price_value">
        </div>

        {{-- Quantity --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-123"></i> Quantity Sold</label>
          <input type="number" id="quantity" name="quantity" class="form-control" min="1" required oninput="updateSuggestedTotal(); validateForm();">
        </div>

        {{-- Total Pieces Sold (computed) --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-box-seam"></i> Total Pieces Sold</label>
          <input type="text" id="total_pieces_sold" class="form-control bg-light" readonly placeholder="Computed pieces sold">
          <input type="hidden" id="total_pieces_value" name="total_pieces_value">
        </div>

        {{-- Discount for bulk sales (only show if unit is dozen or carton) --}}
        <div class="col-md-3" id="discount_container" style="display:none;">
          <label class="form-label fw-semibold"><i class="bi bi-percent"></i> Bulk Discount (%)</label>
          <input type="number" id="discount_input" name="discount" class="form-control" min="0" max="100" value="0" oninput="updateSuggestedTotal(); validateForm();">
        </div>

        {{-- Amount paid --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-currency-exchange"></i> Amount Paid (UGX)</label>
          <input type="text" id="amount_display" name="amount_display" class="form-control" required oninput="formatAmountInput(this)">
          <input type="hidden" id="amount_sold" name="amount_sold">
        </div>

        {{-- Suggested total --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold"><i class="bi bi-calculator"></i> Minimum Expected (UGX)</label>
          <input type="text" id="suggested_total" class="form-control bg-light text-success" readonly>
        </div>

        <div class="col-12 d-flex justify-content-end">
          <button id="submitBtn" type="button" class="btn btn-primary px-4" disabled onclick="confirmSubmit()">
            <i class="bi bi-send-check"></i> Submit Sale
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-question-circle"></i> Confirm Sale</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Are you sure you want to record this sale?</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-success" onclick="document.getElementById('saleForm').submit()">Yes, Submit</button>
      </div>
    </div>
  </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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

  function formatUGX(amount) {
    return new Intl.NumberFormat('en-UG', {
      style: 'currency', currency: 'UGX', minimumFractionDigits: 0
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
      discountContainer.style.display = 'none';
      suggestedTotal.value = '';
      amountDisplay.value = '';
      amountSoldInput.value = '';
      submitBtn.disabled = true;
      return;
    }

    selectedPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    selectedStock = parseInt(selectedOption.getAttribute('data-quantity')) || 0;
    unitsPerCarton = parseInt(selectedOption.getAttribute('data-units-per-carton')) || 24;

    const unit = unitSelect.value;

    // Show stock always in pieces
    stockDisplay.value = selectedStock;

    // Price per piece always shown
    priceDisplay.value = selectedPrice ? formatUGX(selectedPrice) : '';
    priceValueInput.value = selectedPrice;

    // Show/hide discount input for bulk units
    if (unit === 'dozen' || unit === 'carton') {
      discountContainer.style.display = 'block';
    } else {
      discountContainer.style.display = 'none';
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

  function updateSuggestedTotal() {
    const qty = Math.max(0, parseInt(quantityInput.value) || 0);
    const unit = unitSelect.value;

    let multiplier = 1;
    if (unit === 'dozen') multiplier = 12;
    else if (unit === 'carton') multiplier = unitsPerCarton;

    const totalPieces = qty * multiplier;

    // Show total pieces sold read-only
    totalPiecesSoldInput.value = totalPieces;
    totalPiecesValueInput.value = totalPieces;

    // Calculate total price (pieces * piece price)
    let total = selectedPrice * totalPieces;

    if (discountContainer.style.display === 'block') {
      const discountPercent = Math.min(100, Math.max(0, parseInt(discountInput.value) || 0));
      total = total * (1 - discountPercent / 100);
    }

    suggestedTotal.value = qty ? formatUGX(total) : '';
  }

  function formatAmountInput(input) {
    // Remove non-digit chars
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

    // Check stock availability
    const sufficientStock = qty > 0 && totalPieces <= selectedStock;

    // Calculate total price including discount
    let total = selectedPrice * totalPieces;
    if (discountContainer.style.display === 'block') {
      const discountPercent = Math.min(100, Math.max(0, parseInt(discountInput.value) || 0));
      total = total * (1 - discountPercent / 100);
    }

    // Check amount paid is enough
    const priceOk = amt >= total;

    // Enable submit only if valid
    submitBtn.disabled = !(sufficientStock && priceOk);
  }

  function confirmSubmit() {
    if (!submitBtn.disabled) {
      new bootstrap.Modal(document.getElementById('confirmModal')).show();
    }
  }

  // Init
  productSelect.addEventListener('change', () => {
    updateProductDetails();
  });

  unitSelect.addEventListener('change', () => {
    updateProductDetails();
    updateSuggestedTotal();
    validateForm();
  });

  quantityInput.addEventListener('input', () => {
    updateSuggestedTotal();
    validateForm();
  });

  discountInput.addEventListener('input', () => {
    updateSuggestedTotal();
    validateForm();
  });

  amountDisplay.addEventListener('input', () => {
    formatAmountInput(amountDisplay);
  });

  // Initial call
  updateProductDetails();
</script>
@endsection
