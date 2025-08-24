@extends(auth()->user()->role->name === 'staff' ? 'staff.layouts.app' : 'admins.layouts.app')



@section('title', 'Chi tiết Kho hàng')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
  .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
  .badge.bg-danger-soft{ background:#fde7e7; color:#992f2f }
</style>

<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.inventories.index') }}">Kho hàng</a></li>
      <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
    </ol>
  </nav>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card card-soft">
        <div class="card-header"><h5 class="card-title mb-0">Chi tiết Kho hàng #{{ $inventory->id }}</h5></div>
        <div class="card-body">
          <dl class="row">
            <dt class="col-sm-4">Sản phẩm:</dt>
            <dd class="col-sm-8">{{ $inventory->product?->name ?? 'N/A' }}</dd>

            <dt class="col-sm-4">Biến thể:</dt>
            <dd class="col-sm-8">
              @if($inventory->variant)
                <strong>{{ $inventory->variant->sku }}</strong>
                @if(!empty($inventory->variant->attributes))
                  @php $parts=[]; foreach($inventory->variant->attributes as $k=>$v){ $parts[] = ucfirst($k).': '.$v; } @endphp
                  <br><small class="text-muted">({{ implode(', ', $parts) }})</small>
                @endif
              @else
                <span class="text-muted">Không áp dụng</span>
              @endif
            </dd>

            <dt class="col-sm-4">Số lượng hiện tại:</dt>
            <dd class="col-sm-8">
              @php $q = (int) $inventory->quantity; @endphp
              <h4>
                <span class="badge {{ $q>0 ? 'bg-success-soft' : 'bg-danger-soft' }}">{{ $q }}</span>
              </h4>
            </dd>
          </dl>
        </div>
      </div>

      <div class="card card-soft mt-3">
        <div class="card-header"><h5 class="card-title mb-0">Vị trí Lưu trữ</h5></div>
        <div class="card-body">
          <dl class="row">
            <dt class="col-sm-4">Địa chỉ kho:</dt>
            <dd class="col-sm-8">{{ optional($inventory->location)->address ?? 'Chưa cập nhật' }}</dd>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card card-soft">
        <div class="card-header"><h5 class="card-title mb-0">Lịch sử Giao dịch</h5></div>
        <div class="card-body">
          <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
            <table class="table table-sm table-borderless mb-0">
              <tbody>
                @forelse($inventory->transactions->sortByDesc('created_at') as $t)
                  <tr>
                    <td>
                      @if($t->type === 'import')
                        <span class="badge bg-success">Nhập</span>
                      @elseif($t->type === 'export')
                        <span class="badge bg-danger">Xuất</span>
                      @else
                        <span class="badge bg-warning text-dark">Điều chỉnh</span>
                      @endif
                    </td>
                    <td>
                      @if($t->type === 'import')
                        <strong class="text-success">+{{ $t->quantity }}</strong>
                      @else
                        <strong class="text-danger">-{{ $t->quantity }}</strong>
                      @endif
                    </td>
                    <td class="text-muted text-end">
                      {{ $t->user?->name ?? 'Hệ thống' }}<br>
                      <small>{{ $t->created_at->format('d/m/y H:i') }}</small>
                    </td>
                  </tr>
                @empty
                  <tr><td class="text-center text-muted">Chưa có giao dịch nào.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
          <a href="{{ route('admin.inventories.edit', $inventory->id) }}" class="btn btn-warning">Chỉnh sửa</a>
          <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
