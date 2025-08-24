@extends(auth()->user()->role->name === 'staff' ? 'staff.layouts.app' : 'admins.layouts.app')

@include('admins.partials.area_route')

@section('title', 'Quản lý Đơn hàng')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $orders */
    $total = method_exists($orders,'total') ? $orders->total() : $orders->count();
@endphp

<style>
  .filter-bar{
    border-radius:16px; padding:12px;
    background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08);
    box-shadow: var(--shadow);
  }
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
  .table td,.table th{ vertical-align: middle; }
  .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
  .badge.bg-secondary-soft{ background:#ececec; color:#545b62 }
  .badge.bg-warning-soft{ background:#fff4d6; color:#8b6b00 }
  .badge.bg-info-soft{ background:#e6f1ff; color:#0b4a8b }
  .badge.bg-primary-soft{ background:#e8ebff; color:#2a3cff }
  .badge.bg-danger-soft{ background:#fde7e7; color:#992f2f }
</style>

<div class="container-fluid">
  {{-- Header + status tabs --}}
  <div class="filter-bar mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <h1 class="h5 mb-0 fw-bold">Đơn hàng</h1>
        <span class="text-muted small">({{ number_format($total) }} đơn)</span>
      </div>
    </div>
    {{-- Bộ lọc --}}
<form class="row g-2 mt-2" method="get" action="{{ route('admin.orders.index') }}">
    {{-- Giữ nguyên status hiện tại khi lọc --}}
    <input type="hidden" name="status" value="{{ $currentStatus }}">
    
    <div class="col-12 col-lg-9">
        <div class="input-group">
            <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
            {{-- THÊM MỚI Ô TÌM KIẾM MÃ ĐƠN HÀNG Ở ĐÂY --}}
            <input type="text" name="code" class="form-control" value="{{ request('code') }}" placeholder="Tìm theo mã đơn hàng, tên khách hàng...">
        </div>
    </div>
    <div class="col-12 col-lg-3 d-grid">
        <button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i>Lọc</button>
    </div>
</form>
    {{-- Thanh điều hướng trạng thái --}}
    <ul class="nav nav-pills mt-2 flex-wrap">
      <li class="nav-item">
        <a class="nav-link {{ $currentStatus == 'all' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'all']) }}">Tất cả</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $currentStatus == 'pending' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'pending']) }}">Chờ xử lý</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $currentStatus == 'processing' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'processing']) }}">Đang xử lý</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $currentStatus == 'shipping' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'shipping']) }}">Đang giao</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $currentStatus == 'delivered' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'delivered']) }}">Đã giao</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $currentStatus == 'received' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'received']) }}">Đã nhận</a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $currentStatus == 'cancelled' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}">Đã hủy</a>
      </li>
    </ul>
  </div>

  @if(session('success')) <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div> @endif

  <div class="card card-soft">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Danh sách</strong>
      
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width:140px">Mã ĐH</th>
              <th>Khách hàng</th>
              <th style="width:170px">Ngày đặt</th>
              <th style="width:170px" class="text-end">Tổng tiền</th>
              <th style="width:140px">Trạng thái</th>
              <th style="width:110px" class="text-end">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($orders as $order)
              @php
                $statusConfig = [
                  'pending'   => ['class' => 'bg-warning-soft', 'text' => 'Chờ xử lý'],
                  'processing'=> ['class' => 'bg-info-soft',    'text' => 'Đang xử lý'],
                  'shipping'  => ['class' => 'bg-primary-soft', 'text' => 'Đang giao'],
                  'delivered' => ['class' => 'bg-success-soft', 'text' => 'Đã giao'],
                  'received'  => ['class' => 'bg-success',      'text' => 'Đã nhận'],
                  'cancelled' => ['class' => 'bg-danger-soft',  'text' => 'Đã hủy'],
                ];
                $st = $statusConfig[$order->status] ?? ['class'=>'bg-secondary-soft','text'=>'Không xác định'];
              @endphp
              <tr>
                <td>
                  <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none fw-semibold">
                    #{{ $order->order_code }}
                  </a>
                </td>
                <td class="text-truncate">{{ $order->user->name ?? 'N/A' }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-end">{{ number_format($order->final_amount) }} ₫</td>
                <td><span class="badge {{ $st['class'] }}">{{ $st['text'] }}</span></td>
                <td class="text-end">
                  <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-eye"></i> Xem
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted p-4">Không có đơn hàng nào.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        {{ $orders->appends(request()->query())->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
