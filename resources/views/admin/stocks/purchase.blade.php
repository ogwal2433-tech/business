@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <h4 class="mb-0 mt-0">
        <i class="bi bi-bag-plus-fill me-2 text-primary"></i>
        <strong>{{ __('Record New Purchase') }}</strong>
        <span class="text-muted fs-6 fw-normal ms-2">{{ __('(Temporary store — transfer to inventory later)') }}</span>
    </h4>
    <a href="{{ route('purchases.view') }}" class="btn btn-outline-info shadow-sm">
        <i class="bi bi-eye-fill me-1"></i> {{ __('View My Purchases') }}
    </a>
</div>


    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Purchase Form -->
    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf
        <div id="productEntries">
            <div class="row g-3 border p-3 mb-3 rounded product-entry shadow-sm bg-light">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Product Name') }}</label>
                    <input type="text" name="product_name[]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Quantity') }}</label>
                    <input type="number" name="quantity[]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ currency_label('Price/Unit (UGX)') }}</label>
                    <input type="number" step="0.01" name="price_per_unit[]" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Purchase Date') }}</label>
                    <input type="date" name="purchase_date[]" class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end btn-col">
                    {{-- <button type="button" class="btn btn-success w-100 add-entry">
                        <i class="bi bi-plus-circle me-1"></i> Add More
                    </button> --}}
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="mb-3">
            <label class="form-label">{{ __('Notes (optional)') }}</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Any additional remarks...') }}"></textarea>
        </div>

        <!-- Submit Button -->
        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save-fill me-1"></i> {{ __('Save Purchases') }}
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
var addMoreText = '{{ __('Add More') }}';
var removeText = '{{ __('Remove') }}';

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('productEntries');

    function createAddButton() {
        return `
            <button type="button" class="btn btn-success w-100 add-entry">
                <i class="bi bi-plus-circle me-1"></i> ${addMoreText}
            </button>
        `;
    }

    function createRemoveButton() {
        return `
            <button type="button" class="btn btn-danger w-100 remove-entry">
                <i class="bi bi-trash me-1"></i> ${removeText}
            </button>
        `;
    }

    function refreshButtons() {
        const entries = container.querySelectorAll('.product-entry');
        entries.forEach((entry, index) => {
            const btnCol = entry.querySelector('.btn-col');
            if (index === entries.length - 1) {
                btnCol.innerHTML = createAddButton();
            } else {
                btnCol.innerHTML = createRemoveButton();
            }
        });
    }

    container.addEventListener('click', function (e) {
        if (e.target.closest('.add-entry')) {
            e.preventDefault();
            const currentRow = e.target.closest('.product-entry');
            const clone = currentRow.cloneNode(true);

            // Clear all inputs in the cloned row
            clone.querySelectorAll('input').forEach(input => input.value = '');

            container.appendChild(clone);
            refreshButtons();
        }

        if (e.target.closest('.remove-entry')) {
            e.preventDefault();
            const row = e.target.closest('.product-entry');
            row.remove();

            // Ensure at least one row remains
            if (container.querySelectorAll('.product-entry').length === 0) {
                const newRow = row.cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                container.appendChild(newRow);
            }

            refreshButtons();
        }
    });

    refreshButtons(); // Make sure buttons are correct on page load
});
</script>
@endsection
