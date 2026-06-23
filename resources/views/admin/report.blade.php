@extends('layouts.app')

@section('title', __('Sales Report'))

@section('content')
<div class="w-full p-6">
  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">{{ __('Sales Report') }}</h1>
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      {{ __('Back to Dashboard') }}
    </a>
  </div>

  {{-- Filter Form --}}
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Filter Sales') }}</h2>
    <form method="GET" action="{{ route('sales.report') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('Period') }}</label>
        <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" onchange="this.form.submit()">
          <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>{{ __('Today') }}</option>
          <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>{{ __('This Week') }}</option>
          <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>{{ __('This Month') }}</option>
          <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>{{ __('Custom Range') }}</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('From Date') }}</label>
        <input type="date" name="from" value="{{ $from ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('To Date') }}</label>
        <input type="date" name="to" value="{{ $to ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('Employee') }}</label>
        <select name="employee_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
          <option value="">{{ __('All Employees') }}</option>
          @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">{{ __('Search') }}</label>
        <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('Product or employee...') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
      </div>
      <div class="flex items-end gap-2">
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
          {{ __('Search') }}
        </button>
        <a href="{{ route('sales.report') }}" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium text-center transition-colors">
          {{ __('Reset') }}
        </a>
      </div>
    </form>
  </div>

  {{-- Type Filter Indicator --}}
  @if($type)
  <div class="mb-4 flex items-center gap-3">
    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium {{ $type == 'admin' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
      {{ $type == 'admin' ? __('Showing Admin Sales') : __('Showing Employee Sales') }}
    </span>
    <a href="{{ route('sales.report', request()->except('type')) }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
      {{ __('Show All Sales') }}
    </a>
  </div>
  @endif

  {{-- Summary Cards --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('sales.report', request()->except('type')) }}" class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-100 p-5 shadow-sm hover:shadow-md transition-shadow block {{ !request('type') ? 'ring-2 ring-blue-400' : '' }}">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Total Sales') }}</span>
        <div class="p-2 bg-blue-100 rounded-lg"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
      </div>
      <p class="text-2xl font-bold text-blue-700">UGX {{ number_format($totalSalesAmount) }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ $totalQuantity }} {{ __('items sold') }}</p>
    </a>
    <a href="{{ route('sales.report', array_merge(request()->all(), ['type' => request('type') === 'admin' ? '' : 'admin'])) }}" class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border border-green-100 p-5 shadow-sm hover:shadow-md transition-shadow block {{ request('type') == 'admin' ? 'ring-2 ring-green-400' : '' }}">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Admin Sales') }}</span>
        <div class="p-2 bg-green-100 rounded-lg"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
      </div>
      <p class="text-2xl font-bold text-green-700">UGX {{ number_format($adminTotalAmount) }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ $adminSales->count() }} {{ __('transactions') }}</p>
    </a>
    <a href="{{ route('sales.report', array_merge(request()->all(), ['type' => request('type') === 'employee' ? '' : 'employee'])) }}" class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-100 p-5 shadow-sm hover:shadow-md transition-shadow block {{ request('type') == 'employee' ? 'ring-2 ring-purple-400' : '' }}">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Employee Sales') }}</span>
        <div class="p-2 bg-purple-100 rounded-lg"><svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
      </div>
      <p class="text-2xl font-bold text-purple-700">UGX {{ number_format($employeeTotalAmount) }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ $employeeSales->count() }} {{ __('transactions') }}</p>
    </a>
  </div>

  {{-- Admin Sales Table --}}
  @if($adminSales->isNotEmpty())
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-base font-semibold text-gray-800">{{ __('Admin Sales') }}</h3>
      <span class="text-xs text-gray-500">UGX {{ number_format($adminTotalAmount) }}</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-green-600 text-white">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Date') }}</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Product') }}</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider">{{ __('Quantity') }}</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider">{{ __('Amount') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @foreach($adminSales as $sale)
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">{{ $sale->created_at->format('d M Y H:i') }}</td>
            <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $sale->product->name ?? 'N/A' }}</td>
            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $sale->quantity }}</td>
            <td class="px-4 py-3 text-sm text-green-600 font-semibold text-right">UGX {{ number_format($sale->total_amount) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- Employee Sales Table --}}
  @if($employeeSales->isNotEmpty())
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-base font-semibold text-gray-800">{{ __('Employee Sales') }}</h3>
      <span class="text-xs text-gray-500">UGX {{ number_format($employeeTotalAmount) }}</span>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-blue-600 text-white">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Date') }}</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Product') }}</th>
            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">{{ __('Sold By') }}</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider">{{ __('Quantity') }}</th>
            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider">{{ __('Amount') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @foreach($employeeSales as $sale)
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">{{ $sale->created_at->format('d M Y H:i') }}</td>
            <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $sale->product->name ?? 'N/A' }}</td>
            <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->employee->name ?? 'N/A' }}</td>
            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $sale->quantity }}</td>
            <td class="px-4 py-3 text-sm text-green-600 font-semibold text-right">UGX {{ number_format($sale->total_amount) }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot class="bg-gray-100 font-semibold">
          <tr>
            <td colspan="3" class="px-4 py-3 text-sm text-gray-700 text-right">{{ __('Totals:') }}</td>
            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $employeeSales->sum('quantity') }}</td>
            <td class="px-4 py-3 text-sm text-green-700 text-right">UGX {{ number_format($employeeTotalAmount) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  @endif

  {{-- Employee Breakdown --}}
  @if($groupedByEmployee->isNotEmpty())
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
      <h3 class="text-base font-semibold text-gray-800">{{ __('Sales by Employee') }}</h3>
    </div>
    <div class="p-6 space-y-6">
      @foreach($groupedByEmployee as $empName => $data)
      <div class="border border-gray-200 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
              <span class="text-sm font-semibold text-blue-600">{{ strtoupper(substr($empName, 0, 1)) }}</span>
            </div>
            <span class="font-semibold text-gray-800">{{ $empName }}</span>
          </div>
          <div class="text-right">
            <span class="text-sm font-semibold text-blue-700">UGX {{ number_format($data['total_sales']) }}</span>
            <span class="text-xs text-gray-500 ml-2">({{ $data['total_quantity'] }} {{ __('items') }})</span>
          </div>
        </div>
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Product') }}</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Qty Sold') }}</th>
              <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($data['products'] as $prodName => $prodData)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-sm text-gray-800">{{ $prodName }}</td>
              <td class="px-4 py-2 text-sm text-gray-600 text-right">UGX {{ number_format($prodData['price']) }}</td>
              <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ $prodData['quantity_sold'] }}</td>
              <td class="px-4 py-2 text-sm text-green-600 font-medium text-right">UGX {{ number_format($prodData['total_sales']) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Top Products --}}
  @if($mostSoldProducts->isNotEmpty())
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
      <h3 class="text-base font-semibold text-gray-800">{{ __('Top 5 Products') }}</h3>
    </div>
    <div class="p-6">
      <div class="space-y-3">
        @foreach($mostSoldProducts as $prodName => $data)
        <div class="flex items-center justify-between">
          <div class="flex-1">
            <span class="text-sm font-medium text-gray-800">{{ $prodName }}</span>
            <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mostSoldProducts->max('quantity_sold') > 0 ? ($data['quantity_sold'] / $mostSoldProducts->max('quantity_sold') * 100) : 0 }}%"></div>
            </div>
          </div>
          <div class="text-right ml-4">
            <span class="text-sm font-semibold text-gray-900">{{ $data['quantity_sold'] }} {{ __('sold') }}</span>
            <span class="text-xs text-gray-500 block">UGX {{ number_format($data['total_sales']) }}</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

  {{-- Empty State --}}
  @if($allSales->isEmpty())
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <p class="text-gray-500 font-medium">{{ __('No sales found for the selected period.') }}</p>
    <p class="text-sm text-gray-400 mt-1">{{ __('Try adjusting your filters') }}</p>
  </div>
  @endif
</div>
@endsection