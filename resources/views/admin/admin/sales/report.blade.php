@extends('layouts.app')

@section('head')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid px-3 py-4">
  <!-- Header Section -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
          <h1 class="h2 fw-bold text-gradient text-primary mb-2">
            <i class="bi bi-bar-chart-line-fill me-3"></i>Sales & Expenses Dashboard
          </h1>
          <p class="text-muted mb-0">Comprehensive overview of employee performance and financial metrics</p>
        </div>

        <!-- Date Range Info -->
        @if(isset($start) && isset($end))
          <div class="bg-light rounded-3 px-4 py-3 border">
            <div class="text-sm text-muted">Reporting Period</div>
            <div class="fw-semibold text-dark">
              {{ $start->format('M d, Y') }} - {{ $end->format('M d, Y') }}
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Filter Section -->
  <div class="card border-0 shadow-lg mb-5">
    <div class="card-header bg-gradient-primary text-white py-3">
      <h5 class="mb-0"><i class="bi bi-funnel-fill me-2"></i>Filter Report</h5>
    </div>
    <div class="card-body p-4">
      <form method="GET" action="{{ route('sales.report') }}">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label for="period" class="form-label fw-semibold text-dark">Time Period</label>
            <select name="period" id="period" class="form-select border-2 py-2" onchange="toggleCustomDates()">
              <option value="daily" {{ ($period ?? '') === 'daily' ? 'selected' : '' }}>📅 Today</option>
              <option value="weekly" {{ ($period ?? '') === 'weekly' ? 'selected' : '' }}>📊 This Week</option>
              <option value="monthly" {{ ($period ?? '') === 'monthly' ? 'selected' : '' }}>📈 This Month</option>
              <option value="custom" {{ ($period ?? '') === 'custom' ? 'selected' : '' }}>🎯 Custom Range</option>
            </select>
          </div>

          <!-- Custom Date Pickers -->
          <div class="col-md-2" id="fromDateDiv" style="display: {{ ($period ?? '') === 'custom' ? 'block' : 'none' }};">
            <label for="from" class="form-label fw-semibold text-dark">From Date</label>
            <input type="date" name="from" id="from" class="form-control border-2 py-2"
                   value="{{ isset($from) ? \Carbon\Carbon::parse($from)->format('Y-m-d') : '' }}">
          </div>

          <div class="col-md-2" id="toDateDiv" style="display: {{ ($period ?? '') === 'custom' ? 'block' : 'none' }};">
            <label for="to" class="form-label fw-semibold text-dark">To Date</label>
            <input type="date" name="to" id="to" class="form-control border-2 py-2"
                   value="{{ isset($to) ? \Carbon\Carbon::parse($to)->format('Y-m-d') : '' }}">
          </div>

          <div class="col-md-5 d-flex gap-2 align-items-end">
            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
              <i class="bi bi-search me-2"></i>Generate Report
            </button>
            <a href="{{ route('sales.report') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold">
              <i class="bi bi-arrow-clockwise me-2"></i>Reset
            </a>
            <button type="button" class="btn btn-success px-4 py-2 fw-semibold ms-auto" onclick="window.print()">
              <i class="bi bi-printer me-2"></i>Print
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Summary Cards -->
  @if(!$groupedByEmployee->isEmpty())
  <div class="row mb-5">
    <div class="col-md-4 mb-3">
      <div class="card border-0 bg-gradient-success text-white shadow-lg h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <i class="bi bi-currency-dollar fs-1"></i>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="card-title text-white-50 mb-1">Total Sales</h6>
              <h3 class="fw-bold mb-0">{{ businessCurrency() }} {{ number_format($totalSalesAmount, 0) }}</h3>
              <small class="text-white-70">{{ $groupedByEmployee->count() }} employees</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card border-0 bg-gradient-info text-white shadow-lg h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <i class="bi bi-people fs-1"></i>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="card-title text-white-50 mb-1">Active Employees</h6>
              <h3 class="fw-bold mb-0">{{ $groupedByEmployee->count() }}</h3>
              <small class="text-white-70">With sales records</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-3">
      <div class="card border-0 bg-gradient-warning text-dark shadow-lg h-100">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
              <i class="bi bi-box-seam fs-1"></i>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="card-title text-dark-50 mb-1">Products Sold</h6>
              <h3 class="fw-bold mb-0">{{ $mostSoldProducts->count() }}</h3>
              <small class="text-dark-70">Different items</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Sales Data -->
  @if($groupedByEmployee->isEmpty())
    <div class="card border-0 shadow-lg">
      <div class="card-body text-center py-5">
        <i class="bi bi-graph-up text-muted fs-1 mb-3 d-block"></i>
        <h4 class="text-muted mb-3">No Sales Data Found</h4>
        <p class="text-muted mb-4">No sales records available for the selected period.</p>
        <a href="{{ route('sales.report') }}" class="btn btn-primary">
          <i class="bi bi-arrow-clockwise me-2"></i>View All Sales
        </a>
      </div>
    </div>
  @else
    <!-- Employee Sales Performance -->
    <div class="card border-0 shadow-lg mb-5">
      <div class="card-header bg-white py-3 border-bottom">
        <h4 class="mb-0 text-dark fw-bold">
          <i class="bi bi-trophy-fill text-warning me-2"></i>Employee Sales Performance
        </h4>
      </div>
      <div class="card-body p-0">
        @foreach($groupedByEmployee as $employeeName => $data)
          <div class="border-bottom">
            <div class="p-4 bg-light">
              <div class="row align-items-center">
                <div class="col-md-6">
                  <h5 class="mb-1 fw-bold text-dark">
                    <i class="bi bi-person-circle text-primary me-2"></i>{{ $employeeName }}
                  </h5>
                  <small class="text-muted">{{ count($data['products']) }} products sold</small>
                </div>
                <div class="col-md-6 text-md-end">
                  <span class="badge bg-success fs-6 py-2 px-3">
                    Total Sales: {{ businessCurrency() }} {{ number_format($data['total_sales'], 0) }}
                  </span>
                </div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-4">Product Name</th>
                    <th>Unit Price</th>
                    <th>Quantity Sold</th>
                    <th class="text-end pe-4">Total Revenue</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($data['products'] as $productName => $productData)
                    <tr>
                      <td class="ps-4">
                        <i class="bi bi-box me-2 text-primary"></i>{{ $productName }}
                      </td>
                      <td>
                        <span class="fw-semibold">{{ businessCurrency() }} {{ number_format($productData['price'], 0) }}</span>
                      </td>
                      <td>
                        <span class="badge bg-primary rounded-pill">{{ number_format($productData['quantity_sold']) }}</span>
                      </td>
                      <td class="text-end pe-4 fw-bold text-success">
                        {{ businessCurrency() }} {{ number_format($productData['total_sales'], 0) }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <div class="row">
    <!-- Most Sold Products -->
    <div class="col-lg-6 mb-4">
      <div class="card border-0 shadow-lg h-100">
        <div class="card-header bg-gradient-success text-white py-3">
          <h5 class="mb-0"><i class="bi bi-star-fill me-2"></i>Top Selling Products</h5>
        </div>
        <div class="card-body">
          @if($mostSoldProducts->isEmpty())
            <div class="text-center py-4 text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2"></i>
              <p class="mb-0">No sales data available</p>
            </div>
          @else
            <div class="list-group list-group-flush">
              @foreach($mostSoldProducts->take(8) as $productName => $data)
                <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                  <div class="d-flex align-items-center">
                    <span class="badge bg-success me-3">{{ $loop->iteration }}</span>
                    <span class="fw-semibold text-dark">{{ Str::limit($productName, 30) }}</span>
                  </div>
                  <span class="badge bg-success rounded-pill fs-6">
                    {{ number_format($data['quantity_sold']) }} sold
                  </span>
                </div>
              @endforeach
            </div>
            @if($mostSoldProducts->count() > 8)
              <div class="text-center mt-3">
                <small class="text-muted">+{{ $mostSoldProducts->count() - 8 }} more products</small>
              </div>
            @endif
          @endif
        </div>
      </div>
    </div>

    <!-- Recent Expenses -->
    <div class="col-lg-6 mb-4">
      <div class="card border-0 shadow-lg h-100">
        <div class="card-header bg-gradient-danger text-white py-3">
          <h5 class="mb-0"><i class="bi bi-wallet-fill me-2"></i>Recent Expenses</h5>
        </div>
        <div class="card-body p-0">
          @if($adminExpenses->isEmpty())
            <div class="text-center py-5 text-muted">
              <i class="bi bi-wallet fs-1 d-block mb-2"></i>
              <p class="mb-0">No expenses recorded</p>
            </div>
          @else
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="ps-4">Employee</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th class="pe-4">Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($adminExpenses->take(6) as $expense)
                    <tr>
                      <td class="ps-4">
                        <div class="d-flex align-items-center">
                          <div class="flex-shrink-0">
                            <i class="bi bi-person-circle text-primary"></i>
                          </div>
                          <div class="flex-grow-1 ms-2">
                            <small class="fw-semibold text-dark">{{ $expense->employee->name ?? 'Unknown' }}</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <small class="text-dark fw-semibold">{{ Str::limit($expense->title, 20) }}</small>
                        @if($expense->description)
                          <br><small class="text-muted">{{ Str::limit($expense->description, 25) }}</small>
                        @endif
                      </td>
                      <td>
                        <span class="badge bg-danger rounded-pill">
                          {{ businessCurrency() }} {{ number_format($expense->amount, 0) }}
                        </span>
                      </td>
                      <td class="pe-4">
                        <small class="text-muted">{{ \Carbon\Carbon::parse($expense->date)->format('M d') }}</small>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @if($adminExpenses->count() > 6)
              <div class="text-center py-3 border-top">
                <small class="text-muted">Showing 6 of {{ $adminExpenses->count() }} expenses</small>
              </div>
            @endif
            <div class="p-3 border-top">
              {{ $adminExpenses->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Toggle custom date fields -->
<script>
  function toggleCustomDates() {
    const period = document.getElementById('period').value;
    const show = period === 'custom';
    document.getElementById('fromDateDiv').style.display = show ? 'block' : 'none';
    document.getElementById('toDateDiv').style.display = show ? 'block' : 'none';
  }

  document.addEventListener('DOMContentLoaded', function() {
    toggleCustomDates();

    // Add smooth animations
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
    });

    setTimeout(() => {
      cards.forEach((card, index) => {
        setTimeout(() => {
          card.style.transition = 'all 0.5s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });
    }, 100);
  });
</script>

<style>
  .text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  }

  .bg-gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
  }

  .bg-gradient-info {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
  }

  .bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important;
  }

  .bg-gradient-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffa8a8 100%) !important;
  }

  .card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  }

  .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
  }

  .table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
  }

  @media (max-width: 768px) {
    .container-fluid {
      padding-left: 15px;
      padding-right: 15px;
    }

    .card-body {
      padding: 1rem !important;
    }

    .table-responsive {
      font-size: 0.875rem;
    }
  }
</style>
@endsection
