@extends('layouts.app') {{-- Or use your actual layout name --}}

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-gray-800">{{ __('Employee Dashboard') }}</h1>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <h2 class="text-sm text-gray-500">{{ __("Today's Sales") }}</h2>
            <p class="text-2xl font-bold text-blue-600" id="emp-today-sales">{{ businessCurrency() }} {{ number_format($todaySales, 0) }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <h2 class="text-sm text-gray-500">{{ __("This Month's Sales") }}</h2>
            <p class="text-2xl font-bold text-green-600" id="emp-monthly-sales">{{ businessCurrency() }} {{ number_format($monthlySales, 0) }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-5 text-center">
            <h2 class="text-sm text-gray-500">{{ __('Total Products Sold') }}</h2>
            <p class="text-2xl font-bold text-indigo-600" id="emp-total-products">{{ number_format($totalProductsSold) }}</p>
        </div>
    </div>

    {{-- Recent Sales Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-700">{{ __('Recent Sales') }}</h3>
            <span class="text-xs text-gray-400 live-indicator">● {{ __('Live') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-auto text-sm text-left">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2">{{ __('Product') }}</th>
                        <th class="px-4 py-2">{{ __('Unit') }}</th>
                        <th class="px-4 py-2">{{ __('Price/Unit') }}</th>
                        <th class="px-4 py-2">{{ __('Quantity') }}</th>
                        <th class="px-4 py-2">{{ __('Total') }}</th>
                        <th class="px-4 py-2">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700" id="emp-recent-sales">
                    @forelse($recentSales as $sale)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium">{{ $sale->product->name ?? __('N/A') }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($sale->product->unit ?? __('piece')) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-600">{{ businessCurrency() }} {{ number_format($sale->product->price ?? 0, 0) }}</td>
                            <td class="px-4 py-2">{{ $sale->quantity }}</td>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ businessCurrency() }} {{ number_format($sale->total_amount, 0) }}</td>
                            <td class="px-4 py-2 text-gray-500 text-xs">{{ $sale->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500" id="emp-no-sales">{{ __('No sales recorded yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function numberLocale(v) { return Number(v).toLocaleString('en-UG', {minimumFractionDigits:0}); }

    function refreshEmployeeDashboard() {
        fetch('/api/employee/dashboard/stats')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                var todayEl = document.getElementById('emp-today-sales');
                var monthEl = document.getElementById('emp-monthly-sales');
                var totalEl = document.getElementById('emp-total-products');
                var tbody = document.getElementById('emp-recent-sales');

                if (todayEl) todayEl.textContent = window.businessCurrency + ' ' + numberLocale(d.todaySales);
                if (monthEl) monthEl.textContent = window.businessCurrency + ' ' + numberLocale(d.monthlySales);
                if (totalEl) totalEl.textContent = numberLocale(d.totalProductsSold);

                if (tbody && d.recentSales && d.recentSales.length) {
                    tbody.innerHTML = d.recentSales.map(function(s) {
                        return '<tr class="border-b hover:bg-gray-50">' +
                            '<td class="px-4 py-2 font-medium">' + s.product + '</td>' +
                            '<td class="px-4 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ __("piece") }}</span></td>' +
'<td class="px-4 py-2 text-gray-600">' + window.businessCurrency + ' ' + numberLocale(s.total_amount / s.quantity) + '</td>' +
                            '<td class="px-4 py-2">' + s.quantity + '</td>' +
                            '<td class="px-4 py-2 font-medium text-gray-900">' + window.businessCurrency + ' ' + numberLocale(s.total_amount) + '</td>' +
                            '<td class="px-4 py-2 text-gray-500 text-xs">' + s.created_at + '</td>' +
                        '</tr>';
                    }).join('');
                }
            })
            .catch(function() {});
    }

    setInterval(refreshEmployeeDashboard, 30000);
});
</script>
@endsection
