@extends('staffs.layouts.app')

@section('breadcrumb')
  <div class="d-flex align-items-center gap-2">
    <i class="ri-dashboard-3-line text-primary"></i>
    <span class="fw-semibold">Bảng điều khiển</span>
  </div>
@endsection

@section('content')
  <div class="container-fluid px-0">

    <div class="mb-4">
      <h4 class="mb-1">Xin chào, {{ $user->name ?? auth()->user()->name ?? 'Staff' }}</h4>
      <div class="text-secondary">Chúc bạn một ngày làm việc hiệu quả ✨</div>
    </div>

    {{-- THỐNG KÊ NHANH --}}
    @if(isset($stats['orders']))
      @php $o = $stats['orders']; @endphp
      <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xxl-3">
          <div class="card clean">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Đơn hôm nay</span>
                <i class="ri-calendar-check-line text-primary"></i>
              </div>
              <div class="display-6 fw-bold">{{ number_format($o['today_count']) }}</div>
              <div class="text-secondary small">Số đơn tạo trong ngày</div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-xxl-3">
          <div class="card clean">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Chờ xử lý</span>
                <i class="ri-time-line text-warning"></i>
              </div>
              <div class="display-6 fw-bold">{{ number_format($o['pending_count']) }}</div>
              <div class="text-secondary small">Đơn đang chờ xác nhận</div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-xxl-3">
          <div class="card clean">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Đang xử lý / đóng gói</span>
                <i class="ri-settings-4-line text-info"></i>
              </div>
              <div class="display-6 fw-bold">{{ number_format($o['processing_count']) }}</div>
              <div class="text-secondary small">Đơn đã xác nhận / chuẩn bị giao</div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-6 col-xxl-3">
          <div class="card clean">
            <div class="card-body">
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-semibold">Doanh thu giao hôm nay</span>
                <i class="ri-truck-line text-success"></i>
              </div>
              <div class="display-6 fw-bold">{{ number_format($o['delivered_today']) }}₫</div>
              <div class="text-secondary small">Đơn trạng thái delivered hôm nay</div>
            </div>
          </div>
        </div>
      </div>
    @endif

    <div class="row g-3">
      {{-- ĐƠN MỚI NHẤT --}}
      <div class="col-12 col-xxl-7">
        <div class="card clean h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="fw-semibold">Đơn hàng mới nhất</div>
              @if(($can['orders']['view'] ?? false) && Route::has('staff.orders.index'))
                <a href="{{ route('staff.orders.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
              @endif
            </div>

            @if(!empty($latestOrders) && count($latestOrders))
              <div class="table-responsive">
                <table class="table align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Mã đơn</th>
                      <th>Trạng thái</th>
                      <th class="text-end">Tổng tiền</th>
                      <th>Ngày tạo</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($latestOrders as $od)
                      @php
                        $status = $od->status;
                        $badge = match($status){
                          'pending' => 'text-bg-warning',
                          'confirmed','processing','ready_to_ship' => 'text-bg-info',
                          'shipping' => 'text-bg-primary',
                          'delivered','received' => 'text-bg-success',
                          'cancelled','failed' => 'text-bg-danger',
                          default => 'text-bg-secondary'
                        };
                      @endphp
                      <tr>
                        <td>{{ $od->id }}</td>
                        <td class="fw-semibold">{{ $od->order_code ?? '—' }}</td>
                        <td><span class="badge {{ $badge }}">{{ $status }}</span></td>
                        <td class="text-end">{{ number_format($od->total_amount) }}₫</td>
                        <td>{{ \Carbon\Carbon::parse($od->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                          @if(($can['orders']['view'] ?? false) && Route::has('staff.orders.show'))
                            <a class="btn btn-sm btn-light border" href="{{ route('staff.orders.show', $od->id) }}">
                              <i class="ri-eye-line"></i>
                            </a>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="text-center text-secondary py-4">
                <i class="ri-inbox-archive-line fs-2 d-block mb-2"></i>
                Chưa có đơn hàng nào.
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- QUYỀN HIỆN CÓ --}}
      <div class="col-12 col-xxl-5">
        <div class="card clean h-100">
          <div class="card-body">
            <div class="fw-semibold mb-2">Quyền hiện có</div>
            @php
              $perms = collect(auth()->user()?->permissions ?? [])
                        ->merge(collect(auth()->user()?->role?->permissions ?? []))
                        ->unique('id')->values();
            @endphp
            @if($perms->count())
              @foreach($perms as $p)
                <span class="chip">{{ $p->module_name.'.'.$p->action }}</span>
              @endforeach
            @else
              <div class="text-secondary">Tài khoản chưa được cấp quyền riêng.</div>
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
@endsection
