@extends('admins::layouts.app')


@section('title', 'Báo cáo')

@section('content')
@php
    // Bảo đảm $from / $to luôn là Carbon, dù controller gửi chuỗi hay không gửi gì
    $from = $from ?? request('from');
    $to   = $to   ?? request('to');

    $from = ($from instanceof \Carbon\Carbon)
        ? $from
        : ($from ? \Carbon\Carbon::parse($from) : \Carbon\Carbon::now()->subDays(30));

    $to = ($to instanceof \Carbon\Carbon)
        ? $to
        : ($to ? \Carbon\Carbon::parse($to) : \Carbon\Carbon::now());

    // Phòng khi thiếu dữ liệu khác
    $labels        = $labels        ?? [];
    $series        = $series        ?? [];
    $paymentLabels = $paymentLabels ?? [];
    $paymentData   = $paymentData   ?? [];
@endphp

<style>
/* ====== Filter bar & KPI ====== */
.report-filter{
    border-radius:16px; padding:12px;
    background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08);
    box-shadow: var(--shadow);
}
.kpi{
    border-radius:16px;
    background:linear-gradient(140deg, rgba(196,111,59,.10), rgba(78,107,82,.08) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08);
    box-shadow: var(--shadow);
    padding:14px 14px;
    transition:transform .18s ease, box-shadow .18s ease;
}
.kpi:hover{ transform:translateY(-2px); box-shadow:0 16px 40px rgba(0,0,0,.12) }
.kpi .label{ font-size:.85rem; color:#7d726c }
.kpi .value{ font-size:1.35rem; font-weight:700 }

/* Cards */
.card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
.card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }

/* Tables */
.table td,.table th{ vertical-align:middle }
.table .text-truncate{ max-width:220px }

/* Chart container heights for better responsiveness */
.chart-h-280{ height:280px }
.chart-h-220{ height:220px }

@media (max-width:575.98px){
    .table .text-truncate{ max-width:140px }
}
</style>

<div class="container-fluid">
    {{-- Filter bar --}}
    <div class="report-filter mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h1 class="h4 mb-0 fw-bold">Báo cáo tổng hợp</h1>
            <form class="row g-2 align-items-end" method="get">
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Từ ngày</label>
                    <input type="date" class="form-control form-control-sm" name="from" value="{{ $from->toDateString() }}">
                </div>
                <div class="col-6 col-sm-auto">
                    <label class="form-label small text-muted mb-1">Đến ngày</label>
                    <input type="date" class="form-control form-control-sm" name="to" value="{{ $to->toDateString() }}">
                </div>
                <div class="col-12 col-sm-auto d-grid">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Lọc</button>
                </div>
                <div class="col-12 col-sm-auto d-grid">
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.reports.export.top_products', request()->query()) }}">
                        <i class="bi bi-download me-1"></i>Xuất Top SP (CSV)
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-6">
            <div class="kpi h-100">
                <div class="label">Doanh thu</div>
                <div class="value">{{ number_format($revenue ?? 0) }} đ</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi h-100">
                <div class="label">Đơn tạo</div>
                <div class="value">{{ (int)($ordersCount ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi h-100">
                <div class="label">Đơn đã thanh toán</div>
                <div class="value">{{ (int)($paidOrdersCount ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi h-100">
                <div class="label">Đơn hủy</div>
                <div class="value">{{ (int)($cancelledCount ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi h-100">
                <div class="label">AOV (đ)</div>
                <div class="value">{{ number_format($aov ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="kpi h-100">
                <div class="label">Tỉ lệ thanh toán</div>
                <div class="value">
                    {{ ($ordersCount ?? 0) ? round(($paidOrdersCount ?? 0)*100/($ordersCount ?? 1),1) : 0 }}%
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card card-soft h-100">
                <div class="card-header fw-semibold">Doanh thu theo ngày</div>
                <div class="card-body">
                    <div class="chart-h-280">
                        <canvas id="revenueLine" role="img" aria-label="Biểu đồ doanh thu theo ngày"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-soft h-100">
                <div class="card-header fw-semibold">Phương thức thanh toán</div>
                <div class="card-body">
                    <div class="chart-h-220">
                        <canvas id="paymentPie" role="img" aria-label="Phân bổ phương thức thanh toán"></canvas>
                    </div>
                    <ul class="mt-3 list-unstyled small">
                        @foreach(($paymentMethods ?? []) as $pm)
                            <li class="d-flex justify-content-between">
                                <span>{{ is_array($pm) ? ($pm['payment_method'] ?? 'Không rõ') : ($pm->payment_method ?? 'Không rõ') }}</span>
                                <span>
                                    {{ (int)(is_array($pm) ? ($pm['cnt'] ?? 0) : ($pm->cnt ?? 0)) }} đơn •
                                    {{ number_format(is_array($pm) ? ($pm['revenue'] ?? 0) : ($pm->revenue ?? 0)) }} đ
                                </span>
                            </li>
                        @endforeach
                        @if(empty($paymentMethods) || count($paymentMethods)===0)
                            <li class="text-muted">Chưa có dữ liệu</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card card-soft">
                <div class="card-header fw-semibold">Top sản phẩm</div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr><th>#</th><th>Sản phẩm</th><th class="text-end">SL</th><th class="text-end">Doanh thu</th></tr>
                        </thead>
                        <tbody>
                            @foreach(($topProducts ?? collect()) as $i => $p)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td class="text-truncate">{{ $p->name }}</td>
                                    <td class="text-end">{{ (int)$p->qty }}</td>
                                    <td class="text-end">{{ number_format($p->revenue) }} đ</td>
                                </tr>
                            @endforeach
                            @if(($topProducts ?? collect())->isEmpty())
                                <tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card card-soft">
                <div class="card-header fw-semibold">Top danh mục</div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr><th>#</th><th>Danh mục</th><th class="text-end">SL</th><th class="text-end">Doanh thu</th></tr>
                        </thead>
                        <tbody>
                            @foreach(($topCategories ?? collect()) as $i => $c)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td class="text-truncate">{{ $c->name }}</td>
                                    <td class="text-end">{{ (int)$c->qty }}</td>
                                    <td class="text-end">{{ number_format($c->revenue) }} đ</td>
                                </tr>
                            @endforeach
                            @if(($topCategories ?? collect())->isEmpty())
                                <tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card card-soft">
                <div class="card-header fw-semibold">Top brand</div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr><th>#</th><th>Brand</th><th class="text-end">SL</th><th class="text-end">Doanh thu</th></tr>
                        </thead>
                        <tbody>
                            @foreach(($topBrands ?? collect()) as $i => $b)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td class="text-truncate">{{ $b->name }}</td>
                                    <td class="text-end">{{ (int)$b->qty }}</td>
                                    <td class="text-end">{{ number_format($b->revenue) }} đ</td>
                                </tr>
                            @endforeach
                            @if(($topBrands ?? collect())->isEmpty())
                                <tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card card-soft">
                <div class="card-header fw-semibold">Tồn kho thấp</div>
                <div class="card-body table-responsive">
                    <table class="table table-sm align-middle">
                        <thead><tr><th>SP</th><th>Variant</th><th class="text-end">Tồn</th></tr></thead>
                        <tbody>
                            @forelse(($lowStocks ?? collect()) as $v)
                                <tr>
                                    <td class="text-truncate">{{ $v->product?->name }}</td>
                                    <td class="text-truncate">{{ $v->display_name }}</td>
                                    <td class="text-end">{{ $v->inventory?->stock_display ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">OK, chưa có biến thể nào thấp</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
  const css = getComputedStyle(document.documentElement);
  const BRAND  = (css.getPropertyValue('--brand')||'#C46F3B').trim();
  const B600   = (css.getPropertyValue('--brand-600')||'#B46234').trim();
  const B700   = (css.getPropertyValue('--brand-700')||'#7B3E22').trim();
  const ACCENT = (css.getPropertyValue('--accent')||'#4E6B52').trim();
  const SAND   = (css.getPropertyValue('--sand')||'#EADFCE').trim();
  const TEXT   = (css.getPropertyValue('--text')||'#2B2623').trim();

  Chart.defaults.font.family = 'Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial';
  Chart.defaults.color = TEXT;

  const money = v => new Intl.NumberFormat('vi-VN').format(v) + ' đ';

  // Revenue
  const revEl = document.getElementById('revenueLine');
  if (revEl){
    const ctx = revEl.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, revEl.parentElement.clientHeight || 280);
    grad.addColorStop(0, BRAND + '33');
    grad.addColorStop(1, BRAND + '00');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: @json($labels),
        datasets: [{
          label: 'Doanh thu (đ)',
          data: @json($series),
          borderColor: BRAND,
          backgroundColor: grad,
          tension: .35,
          fill: 'start',
          pointRadius: 0,
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: (ctx) => money(ctx.raw) } }
        },
        scales: {
          x: { grid: { display:false } },
          y: {
            ticks: { callback: (v)=> new Intl.NumberFormat('vi-VN').format(v) },
            grid: { color: SAND + '66' },
            beginAtZero: true
          }
        }
      }
    });
  }

  // Payment
  const payEl = document.getElementById('paymentPie');
  if (payEl){
    const colors = [BRAND, B600, B700, ACCENT, SAND, '#d0c7bd', '#bfae9f'];
    new Chart(payEl.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: @json($paymentLabels),
        datasets: [{
          data: @json($paymentData),
          backgroundColor: (c)=> colors[c.dataIndex % colors.length],
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: {
          legend: { position: 'bottom' },
          tooltip: { callbacks: { label: (ctx)=> `${ctx.label}: ${money(ctx.raw)}` } }
        }
      }
    });
  }
})();
</script>
@endpush
