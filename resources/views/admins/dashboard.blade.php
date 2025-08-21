@extends('admins.layouts.app')

@section('title', 'Tổng quan')

@section('content')
@php
    $k = $kpis ?? [
        'revenue'=>0,'orders'=>0,'paid_orders'=>0,'customers'=>0,'products'=>0,'growth'=>0,'aov'=>0
    ];
    $recentOrders = $recentOrders ?? collect();
    $range = $range ?? '30d';
@endphp

<style>
    /* ====== Cards / tiles ====== */
    .tile{
        position:relative; overflow:hidden; border-radius:16px;
        background: linear-gradient(140deg, rgba(196,111,59,.10), rgba(78,107,82,.08) 70%), var(--card);
        box-shadow: var(--shadow);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        border:1px solid rgba(32,25,21,.06);
    }
    .tile:hover{ transform: translateY(-2px); box-shadow: 0 16px 40px rgba(0,0,0,.12); }
    .tile .icon-wrap{
        width:46px; height:46px; border-radius:12px; display:grid; place-items:center;
        background:linear-gradient(135deg, rgba(196,111,59,.22), rgba(196,111,59,.08)); color:#8e4f29;
    }
    [data-theme="dark"] .tile .icon-wrap{ background:linear-gradient(135deg, rgba(196,111,59,.25), rgba(196,111,59,.10)); color:#e2c5b3; }

    .quick-link{
        display:flex; align-items:center; gap:.6rem; padding:.75rem 1rem; color:inherit; text-decoration:none;
        border-radius:12px; transition: .18s ease background, .18s ease transform;
    }
    .quick-link:hover{ background:rgba(196,111,59,.10); transform: translateX(2px); }

    .status-badge{ border-radius:999px; padding:.25rem .6rem; font-weight:600; font-size:.75rem }

    .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08); background: var(--card); }
    .card-soft .table>tbody>tr:hover{ background: rgba(196,111,59,.06) }

    .range-btn { border-radius:999px; }

    .progress.funnel { height:10px; background:#efe7dc }
    [data-theme="dark"] .progress.funnel { background:#1d2329 }

    /* Table tweaks for better mobile read */
    .table td, .table th{ vertical-align: middle }
    .table .text-truncate{ max-width:180px }

    @media (max-width: 575.98px){
        .range-btn{ padding:.25rem .5rem }
        .table .text-truncate{ max-width:120px }
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0 fw-bold">Bảng điều khiển</h1>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <div class="btn-group" role="group" aria-label="range">
            <a href="{{ route('admin.dashboard', ['range'=>'7d']) }}"  class="btn btn-sm {{ $range==='7d' ? 'btn-primary' : 'btn-outline-secondary' }} range-btn">7 ngày</a>
            <a href="{{ route('admin.dashboard', ['range'=>'30d']) }}" class="btn btn-sm {{ $range==='30d' ? 'btn-primary' : 'btn-outline-secondary' }} range-btn">30 ngày</a>
            <a href="{{ route('admin.dashboard', ['range'=>'90d']) }}" class="btn btn-sm {{ $range==='90d' ? 'btn-primary' : 'btn-outline-secondary' }} range-btn">90 ngày</a>
        </div>
        <a href="{{ route('admin.reports.dashboard', ['from'=>$from?->toDateString(),'to'=>$to?->toDateString()]) }}" class="btn btn-primary ripple">
            <i class="bi bi-graph-up-arrow me-1"></i> Báo cáo
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary ripple">
            <i class="bi bi-receipt me-1"></i> Đơn hàng
        </a>
    </div>
</div>

{{-- KPI Tiles --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="tile p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted">Tổng doanh thu</small>
                    <div class="h4 mb-0">{{ number_format($k['revenue']) }}₫</div>
                    <small class="{{ ($k['growth'] ?? 0) >= 0 ? 'text-success':'text-danger' }}">
                        <i class="bi {{ ($k['growth'] ?? 0) >= 0 ? 'bi-arrow-up-right':'bi-arrow-down-right' }}"></i>
                        {{ number_format(abs($k['growth'] ?? 0),1) }}% MoM
                    </small>
                </div>
                <div class="icon-wrap"><i class="bi bi-cash-stack fs-5"></i></div>
            </div>
            <div class="mt-2">
                <canvas id="sparkRevenue" height="50" aria-label="Biểu đồ doanh thu mini" role="img"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="tile p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted">Đơn hàng</small>
                    <div class="h4 mb-0">{{ number_format($k['orders']) }}</div>
                    <small class="text-muted">Đã thanh toán: {{ number_format($k['paid_orders']) }}</small>
                </div>
                <div class="icon-wrap"><i class="bi bi-bag-check fs-5"></i></div>
            </div>
            <div class="mt-2 small text-muted">AOV: <strong>{{ number_format($k['aov']) }}₫</strong></div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="tile p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted">Khách hàng</small>
                    <div class="h4 mb-0">{{ number_format($k['customers']) }}</div>
                    <small class="text-muted">Tích cực kỳ này</small>
                </div>
                <div class="icon-wrap"><i class="bi bi-people fs-5"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="tile p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted">Sản phẩm</small>
                    <div class="h4 mb-0">{{ number_format($k['products']) }}</div>
                    <small class="text-muted">Đang kinh doanh</small>
                </div>
                <div class="icon-wrap"><i class="bi bi-box-seam fs-5"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Recent Orders --}}
    <div class="col-xxl-8">
        <div class="card card-soft h-100">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <strong>Đơn hàng gần đây</strong>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary ripple">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th class="text-end">Số tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $o)
                            <tr>
                                <td class="fw-600">#{{ $o->order_code ?? $o->id }}</td>
                                <td class="text-truncate">{{ $o->user->name ?? 'N/A' }}</td>
                                <td class="text-end">{{ number_format($o->final_amount ?? $o->total_amount ?? 0) }}₫</td>
                                <td>
                                    @php
                                        $cls = \App\Models\Order::getStatusClass($o->status);
                                        $map = [
                                            'bg-warning text-dark' => 'badge bg-warning-soft',
                                            'bg-info text-dark'    => 'badge bg-info-soft',
                                            'bg-success'           => 'badge bg-success-soft',
                                            'bg-danger'            => 'badge bg-danger-soft',
                                            'bg-primary'           => 'badge bg-primary-soft',
                                            'bg-secondary'         => 'badge bg-secondary',
                                        ];
                                        $badge = $map[$cls] ?? 'badge bg-secondary';
                                    @endphp
                                    <span class="status-badge {{ $badge }}">{{ \App\Models\Order::getStatusText($o->status) }}</span>
                                </td>
                                <td>{{ optional($o->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">
                                    <i class="bi bi-inbox me-1"></i> Chưa có dữ liệu đơn gần đây.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Quick actions + Payment + Funnel + Low stock --}}
    <div class="col-xxl-4">
        <div class="card card-soft mb-3">
            <div class="card-header bg-transparent"><strong>Hành động nhanh</strong></div>
            <div class="p-2">
                <a href="{{ route('admin.users.index') }}" class="quick-link"><i class="bi bi-person-plus fs-5 text-primary"></i> Quản lý người dùng</a>
                <a href="{{ route('admin.products.index') }}" class="quick-link"><i class="bi bi-box fs-5 text-success"></i> Thêm sản phẩm mới</a>
                <a href="{{ route('admin.orders.index') }}" class="quick-link"><i class="bi bi-hourglass-split fs-5 text-warning"></i> Đơn chờ xử lý</a>
                <a href="{{ route('admin.reports.dashboard', ['from'=>$from?->toDateString(),'to'=>$to?->toDateString()]) }}" class="quick-link"><i class="bi bi-bar-chart fs-5 text-info"></i> Xem báo cáo</a>
                <a href="{{ route('admin.settings.index') }}" class="quick-link"><i class="bi bi-gear fs-5 text-secondary"></i> Cài đặt hệ thống</a>
            </div>
        </div>

        <div class="card card-soft mb-3">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <strong>Phương thức thanh toán</strong>
            </div>
            <div class="card-body">
                <canvas id="paymentDonut" height="170" aria-label="Biểu đồ phương thức thanh toán" role="img"></canvas>
                @if(!empty($paymentLabels) && count($paymentLabels))
                    <ul class="mt-3 list-unstyled small">
                        @foreach($paymentLabels as $i => $label)
                            <li class="d-flex justify-content-between">
                                <span>{{ $label }}</span>
                                <span>{{ number_format($paymentData[$i] ?? 0) }} đ</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-muted small">Chưa có dữ liệu thanh toán.</div>
                @endif
            </div>
        </div>

        <div class="card card-soft mb-3">
            <div class="card-header bg-transparent"><strong>Funnel trạng thái</strong></div>
            <div class="card-body">
                @forelse(($funnel ?? []) as $row)
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $row['text'] }}</span>
                        <span>{{ $row['count'] }} • {{ $row['percent'] }}%</span>
                    </div>
                    <div class="progress funnel mb-2">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $row['percent'] }}%; background: linear-gradient(90deg, #c46f3b, #4e6b52)"
                             aria-valuenow="{{ $row['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                @empty
                    <div class="text-muted small">Chưa có dữ liệu.</div>
                @endforelse
            </div>
        </div>

        <div class="card card-soft">
            <div class="card-header bg-transparent"><strong>Sản phẩm sắp hết kho</strong></div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light"><tr><th>SP</th><th>Biến thể</th><th class="text-end">Tồn</th></tr></thead>
                    <tbody>
                        @forelse(($lowStocks ?? collect()) as $v)
                            <tr>
                                <td class="text-truncate">{{ $v->product?->name }}</td>
                                <td class="text-truncate">{{ $v->display_name }}</td>
                                <td class="text-end">{{ $v->inventory?->quantity ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Kho đang ổn.</td></tr>
                        @endforelse
                    </tbody>
                </table>
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
  const BRAND   = css.getPropertyValue('--brand').trim() || '#C46F3B';
  const BRAND6  = css.getPropertyValue('--brand-600').trim() || '#B46234';
  const BRAND7  = css.getPropertyValue('--brand-700').trim() || '#7B3E22';
  const ACCENT  = css.getPropertyValue('--accent').trim() || '#4E6B52';
  const SAND    = css.getPropertyValue('--sand').trim() || '#EADFCE';
  const TEXT    = css.getPropertyValue('--text').trim() || '#2B2623';

  // Global Chart defaults
  Chart.defaults.font.family = 'Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial';
  Chart.defaults.color = TEXT;
  Chart.defaults.plugins.tooltip.callbacks.label = (ctx) => {
    const nf = new Intl.NumberFormat('vi-VN');
    const v  = ctx.raw;
    return (typeof v === 'number') ? `${nf.format(v)} đ` : v;
  };

  // Sparkline (Revenue)
  const sparkEl = document.getElementById('sparkRevenue');
  if (sparkEl){
    const ctx = sparkEl.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, sparkEl.height);
    grad.addColorStop(0, BRAND + '33');   // ~0.2 alpha
    grad.addColorStop(1, BRAND + '00');   // 0 alpha

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: @json($sparkLabels ?? []),
        datasets: [{
          data: @json($sparkData ?? []),
          tension: .35,
          fill: true,
          borderColor: BRAND,
          backgroundColor: grad,
          borderWidth: 2,
          pointRadius: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins:{legend:{display:false}},
        scales:{x:{display:false}, y:{display:false}},
        elements:{line:{borderJoinStyle:'round'}},
      }
    });
  }

  // Payment donut
  const donutEl = document.getElementById('paymentDonut');
  if (donutEl){
    const colors = [BRAND, BRAND6, BRAND7, ACCENT, SAND, '#d0c7bd', '#bfae9f'];
    new Chart(donutEl.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: @json($paymentLabels ?? []),
        datasets: [{
          data: @json($paymentData ?? []),
          backgroundColor: (ctx) => colors[ctx.dataIndex % colors.length],
          borderColor: '#fff',
          borderWidth: 2
        }]
      },
      options: {
        plugins:{legend:{position:'bottom'}},
        cutout: '62%',
        animation:{ animateRotate:true, animateScale:true }
      }
    });
  }
})();
</script>
@endpush
