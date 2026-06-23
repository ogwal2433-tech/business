@extends('layouts.app')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container mt-0">
  <div class="card shadow-lg border-0">

    {{-- Header with page title and action buttons --}}
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="bi bi-wallet2"></i> {{ __('Record Business Expense') }}</h4>
      <div>
          <a href="{{ route('employee.expenses.index') }}" class="btn btn-light btn-sm">
          <i class="bi bi-card-list"></i> {{ __('View My Expenses') }}
        </a>
        <a href="/employee/sales/history " class="btn btn-light btn-sm me-2">
          <i class="bi bi-arrow-left-circle"></i> {{ __('Back to Report Section') }}
        </a>
        <a href="{{ route('employee.dashboard') }}" class="btn btn-light btn-sm">
          <i class="bi bi-house-door"></i> {{ __('Back to Dashboard') }}
        </a>
      </div>
    </div>

    {{-- Card body with form --}}
    <div class="card-body">

      {{-- Success message --}}
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      <form action="{{ route('employee.expenses.store') }}" method="POST" class="row g-3" novalidate>
        @csrf

        <div class="col-md-6">
          <label for="title" class="form-label fw-semibold text-primary">{{ __('Title') }}</label>
          <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
          @error('title')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label for="amount_display" class="form-label fw-semibold text-primary">{{ currency_label('Amount (UGX)') }}</label>
          <input type="text" id="amount_display" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') ? number_format((float) old('amount')) : '' }}" required oninput="formatAmountComma(this)" onkeypress="return allowDigitsAndBackspace(event)">
          <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
          @error('amount')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label for="category" class="form-label fw-semibold text-primary">{{ __('Category') }}</label>
          <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
            <option value="" disabled selected>{{ __('-- Select Category --') }}</option>
            <option value="Transport" {{ old('category') == 'Transport' ? 'selected' : '' }}>{{ __('Transport') }}</option>
            <option value="Supplies" {{ old('category') == 'Supplies' ? 'selected' : '' }}>{{ __('Supplies') }}</option>
            <option value="Maintenance" {{ old('category') == 'Maintenance' ? 'selected' : '' }}>{{ __('Maintenance') }}</option>
            <option value="Miscellaneous" {{ old('category') == 'Miscellaneous' ? 'selected' : '' }}>{{ __('Miscellaneous') }}</option>
          </select>
          @error('category')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6">
          <label for="date" class="form-label fw-semibold text-primary">{{ __('Date') }}</label>
          <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', now()->toDateString()) }}" required>
          @error('date')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12">
          <label for="description" class="form-label fw-semibold text-primary">{{ __('Description (optional)') }}</label>
          <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="{{ __('Add any additional notes...') }}">{{ old('description') }}</textarea>
          @error('description')
          <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> {{ __('Save Expense') }}
          </button>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
function formatAmountComma(input) {
  let val = input.value.replace(/[^\d.]/g, '');
  let parts = val.split('.');
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  input.value = parts.join('.');
  document.getElementById('amount').value = val || '0';
}

function allowDigitsAndBackspace(e) {
  const allowed = e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab' || e.key === 'ArrowLeft' || e.key === 'ArrowRight' || e.key === 'Home' || e.key === 'End';
  if (allowed) return true;
  if (/[\d.]/.test(e.key)) return true;
  return false;
}
</script>
@endsection
