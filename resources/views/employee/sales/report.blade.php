@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold text-blue-700 flex items-center gap-2">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      Sales Report
    </h2>
    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-blue-50 text-blue-700 border border-blue-200">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      {{ ucfirst($period ?? 'daily') }} report
    </span>
  </div>

  {{-- Filter Form --}}
  <div class="bg-white border border-gray-200 rounded-xl p-5 mb-6 border-l-4 border-l-blue-600">
    <form method="GET" action="{{ route('employee.sales.report') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
      <div class="md:col-span-3">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
          <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Select Period
        </label>
        <select name="period" id="period" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" onchange="toggleCustomDates()">
          <option value="daily" {{ old('period', $period) == 'daily' ? 'selected' : '' }}>Today (Daily)</option>
          <option value="weekly" {{ old('period', $period) == 'weekly' ? 'selected' : '' }}>This Week</option>
          <option value="monthly" {{ old('period', $period) == 'monthly' ? 'selected' : '' }}>This Month</option>
          <option value="custom" {{ old('period', $period) == 'custom' ? 'selected' : '' }}>Custom Range</option>
        </select>
      </div>

      <div class="md:col-span-3" id="fromDateDiv" style="display: {{ old('period', $period) == 'custom' ? 'block' : 'none' }};">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
          <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
          From Date
        </label>
        <input type="date" name="from" id="from" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" value="{{ old('from', $from) }}">
      </div>

      <div class="md:col-span-3" id="toDateDiv" style="display: {{ old('period', $period) == 'custom' ? 'block' : 'none' }};">
        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
          <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
          To Date
        </label>
        <input type="date" name="to" id="to" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" value="{{ old('to', $to) }}">
      </div>

      <div class="md:col-span-3 flex gap-2">
        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
          </svg>
          Filter
        </button>
        <a href="{{ route('employee.sales.report') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Reset
        </a>
      </div>
    </form>
  </div>

  {{-- Products Sold Card --}}
  <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center justify-between">
      <h5 class="text-white font-semibold flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        Products Sold
      </h5>
      <div class="flex items-center gap-2">
        <span class="text-white/80 text-sm">Total Revenue:</span>
        <span class="bg-white text-blue-700 font-bold px-4 py-1.5 rounded-full text-sm">UGX {{ number_format($totalSalesAmount, 0) }}</span>
      </div>
    </div>

    <div>
      @if($grouped->isEmpty())
        <div class="py-12 text-center">
          <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
          </div>
          <p class="text-gray-500 font-medium">No sales found for this period</p>
          <p class="text-gray-400 text-sm mt-1">Try selecting a different date range</p>
        </div>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500">
              <tr>
                <th class="px-5 py-3 text-left font-medium text-xs uppercase tracking-wider">Product</th>
                <th class="px-5 py-3 text-left font-medium text-xs uppercase tracking-wider">Unit</th>
                <th class="px-5 py-3 text-right font-medium text-xs uppercase tracking-wider">Unit Price</th>
                <th class="px-5 py-3 text-center font-medium text-xs uppercase tracking-wider">Quantity Sold</th>
                <th class="px-5 py-3 text-right font-medium text-xs uppercase tracking-wider">Total Sales</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach($grouped as $productName => $data)
              <tr class="hover:bg-blue-50/40 transition-colors">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $data['product_name'] }}</td>
                <td class="px-5 py-3">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                    {{ $data['unit'] == 'dozen' ? 'bg-yellow-100 text-yellow-800' : ($data['unit'] == 'carton' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ ucfirst($data['unit']) }}
                  </span>
                </td>
                <td class="px-5 py-3 text-right text-gray-600">UGX {{ number_format($data['unit_price'], 0) }}</td>
                <td class="px-5 py-3 text-center">
                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                    {{ $data['total_quantity'] }}
                  </span>
                </td>
                <td class="px-5 py-3 text-right font-bold text-blue-700">UGX {{ number_format($data['total_amount'], 0) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  {{-- Most Sold Products Card --}}
  <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 flex items-center gap-3">
      <div class="bg-white/20 p-2 rounded-lg">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
      </div>
      <h5 class="text-white font-semibold">Most Sold Products</h5>
      <span class="bg-white/20 text-white px-3 py-1 rounded-full text-xs font-medium">
        <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ ucfirst($period ?? 'daily') }}
      </span>
    </div>

    <div>
      @if($mostSoldProducts->isEmpty())
        <div class="py-8 text-center">
          <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          <p class="text-sm text-gray-500">No sales data available</p>
        </div>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500">
              <tr>
                <th class="px-5 py-3 text-left font-medium text-xs uppercase tracking-wider" width="15%">Rank</th>
                <th class="px-5 py-3 text-left font-medium text-xs uppercase tracking-wider">Product Name</th>
                <th class="px-5 py-3 text-right font-medium text-xs uppercase tracking-wider">Units Sold</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach($mostSoldProducts as $productName => $data)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-5 py-3">
                  @if($loop->iteration == 1)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                      #1
                    </span>
                  @elseif($loop->iteration == 2)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                      #2
                    </span>
                  @elseif($loop->iteration == 3)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      <svg class="w-3 h-3 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                      #3
                    </span>
                  @else
                    <span class="text-sm text-gray-500 font-medium">#{{ $loop->iteration }}</span>
                  @endif
                </td>
                <td class="px-5 py-3 font-medium text-gray-800">{{ $productName }}</td>
                <td class="px-5 py-3 text-right">
                  <span class="font-bold text-blue-700">{{ $data['total_quantity'] }}</span>
                  <span class="text-gray-400 ml-1 text-xs">units</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="bg-gray-50 px-5 py-3 flex items-center justify-between text-xs">
          <span class="text-gray-500">
            <svg class="w-3.5 h-3.5 inline mr-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Top {{ $mostSoldProducts->count() }} products by sales volume
          </span>
          <span class="text-blue-700 font-medium">
            <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            {{ collect($mostSoldProducts)->sum('total_quantity') }} total units
          </span>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
  function toggleCustomDates() {
    const period = document.getElementById('period').value;
    const showCustom = period === 'custom';
    document.getElementById('fromDateDiv').style.display = showCustom ? 'block' : 'none';
    document.getElementById('toDateDiv').style.display = showCustom ? 'block' : 'none';
  }
  document.addEventListener('DOMContentLoaded', toggleCustomDates);
</script>
@endsection
