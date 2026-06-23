@extends('layouts.app')

@section('content')
<div class="container my-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h4 class="text-primary mb-0">
            <i class="bi bi-clipboard-data-fill me-2"></i><strong>{{ __('My Purchases') }}</strong>
        </h4>
        <a href="{{ route('admin.stocks.purchase') }}" class="btn btn-outline-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> {{ __('Record New Purchase') }}
        </a>
    </div>

    <!-- No Records -->
    @if($purchases->isEmpty())
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <div>{{ __('You have no purchase records yet.') }}</div>
        </div>
    @else
        <!-- Grouped Purchases by Date -->
        @foreach($groupedPurchases as $date => $dailyPurchases)
            <div class="mb-4">
                <h6 class="text-secondary">
                    <i class="bi bi-calendar-event me-1"></i>
                    {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                </h6>

                <div class="table-responsive shadow-sm rounded overflow-hidden">
                    <table class="table table-bordered table-striped table-sm align-middle mb-0">
                        <thead class="table-primary text-dark">
                            <tr>
                                <th><i class="bi bi-box-seam"></i> {{ __('Product') }}</th>
                                <th><i class="bi bi-stack"></i> {{ __('Qty') }}</th>
                                <th><i class="bi bi-currency-exchange"></i> {{ __('Price/Unit') }}</th>
                                <th><i class="bi bi-calculator"></i> {{ __('Total') }}</th>
                                <th><i class="bi bi-chat-left-text"></i> {{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyPurchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->product_name }}</td>
                                    <td>{{ $purchase->quantity }}</td>
                                    <td><span class="text-success">UGX {{ number_format($purchase->price_per_unit) }}</span></td>
                                    <td><strong class="text-primary">UGX {{ number_format($purchase->quantity * $purchase->price_per_unit) }}</strong></td>
                                    <td>{{ $purchase->notes ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <!-- Overall Total -->
        <div class="text-end mt-4">
            <h5 class="text-success fw-bold">
                <i class="bi bi-cash-coin me-1"></i>
                {{ __('Total Spent') }}: <span class="text-dark">UGX {{ number_format($overallTotal) }}</span>
            </h5>
        </div>
    @endif

</div>
@endsection
