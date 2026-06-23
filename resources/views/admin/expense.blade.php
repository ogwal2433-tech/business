@extends('layouts.app')

@section('head')
<style>
    .expense-success-alert {
        background-color: #f0fdf4;
        border-left: 4px solid #22c55e;
        color: #15803d;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    [data-theme="dark"] .expense-success-alert {
        background-color: rgba(22, 101, 52, 0.3);
        border-left-color: #4ade80;
        color: #86efac;
    }
    .expense-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.375rem;
    }
    [data-theme="dark"] .expense-label {
        color: #f3f4f6;
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 style="font-size:1.5rem;font-weight:800;color:#111827;">{{ __('Record Business Expense') }}</h1>
            <p style="font-size:0.875rem;font-weight:700;color:#374151;margin-top:0.25rem;">{{ __('Record an expense for the business') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employee.expenses.index') }}" class="btn btn-light d-inline-flex align-items-center gap-2" style="font-weight:700;">
                <i class="fas fa-list"></i> {{ __('View My Expenses') }}
            </a>
            <a href="javascript:history.back()" class="btn btn-light d-inline-flex align-items-center gap-2" style="font-weight:700;">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="expense-success-alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 style="font-size:0.875rem;font-weight:700;color:#111827;display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-wallet text-blue-600"></i> {{ __('Expense Details') }}
            </h3>
        </div>

        <div class="px-6 py-5">
            <form action="/admin/expenses/store" method="POST" novalidate>
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="title" class="expense-label">{{ __('Title') }}</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="amount" class="expense-label">{{ currency_label('Amount (UGX)') }}</label>
                        <input type="text" name="amount_display" id="amount_display" value="{{ old('amount') ? number_format((float) old('amount')) : '' }}"
                               required
                               class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('amount') border-red-500 @enderror"
                               oninput="formatAmountComma(this)"
                               onkeypress="return allowDigitsAndBackspace(event)">
                        <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
                        @error('amount')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="expense-label">{{ __('Category') }}</label>
                        <select name="category" id="category" required
                                class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-500 @enderror">
                            <option value="" disabled selected>{{ __('-- Select Category --') }}</option>
                            <option value="Transport" {{ old('category') == 'Transport' ? 'selected' : '' }}>{{ __('Transport') }}</option>
                            <option value="Supplies" {{ old('category') == 'Supplies' ? 'selected' : '' }}>{{ __('Supplies') }}</option>
                            <option value="Maintenance" {{ old('category') == 'Maintenance' ? 'selected' : '' }}>{{ __('Maintenance') }}</option>
                            <option value="Miscellaneous" {{ old('category') == 'Miscellaneous' ? 'selected' : '' }}>{{ __('Miscellaneous') }}</option>
                        </select>
                        @error('category')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date" class="expense-label">{{ __('Date') }}</label>
                        <input type="date" name="date" id="date" value="{{ old('date', now()->toDateString()) }}" required
                               class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('date') border-red-500 @enderror">
                        @error('date')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-5">
                    <label for="description" class="expense-label">{{ __('Description (optional)') }}</label>
                    <textarea name="description" id="description" rows="3" placeholder="{{ __('Add any additional notes...') }}"
                              class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="javascript:history.back()" class="btn btn-light d-inline-flex align-items-center gap-2" style="font-weight:700;">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-save"></i> {{ __('Save Expense') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function stripCommas(val) {
        return val.replace(/,/g, '');
    }

    function formatAmountComma(el) {
        let raw = el.value.replace(/[^0-9]/g, '');
        if (raw === '') {
            document.getElementById('amount').value = '';
            return;
        }
        let num = parseInt(raw, 10);
        document.getElementById('amount').value = num;
        el.value = num.toLocaleString('en-US');
    }

    function allowDigitsAndBackspace(e) {
        var key = e.keyCode || e.which;
        if (key === 8 || key === 46 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105)) {
            return true;
        }
        return false;
    }

    document.querySelector('form').addEventListener('submit', function() {
        var display = document.getElementById('amount_display');
        var hidden = document.getElementById('amount');
        hidden.value = stripCommas(display.value);
    });
</script>
@endsection
