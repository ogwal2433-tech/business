@extends('layouts.app')

@section('content')
<div class="container mt-0">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class="bi bi-pencil-square"></i> {{ __('Edit Expense') }}</h4>
      <a href="{{ route('employee.expenses.index') }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left-circle"></i> {{ __('Back to Expenses') }}
      </a>
    </div>

    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li><i class="bi bi-exclamation-circle"></i> {{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('employee.expenses.update', $expense->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label for="title" class="form-label text-primary">{{ __('Title') }}</label>
          <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                 value="{{ old('title', $expense->title) }}" required>
          @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="amount_display" class="form-label text-primary">{{ __('Amount (UGX)') }}</label>
          <input type="text" id="amount_display" class="form-control @error('amount') is-invalid @enderror"
                 value="{{ old('amount', $expense->amount) ? number_format((float) old('amount', $expense->amount)) : '' }}"
                 required oninput="formatAmountComma(this)" onkeypress="return allowDigitsAndBackspace(event)">
          <input type="hidden" name="amount" id="amount" value="{{ old('amount', $expense->amount) }}">
          @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="category" class="form-label text-primary">{{ __('Category') }}</label>
          <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
            <option value="">{{ __('-- Select Category --') }}</option>
            @foreach(['Transport', 'Supplies', 'Maintenance', 'Miscellaneous'] as $category)
              <option value="{{ $category }}" {{ old('category', $expense->category) === $category ? 'selected' : '' }}>
                {{ __($category) }}
              </option>
            @endforeach
          </select>
          @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="date" class="form-label text-primary">{{ __('Date') }}</label>
          <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror"
                 value="{{ old('date', $expense->date) }}" required>
          @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="description" class="form-label text-primary">{{ __('Description (optional)') }}</label>
          <textarea name="description" id="description" rows="3"
                    class="form-control @error('description') is-invalid @enderror"
                    placeholder="{{ __('Add any additional notes...') }}">{{ old('description', $expense->description) }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle"></i> {{ __('Update Expense') }}
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
