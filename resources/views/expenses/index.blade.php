@extends('layouts.app')

@section('head')
<style>
    .expense-thead th {
        color: #1d4ed8;
        background-color: #eff6ff;
    }
    [data-theme="dark"] .expense-thead th {
        color: #93c5fd;
        background-color: rgba(30, 58, 95, 0.3);
    }
    .admin-expense-thead th {
        color: #15803d;
        background-color: #f0fdf4;
    }
    [data-theme="dark"] .admin-expense-thead th {
        color: #86efac;
        background-color: rgba(22, 101, 52, 0.25);
    }
    .expense-subtitle {
        font-size: 0.875rem;
        font-weight: 700;
        color: #374151;
        margin-top: 0.25rem;
    }
    [data-theme="dark"] .expense-subtitle {
        color: #e5e7eb;
    }
    .expense-section-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #111827;
    }
    [data-theme="dark"] .expense-section-title {
        color: #f3f4f6;
    }
    .expense-empty-text {
        font-size: 0.875rem;
        font-weight: 600;
        color: #4b5563;
    }
    [data-theme="dark"] .expense-empty-text {
        color: #d1d5db;
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ __('Expenses') }}</h1>
            <p class="expense-subtitle">{{ __('Manage employee and admin expenses') }}</p>
        </div>
        <a href="/admin/operational-costs" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus"></i> {{ __('Add Expense') }}
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Employee Expenses -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="expense-section-title flex items-center gap-2">
                <i class="fas fa-users text-blue-600"></i> {{ __('Employee Expenses') }}
            </h3>
        </div>

        @if($expenses->isEmpty())
            <div class="px-5 py-8 text-center expense-empty-text">
                <i class="fas fa-receipt text-2xl mb-2 block"></i>
                {{ __('No employee expenses recorded yet.') }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="expense-thead text-left text-xs uppercase">
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">{{ __('Employee') }}</th>
                            <th class="px-5 py-3">{{ __('Title') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Amount (UGX)') }}</th>
                            <th class="px-5 py-3">{{ __('Category') }}</th>
                            <th class="px-5 py-3">{{ __('Date') }}</th>
                            <th class="px-5 py-3">{{ __('Description') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php $grandTotal = 0; $employeeTotals = []; @endphp
                        @foreach ($expenses as $expense)
                        @php
                            $empName = $expense->employee->name ?? __('N/A');
                            $grandTotal += $expense->amount;
                            $employeeTotals[$empName] = ($employeeTotals[$empName] ?? 0) + $expense->amount;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-gray-400">{{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}</td>
                            <td class="px-5 py-3 font-medium">{{ $empName }}</td>
                            <td class="px-5 py-3">{{ $expense->title }}</td>
                            <td class="px-5 py-3 text-right font-medium">{{ number_format($expense->amount) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-gray-400 max-w-[200px] truncate">{{ $expense->description ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-900/50">
                        @foreach($employeeTotals as $emp => $total)
                        <tr class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <td colspan="2" class="px-5 py-3">{{ __('Total for :name', ['name' => $emp]) }}</td>
                            <td colspan="5" class="px-5 py-3">{{ number_format($total) }} UGX</td>
                        </tr>
                        @endforeach
                        <tr class="text-sm font-bold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20">
                            <td colspan="2" class="px-5 py-3">{{ __('Grand Total') }}</td>
                            <td colspan="5" class="px-5 py-3">{{ number_format($grandTotal) }} UGX</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $expenses->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Admin Expenses -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h3 class="expense-section-title flex items-center gap-2">
                <i class="fas fa-user-tie text-green-600"></i> {{ __('Admin Expenses') }}
            </h3>
        </div>

        @if($adminExpenses->isEmpty())
            <div class="px-5 py-8 text-center expense-empty-text">
                <i class="fas fa-receipt text-2xl mb-2 block"></i>
                {{ __('No admin expenses recorded yet.') }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="admin-expense-thead text-left text-xs uppercase">
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">{{ __('Title') }}</th>
                            <th class="px-5 py-3 text-right">{{ __('Amount (UGX)') }}</th>
                            <th class="px-5 py-3">{{ __('Category') }}</th>
                            <th class="px-5 py-3">{{ __('Date') }}</th>
                            <th class="px-5 py-3">{{ __('Description') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php $adminGrandTotal = 0; @endphp
                        @foreach($adminExpenses as $adminExpense)
                        @php $adminGrandTotal += $adminExpense->amount; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-5 py-3 text-gray-400">{{ $loop->iteration + ($adminExpenses->currentPage() - 1) * $adminExpenses->perPage() }}</td>
                            <td class="px-5 py-3 font-medium">{{ $adminExpense->title }}</td>
                            <td class="px-5 py-3 text-right font-medium">{{ number_format($adminExpense->amount) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $adminExpense->category }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ \Carbon\Carbon::parse($adminExpense->date)->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-gray-400 max-w-[200px] truncate">{{ $adminExpense->description ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-900/50">
                        <tr class="text-sm font-bold text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-900/20">
                            <td colspan="2" class="px-5 py-3">{{ __('Grand Total') }}</td>
                            <td colspan="4" class="px-5 py-3">{{ number_format($adminGrandTotal) }} UGX</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $adminExpenses->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
