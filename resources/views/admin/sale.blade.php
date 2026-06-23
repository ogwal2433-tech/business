@extends('layouts.app')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
  .ts-wrapper .ts-control {
    border-radius: 0.375rem;
    min-height: 38px;
  }
  #receipt-preview-modal .modal-dialog {
    max-width: 420px;
  }

  #receipt {
    position: relative;
    font-family: 'Courier New', Courier, monospace;
    font-size: 13px;
    line-height: 1.6;
    padding: 28px 22px;
    background: #fff;
    color: #000;
    overflow: hidden;
  }

  #receipt .watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-35deg);
    font-size: 72px;
    font-weight: 900;
    color: rgba(34, 197, 94, 0.15);
    letter-spacing: 12px;
    text-transform: uppercase;
    pointer-events: none;
    white-space: nowrap;
    user-select: none;
    z-index: 0;
  }

  #receipt .receipt-header {
    position: relative;
    z-index: 1;
    text-align: center;
    border-bottom: 2px double #222;
    padding-bottom: 14px;
    margin-bottom: 12px;
  }

  #receipt .receipt-header h4 {
    font-size: 17px;
    font-weight: 800;
    margin: 0 0 2px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
  }

  #receipt .receipt-header .receipt-type {
    font-size: 11px;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 3px;
    margin: 2px 0;
  }

  #receipt .receipt-header .receipt-date {
    font-size: 10px;
    color: #777;
    margin: 2px 0 0;
  }

  #receipt .receipt-divider {
    position: relative;
    z-index: 1;
    border-top: 1px dashed #999;
    margin: 8px 0;
  }

  #receipt .receipt-divider.thick {
    border-top: 1px solid #999;
  }

  #receipt .receipt-row {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    padding: 3px 0;
    font-size: 12px;
  }

  #receipt .receipt-row.total {
    font-weight: 800;
    font-size: 15px;
    border-top: 2px solid #222;
    padding-top: 7px;
    margin-top: 7px;
  }

  #receipt .receipt-row.label-only {
    font-weight: 700;
    font-size: 11px;
    color: #555;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 1px dotted #ccc;
    margin-top: 6px;
    padding-bottom: 4px;
  }

  #receipt .receipt-footer {
    position: relative;
    z-index: 1;
    text-align: center;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 2px double #222;
    font-size: 11px;
    color: #555;
  }

  #receipt .receipt-footer .thanks {
    font-size: 12px;
    font-weight: 700;
    color: #333;
    margin-bottom: 4px;
  }

  #receipt .receipt-footer .footer-info {
    font-size: 9px;
    color: #999;
  }

  #receipt .credit-badge {
    display: inline-block;
    background: #ffc107;
    color: #000;
    padding: 2px 12px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-top: 6px;
  }

  #receipt .client-section {
    position: relative;
    z-index: 1;
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px dashed #999;
  }

  #receipt .signature-line {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    margin-top: 16px;
    padding-top: 4px;
    font-size: 10px;
    color: #666;
  }

  #receipt .signature-line span {
    text-align: center;
    border-top: 1px solid #333;
    padding-top: 4px;
    min-width: 120px;
  }

  .receipt-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    padding: 12px 0;
  }

  @media print {
    @page { margin: 0; size: 80mm auto; }

    body * { visibility: hidden; }
    #receipt, #receipt * { visibility: visible; }
    #receipt {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      width: 72mm;
      margin: 0 auto;
      padding: 3mm 4mm;
      font-size: 10pt;
      line-height: 1.4;
    }
    #receipt .watermark {
      font-size: 48pt;
      color: rgba(34, 197, 94, 0.10);
    }
    #receipt .receipt-row { font-size: 9pt; padding: 1pt 0; }
    #receipt .receipt-header h4 { font-size: 12pt; }
    #receipt .receipt-header .receipt-type { font-size: 8pt; letter-spacing: 2pt; }
    #receipt .receipt-header .receipt-date { font-size: 8pt; }
    #receipt .receipt-row.total { font-size: 11pt; }
    #receipt .receipt-footer .thanks { font-size: 10pt; }
    #receipt .receipt-footer .footer-info { font-size: 7pt; }
    #receipt .receipt-divider { margin: 4pt 0; }
    #receipt .receipt-row.label-only { font-size: 8pt; }
    #receipt .signature-line { font-size: 8pt; margin-top: 10pt; }
    #receipt .signature-line span { min-width: 80pt; }
    #receipt .credit-badge { font-size: 8pt; padding: 1pt 8pt; }
    .receipt-actions { display: none; }
    #receipt-preview-modal .modal-dialog { max-width: 72mm; }
    #receipt-preview-modal .modal-content { box-shadow: none; border: none; }
  }
</style>
@endsection

@section('content')
<div class="container mt-0">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="bi bi-plus-circle"></i> {{ __('Record New Sale') }}</h4>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left-circle"></i> {{ __('Back to Dashboard') }}
      </a>
    </div>

    <div class="card-body">

@if ($errors->any())
  <div class="alert alert-danger">
    <strong>{{ __('Validation Errors:') }}</strong>
    <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

      {{-- Success Alert with Print Button (stays visible) --}}
      @if(session('success'))
      <div class="alert alert-success d-flex justify-content-between align-items-center" data-no-dismiss>
        <div>
          <i class="bi bi-check-circle"></i> {{ session('success') }}
          @if(session('receipt.status') === 'credit')
            <span class="badge bg-warning text-dark ms-2">{{ __('CREDIT') }}</span>
          @endif
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#receiptModal">
            <i class="bi bi-printer"></i> {{ __('View & Print Receipt') }}
          </button>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="startNewSale()">
            <i class="bi bi-plus-circle"></i> {{ __('New Sale') }}
          </button>
        </div>
      </div>

      {{-- Receipt Preview Modal --}}
      <div class="modal fade" id="receiptModal" tabindex="-1">
        <div class="modal-dialog" id="receipt-preview-modal">
          <div class="modal-content">
            <div class="modal-header border-0 pb-0">
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
              <div id="receipt">
                  <div class="watermark">{{ __('PAID') }}</div>

                  <div class="receipt-header">
                    <h4>{{ session('receipt.business_name') }}</h4>
                    <div class="receipt-type">{{ __('Official Receipt') }}</div>
                    <div class="receipt-date">{{ session('receipt.date') }}</div>
                  </div>

                  <div class="receipt-row label-only">{{ __('Items') }}</div>

                  <div class="receipt-row">
                    <span>{{ session('receipt.product') }} × {{ session('receipt.total_pieces') }} {{ __('pcs') }}</span>
                    <span>UGX {{ number_format(session('receipt.price') * session('receipt.total_pieces')) }}</span>
                  </div>

                  <div class="receipt-divider"></div>

                  <div class="receipt-row total">
                    <span>{{ session('receipt.status') === 'credit' ? __('Deposit Today') : __('Total Paid') }}</span>
                    <span>UGX {{ number_format(session('receipt.amount')) }}</span>
                  </div>

                  @if(session('receipt.status') === 'credit' && session('receipt.client_name'))
                  <div class="client-section">
                    <div class="receipt-row">
                      <span>{{ __('Client') }}</span>
                      <span>{{ session('receipt.client_name') }}</span>
                    </div>
                    <div class="receipt-row">
                      <span>{{ __('Balance Due') }}</span>
                      <span>UGX {{ number_format(session('receipt.balance')) }}</span>
                    </div>
                    <div class="text-center">
                      <span class="credit-badge">{{ __('Credit Sale') }}</span>
                    </div>
                  </div>
                  @endif

                  <div class="receipt-row" style="margin-top:8px;">
                    <span>{{ __('Processed by') }}</span>
                    <span>{{ auth()->user()->name }}</span>
                  </div>

                  <div class="signature-line">
                    <span>{{ __('Signature') }}</span>
                    <span>{{ __('Stamp') }}</span>
                  </div>

                  <div class="receipt-footer">
                    <div class="thanks">{{ __('Thank you — come again!') }}</div>
                    <div class="footer-info">{{ now()->format('d M Y H:i:s') }} | #{{ substr(time(), -6) }}</div>
                  </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
              <div class="receipt-actions w-100">
                <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <button class="btn btn-primary" onclick="printReceipt()">
                  <i class="bi bi-printer"></i> {{ __('Print') }}
                </button>
              </div>
            </div>
          </div>
        </div>
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
          <label class="form-label fw-semibold"><i class="bi bi-box"></i> {{ __('Product') }}</label>
          <select id="product_id" name="product_id" class="form-select" required onchange="updateProductDetails()">
            <option value="">-- {{ __('Select Product') }} --</option>
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
          <label class="form-label fw-semibold"><i class="bi bi-grid"></i> {{ __('Unit') }}</label>
          <select id="unit_select" name="unit" class="form-select" required onchange="updateProductDetails()">
            <option value="piece" selected>{{ __('Piece') }}</option>
            <option value="dozen">{{ __('Dozen') }}</option>
            <option value="carton">{{ __('Carton') }}</option>
          </select>
        </div>

        {{-- Stock display --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-pie-chart"></i> {{ __('In-Stock Qty (pieces)') }}</label>
          <input type="text" id="stock_display" class="form-control bg-light" readonly placeholder="{{ __('Choose product & unit') }}">
        </div>

        {{-- Price display --}}
        <div class="col-md-6">
          <label class="form-label fw-semibold"><i class="bi bi-cash-stack"></i> {{ __('Price per Piece (UGX)') }}</label>
          <input type="text" id="price_display" class="form-control bg-light" readonly placeholder="{{ __('Choose product') }}">
          <input type="hidden" id="price_value" name="price_value">
        </div>

        {{-- Quantity --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-123"></i> {{ __('Quantity Sold') }}</label>
          <input type="number" id="quantity" name="quantity" class="form-control" min="1" required oninput="updateSuggestedTotal(); validateForm();">
        </div>

        {{-- Total Pieces Sold (computed) --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-box-seam"></i> {{ __('Total Pieces Sold') }}</label>
          <input type="text" id="total_pieces_sold" class="form-control bg-light" readonly placeholder="{{ __('Computed pieces sold') }}">
          <input type="hidden" id="total_pieces_value" name="total_pieces_value">
        </div>

        {{-- Discount for bulk sales (only show if unit is dozen or carton) --}}
        <div class="col-md-3" id="discount_container" style="display:none;">
          <label class="form-label fw-semibold"><i class="bi bi-percent"></i> {{ __('Bulk Discount (%)') }}</label>
          <input type="number" id="discount_input" name="discount" class="form-control" min="0" max="100" value="0" oninput="updateSuggestedTotal(); validateForm();">
        </div>

        {{-- Payment Mode Toggle --}}
        <div class="col-12">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="btn-group" role="group">
              <button type="button" id="paidBtn" class="btn btn-success active" onclick="setPaymentMode('paid')">
                <i class="bi bi-cash"></i> {{ __('Paid Sale') }}
              </button>
              <button type="button" id="creditBtn" class="btn btn-warning" onclick="setPaymentMode('credit')">
                <i class="bi bi-credit-card"></i> {{ __('Credit Sale') }}
              </button>
            </div>
            <input type="hidden" id="status_input" name="status" value="paid">
            <small id="creditHint" class="text-muted" style="display:none;">
              <i class="bi bi-info-circle"></i> {{ __('Customer pays deposit today, balance later') }}
            </small>
          </div>
        </div>

        {{-- Client Name (credit only) --}}
        <div class="col-md-4" id="clientNameField" style="display:none;">
          <label class="form-label fw-semibold"><i class="bi bi-person"></i> {{ __('Client Name') }}</label>
          <input type="text" name="client_name" class="form-control" placeholder="{{ __('Enter client name for credit') }}">
        </div>

        {{-- Amount paid --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-currency-exchange"></i> <span id="amountLabel">{{ __('Amount Paid (UGX)') }}</span></label>
          <input type="text" id="amount_display" name="amount_display" class="form-control" required oninput="formatAmountInput(this)">
          <input type="hidden" id="amount_sold" name="amount_sold">
        </div>

        {{-- Suggested total --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold"><i class="bi bi-calculator"></i> {{ __('Total Price (UGX)') }}</label>
          <input type="text" id="suggested_total" class="form-control bg-light text-success" readonly>
          <input type="hidden" id="full_total_value" name="full_total" value="0">
        </div>

        {{-- Balance Left (credit only) --}}
        <div class="col-md-3" id="balanceField" style="display:none;">
          <label class="form-label fw-semibold"><i class="bi bi-pie-chart"></i> {{ __('Balance Left (UGX)') }}</label>
          <input type="text" id="balance_left_display" class="form-control bg-light text-danger fw-bold" readonly>
          <input type="hidden" id="balance_left_value" name="balance_left" value="0">
        </div>

        <div class="col-12 d-flex justify-content-end">
          <button id="submitBtn" type="button" class="btn btn-primary px-4" disabled onclick="confirmSubmit()">
            <i class="bi bi-send-check"></i> {{ __('Submit Sale') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Confirmation Modal (smooth overlay) --}}
<div id="confirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40" style="display:none;">
  <div class="bg-white rounded-xl shadow-xl max-w-sm w-full mx-4 p-6">
    <div class="flex items-center gap-3 mb-4">
      <div class="bg-blue-100 p-2 rounded-full">
        <svg class="w-5 h-5 text-blue-600" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h5 class="text-lg font-semibold text-gray-800">{{ __('Confirm Sale') }}</h5>
    </div>
    <p id="confirmMsg" class="text-sm text-gray-600 mb-2">{{ __('Are you sure you want to record this sale?') }}</p>
    <p id="creditConfirmMsg" class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-4" style="display:none;">
      <i class="bi bi-exclamation-triangle"></i> {{ __('CREDIT SALE — Balance will be collected later.') }}
    </p>
    <div class="flex justify-end gap-3 mt-4">
      <button onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">{{ __('Cancel') }}</button>
      <button onclick="document.getElementById('saleForm').submit()" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">{{ __('Yes, Submit') }}</button>
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
  const statusInput = document.getElementById('status_input');
  const clientNameField = document.getElementById('clientNameField');
  const balanceField = document.getElementById('balanceField');
  const balanceLeftDisplay = document.getElementById('balance_left_display');
  const balanceLeftValue = document.getElementById('balance_left_value');
  const amountLabel = document.getElementById('amountLabel');
  const confirmMsg = document.getElementById('confirmMsg');
  const creditConfirmMsg = document.getElementById('creditConfirmMsg');
  const creditHint = document.getElementById('creditHint');

  let selectedPrice = 0;
  let selectedStock = 0;
  let unitsPerCarton = 24;

  function formatUGX(amount) {
    return new Intl.NumberFormat('en-UG', {
      style: 'currency', currency: 'UGX', minimumFractionDigits: 0
    }).format(amount);
  }

  function printReceipt() {
    const receiptModal = document.getElementById('receiptModal');
    const modalInstance = bootstrap.Modal.getInstance(receiptModal);
    if (modalInstance) modalInstance.hide();

    setTimeout(() => window.print(), 300);
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
      priceDisplay.value = selectedPrice ? formatUGX(selectedPrice) : '';
      priceValueInput.value = selectedPrice;

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
    document.getElementById('full_total_value').value = total;
    updateBalanceLeft();
  }

  function updateBalanceLeft() {
    const total = parseFloat((suggestedTotal.value || '').replace(/[^0-9.]/g, '')) || 0;
    const paid = parseFloat(amountSoldInput.value) || 0;
    const balance = Math.max(0, total - paid);
    balanceLeftDisplay.value = balance > 0 ? formatUGX(balance) : formatUGX(0);
    balanceLeftValue.value = balance;
  }

  function formatAmountInput(input) {
    // Remove non-digit chars
    let val = input.value.replace(/[^\d]/g, '');
    input.value = val ? parseInt(val).toLocaleString('en-UG') : '';
    amountSoldInput.value = val ? parseInt(val) : 0;
    updateBalanceLeft();
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

    const isCredit = statusInput.value === 'credit';

    // For credit: amount paid just needs to be > 0 (deposit)
    // For paid: amount must cover full total
    const priceOk = isCredit ? amt > 0 && amt <= total : amt >= total;

    // Enable submit only if valid
    submitBtn.disabled = !(sufficientStock && priceOk);
  }

  function confirmSubmit() {
    if (!submitBtn.disabled) {
      document.getElementById('confirmModal').style.display = 'flex';
    }
  }

  function closeModal() {
    document.getElementById('confirmModal').style.display = 'none';
  }

  function startNewSale() {
    // Hide the success alert
    const successAlert = document.querySelector('.alert-success[data-no-dismiss]');
    if (successAlert) successAlert.style.display = 'none';
    // Close any open receipt modal
    const receiptModal = document.getElementById('receiptModal');
    if (receiptModal) {
      const modalInstance = bootstrap.Modal.getInstance(receiptModal);
      if (modalInstance) modalInstance.hide();
    }
    // Reset form
    document.getElementById('saleForm').reset();
    document.querySelectorAll('#saleForm input[type="hidden"]').forEach(el => el.value = '');
    productSelect.selectedIndex = 0;
    unitSelect.value = 'piece';
    discountInput.value = '0';
    discountContainer.style.display = 'none';
    setPaymentMode('paid');
    updateProductDetails();
    if (typeof tomSelect !== 'undefined') tomSelect.clear();
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

  // Credit / Paid toggle
  function setPaymentMode(mode) {
    const paidBtn = document.getElementById('paidBtn');
    const creditBtn = document.getElementById('creditBtn');

    if (mode === 'credit') {
      paidBtn.classList.remove('active');
      creditBtn.classList.add('active');
      statusInput.value = 'credit';
      creditHint.style.display = 'inline';
      clientNameField.style.display = 'block';
      balanceField.style.display = 'block';
      amountLabel.textContent = '{{ __("Deposit Paid (UGX)") }}';
      confirmMsg.style.display = 'none';
      creditConfirmMsg.style.display = 'block';
    } else {
      creditBtn.classList.remove('active');
      paidBtn.classList.add('active');
      statusInput.value = 'paid';
      creditHint.style.display = 'none';
      clientNameField.style.display = 'none';
      balanceField.style.display = 'none';
      amountLabel.textContent = '{{ __("Amount Paid (UGX)") }}';
      confirmMsg.style.display = 'block';
      creditConfirmMsg.style.display = 'none';
    }
    validateForm();
  }

  // Initial call
  updateProductDetails();

  // Backdrop click & Escape to close modal
  document.addEventListener('click', function(event) {
    const modal = document.getElementById('confirmModal');
    if (modal.style.display === 'flex' && event.target === modal) {
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
  const tomSelect = new TomSelect('#product_id', {
    create: false,
    sortField: { field: 'text', direction: 'asc' }
  });
</script>
@endsection
