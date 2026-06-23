@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
  {{-- Header --}}
  <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl px-6 py-4 flex items-center justify-between mb-6">
    <h4 class="text-white font-semibold text-lg flex items-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      {{ __('Sales History') }}
    </h4>
    <div class="flex gap-2">
      <a href="/expenses/create" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        {{ __('Record Expense') }}
      </a>
      <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        {{ __('Dashboard') }}
      </a>
    </div>
  </div>

  {{-- Filter Section --}}
  <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6 relative before:absolute before:left-0 before:top-0 before:h-full before:w-1 before:bg-blue-600 before:rounded-l-xl">
    <form method="GET" action="{{ route('employee.sales.history') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
      <div class="md:col-span-3">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
          <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          {{ __('Period') }}
        </label>
        <select name="period" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" onchange="this.form.submit()">
          <option value="today" {{ request('period')=='today' ? 'selected' : '' }}>{{ __('Today') }}</option>
          <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>{{ __('This Week') }}</option>
          <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>{{ __('This Month') }}</option>
          <option value="all" {{ request('period')=='all' ? 'selected' : '' }}>{{ __('All Time') }}</option>
        </select>
      </div>

      <div class="md:col-span-4">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
          <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          {{ __('Product Search') }}
        </label>
        <div class="flex">
          <div class="flex items-center border border-r-0 border-gray-300 rounded-l-lg px-3 bg-white">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <input type="text" name="search" class="flex-1 border border-gray-300 rounded-r-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="{{ __('Search by product name...') }}" value="{{ request('search') }}">
        </div>
      </div>

      <div class="md:col-span-2 flex items-end">
        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          {{ __('Search') }}
        </button>
      </div>

      <div class="md:col-span-3 flex items-end">
        <a href="{{ route('employee.sales.report', request()->all()) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          {{ __('Export Report') }}
        </a>
      </div>
    </form>
  </div>

  @if($sales->isEmpty())
    {{-- Empty State --}}
    <div class="bg-white border border-gray-200 rounded-xl py-12 text-center">
      <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
      </div>
      <h5 class="font-semibold text-gray-700 mb-1">{{ __('No transactions found') }}</h5>
      <p class="text-sm text-gray-500 mb-3">
        {{ __('No sales recorded for') }} <span class="font-medium text-blue-700">{{ ucfirst(request('period', 'today')) }}</span>
        @if(request('search'))
          {{ __('matching') }} "{{ request('search') }}"
        @endif
      </p>
      <a href="{{ route('employee.sales.history') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        {{ __('Clear filters') }}
      </a>
    </div>
  @else

  {{-- Sales Table --}}
  <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider">#</th>
            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider">{{ __('Product') }}</th>
            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider">{{ __('Unit') }}</th>
            <th class="px-4 py-3 text-right font-medium text-xs uppercase tracking-wider">{{ __('Unit Price') }}</th>
            <th class="px-4 py-3 text-center font-medium text-xs uppercase tracking-wider">{{ __('Quantity') }}</th>
            <th class="px-4 py-3 text-right font-medium text-xs uppercase tracking-wider">{{ __('Total Amount') }}</th>
            <th class="px-4 py-3 text-left font-medium text-xs uppercase tracking-wider">{{ __('Date & Time') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach ($sales as $sale)
          <tr class="hover:bg-blue-50/40 transition-colors">
            <td class="px-4 py-3 text-gray-500 text-xs">#{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
            <td class="px-4 py-3 font-medium text-gray-800">{{ $sale->product->name }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                {{ ($sale->product->unit ?? 'piece') == 'dozen' ? 'bg-yellow-100 text-yellow-800' : (($sale->product->unit ?? 'piece') == 'carton' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                {{ ucfirst($sale->product->unit ?? 'piece') }}
              </span>
            </td>
            <td class="px-4 py-3 text-right text-gray-600">UGX {{ number_format($sale->product->price) }}</td>
            <td class="px-4 py-3 text-center">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                {{ $sale->quantity }}
              </span>
            </td>
            <td class="px-4 py-3 text-right font-semibold text-gray-900">UGX {{ number_format($sale->total_amount) }}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div>
                  <div class="text-sm font-medium text-gray-800">{{ $sale->created_at->format('d M Y') }}</div>
                  <div class="text-xs text-gray-500">{{ $sale->created_at->format('h:i A') }}</div>
                </div>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Summary & Pagination --}}
    <div class="px-4 py-4 border-t border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="flex items-center gap-3 bg-blue-50/60 border border-blue-100 rounded-xl px-5 py-3">
        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center shadow-sm">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <div>
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Total Revenue') }}</span>
          <div class="text-xl font-bold text-blue-800">UGX {{ number_format($totalSales) }}</div>
          <span class="text-xs text-gray-500">{{ $sales->total() }} {{ __('transactions') }}</span>
        </div>
      </div>
      <div class="w-full md:w-auto">
        {{ $sales->withQueryString()->links() }}
      </div>
    </div>
  </div>

  {{-- Most Sold Products --}}
  <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
      <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
      </div>
      <h5 class="font-semibold text-gray-800">{{ __('Top Performing Products') }}</h5>
      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ ucfirst(request('period', __('this period'))) }}
      </span>
    </div>

    @if($mostSold->isEmpty())
      <div class="py-8 text-center">
        <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p class="text-sm text-gray-500">No sales data available for this period</p>
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500">
            <tr>
              <th class="px-5 py-3 text-left font-medium text-xs uppercase tracking-wider">Rank</th>
              <th class="px-5 py-3 text-left font-medium text-xs uppercase tracking-wider">Product Name</th>
              <th class="px-5 py-3 text-right font-medium text-xs uppercase tracking-wider">Units Sold</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach ($mostSold as $index => $product)
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3">
                @if($index == 0)
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    1
                  </span>
                @elseif($index == 1)
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    2
                  </span>
                @elseif($index == 2)
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="w-3 h-3 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    3
                  </span>
                @else
                  <span class="text-sm text-gray-500 font-medium">#{{ $index + 1 }}</span>
                @endif
              </td>
              <td class="px-5 py-3 font-medium text-gray-800">{{ $product->name }}</td>
              <td class="px-5 py-3 text-right">
                <span class="font-bold text-blue-700">{{ $product->total_quantity }}</span>
                <span class="text-gray-400 ml-1 text-xs">units</span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="px-5 py-3 bg-gray-50 flex items-center justify-between text-xs">
        <span class="text-gray-500">
          <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Top {{ $mostSold->count() }} products by sales volume
        </span>
        <span class="text-blue-700 font-medium">
          <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
          </svg>
          {{ $mostSold->sum('total_quantity') }} total units sold
        </span>
      </div>
    @endif
  </div>
  @endif
</div>

<script>
  (function() {
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');

    if (searchInput) {
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          if (this.value.trim() !== '{{ request('search') }}') {
            this.form.submit();
          }
        }, 800);
      });
    }

    const filterSelects = document.querySelectorAll('select[name="period"]');
    filterSelects.forEach(select => {
      select.addEventListener('change', function() {
        this.form.submit();
      });
    });
  })();
</script>
<style>
  .pagination { display: flex; gap: 4px; flex-wrap: wrap; }
  .pagination .page-link { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 0.5rem; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; font-size: 0.875rem; font-weight: 500; transition: all 0.15s; background: white; text-decoration: none; }
  .pagination .page-link:hover { background: #dbeafe; color: #1e40af; border-color: #bfdbfe; }
  .pagination .active .page-link { background: #2563eb; color: white; border-color: #2563eb; box-shadow: 0 2px 4px rgba(37,99,235,0.2); }
  .pagination .disabled .page-link { opacity: 0.5; cursor: default; }
</style>
@endsection
