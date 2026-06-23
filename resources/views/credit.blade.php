@extends('layouts.app')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container mt-0">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-journal-text"></i> {{ __('Credit Sales') }}</h4>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left-circle"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>

        <div class="card-body">

            @if(session('success'))
            <div id="success-alert" class="alert alert-success">{{ session('success') }}</div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const alert = document.getElementById('success-alert');
                    if (alert) {
                        setTimeout(() => {
                            alert.style.transition = 'opacity 0.5s';
                            alert.style.opacity = '0';
                            setTimeout(() => alert.remove(), 500);
                        }, 3000);
                    }
                });
            </script>
            @endif

            @if($creditSales->isEmpty())
                <div class="alert alert-info">{{ __('No credit sales found.') }}</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Client') }}</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Qty') }}</th>
                                <th>{{ __('Amount Paid (UGX)') }}</th>
                                <th>{{ __('Pending Balance (UGX)') }}</th>
                                <th>{{ __('Next Installment') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="credit-sales-tbody">
                            @foreach($creditSales as $sale)
                            <tr id="credit-row-{{ $sale->id }}">
                                <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}</td>
                                <td>{{ ucwords($sale->client_name ?? 'N/A') }}</td>
                                <td>{{ $sale->product->name ?? 'N/A' }}</td>
                                <td>{{ number_format($sale->quantity) }}</td>
                                <td class="cs-paid">UGX {{ number_format($sale->amount_paid, 0, '.', ',') }}</td>
                                <td class="cs-balance">UGX {{ number_format($sale->total_amount - $sale->amount_paid, 0, '.', ',') }}</td>
                                <td>
                                    @php
                                        $latestRepayment = $sale->repayments()->latest()->first();
                                    @endphp
                                    @if($latestRepayment && $latestRepayment->next_installment_date)
                                        {{ \Carbon\Carbon::parse($latestRepayment->next_installment_date)->format('d M Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($sale->status === 'credit')
                                        <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                    @elseif($sale->status === 'paid')
                                        <span class="badge bg-success">{{ __('Paid') }}</span>
                                    @elseif($sale->status === 'returned')
                                        <span class="badge bg-secondary">{{ __('Returned') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#repaymentHistoryModal{{ $sale->id }}" title="{{ __('View Repayment History') }}">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <div class="modal fade" id="repaymentHistoryModal{{ $sale->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">{{ __('Repayment History for') }} {{ ucwords($sale->client_name ?? 'N/A') }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @php
                                                        $repayments = $sale->repayments()->orderBy('created_at','desc')->get();
                                                    @endphp
                                                    @if($repayments->isEmpty())
                                                        <div class="alert alert-info">{{ __('No repayments recorded yet.') }}</div>
                                                    @else
                                                        <table class="table table-sm table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Date') }}</th>
                                                                    <th>{{ __('Amount (UGX)') }}</th>
                                                                    <th>{{ __('Next Installment') }}</th>
                                                                    <th>{{ __('Note') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($repayments as $repayment)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::parse($repayment->created_at)->format('d M Y') }}</td>
                                                                    <td>UGX {{ number_format($repayment->amount, 0, '.', ',') }}</td>
                                                                    <td>{{ $repayment->next_installment_date ? \Carbon\Carbon::parse($repayment->next_installment_date)->format('d M Y') : 'N/A' }}</td>
                                                                    <td>{{ $repayment->note ?? '-' }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($sale->status === 'credit')
                                        <button type="button" class="btn btn-primary btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#repaymentModal{{ $sale->id }}">
                                            <i class="bi bi-cash"></i> {{ __('Record Repayment') }}
                                        </button>

                                        <div class="modal fade" id="repaymentModal{{ $sale->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('Record Repayment for') }} {{ ucwords($sale->client_name ?? 'N/A') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <form action="{{ route('repayment') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">{{ __('Repayment Amount (UGX)') }}</label>
                                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                                    <input type="text" name="amount" class="form-control" inputmode="numeric" required placeholder="{{ __('Enter amount') }}">
                                                                    <span class="text-muted small">{{ __('Balance:') }} UGX {{ number_format($sale->total_amount - $sale->amount_paid) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">{{ __('Next Installment Date') }}</label>
                                                                <input type="date" name="next_installment_date" class="form-control" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">{{ __('Note (Optional)') }}</label>
                                                                <textarea name="note" class="form-control" rows="3"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('Submit Repayment') }}</button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-warning btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editNextInstallmentModal{{ $sale->id }}">
                                            <i class="bi bi-pencil-square"></i> {{ __('Edit Next Date') }}
                                        </button>

                                        <div class="modal fade" id="editNextInstallmentModal{{ $sale->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ __('Edit Next Installment for') }} {{ ucwords($sale->client_name ?? 'N/A') }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <form action="{{ route('repayment.updateNextInstallment', $sale->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')

                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">{{ __('Next Installment Date') }}</label>
                                                                <input type="date" name="next_installment_date" class="form-control"
                                                                       value="{{ $latestRepayment->next_installment_date ?? '' }}" required>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                            <button type="submit" class="btn btn-warning">{{ __('Update') }}</button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>

                                        <form method="POST" action="{{ route('credit.sales.returned', $sale->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-outline-secondary btn-sm" onclick="return confirm('{{ __('Mark as returned?') }}')">
                                                <i class="bi bi-arrow-counterclockwise"></i> {{ __('Returned') }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if($creditSales->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $creditSales->withQueryString()->links() }}
            </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountInputs = document.querySelectorAll('input[name="amount"]');
        amountInputs.forEach(input => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'amount_raw';
            input.parentNode.insertBefore(hidden, input.nextSibling);

            input.addEventListener('input', function () {
                let raw = this.value.replace(/,/g, '').replace(/[^\d]/g, '');
                if (raw !== '') {
                    const num = parseInt(raw, 10);
                    this.value = num.toLocaleString('en-US');
                    hidden.value = num;
                } else {
                    this.value = '';
                    hidden.value = '';
                }
            });

            input.closest('form').addEventListener('submit', function () {
                if (hidden.value) {
                    input.value = hidden.value;
                }
            });
        });
    });

    // Real-time credit sales balance updates
    var lastCreditCheck = new Date().toISOString();
    setInterval(function() {
        fetch('/api/credit-sales/updates?since=' + encodeURIComponent(lastCreditCheck))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.sales && data.sales.length > 0) {
                    lastCreditCheck = new Date().toISOString();
                    data.sales.forEach(function(s) {
                        var row = document.getElementById('credit-row-' + s.id);
                        if (row) {
                            var paidCells = row.querySelectorAll('.cs-paid');
                            var balCells = row.querySelectorAll('.cs-balance');
                            if (paidCells.length) paidCells[0].textContent = 'UGX ' + Number(s.paid_amount).toLocaleString('en-UG', {minimumFractionDigits:0});
                            if (balCells.length) balCells[0].textContent = 'UGX ' + Number(s.balance).toLocaleString('en-UG', {minimumFractionDigits:0});
                        }
                    });
                }
            })
            .catch(function() {});
    }, 30000);
</script>
@endsection
