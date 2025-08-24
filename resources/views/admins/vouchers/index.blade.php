@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title', 'Quản lý Voucher')

@push('styles')
<style>
  /* ===== Vouchers page (scoped) ===== */
  #voucher-page .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #voucher-page .card-soft{
    border-radius:16px; border:1px solid rgba(32,25,21,.08);
    box-shadow:0 6px 22px rgba(18,38,63,.06); background:var(--card);
  }
  #voucher-page .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }

  #voucher-page .mono{ font:600 .95rem ui-monospace, Menlo, Consolas, "Courier New", monospace }
  #voucher-page .chip{
    display:inline-block; padding:.22rem .6rem; border-radius:999px;
    background:#f2f4f7; border:1px solid rgba(23,26,31,.12); font-weight:600; font-size:.82rem;
    color:var(--text,#2B2623); white-space:nowrap;
  }
  #voucher-page .chip.brand{
    background: color-mix(in srgb, var(--brand,#C46F3B) 14%, white);
    border-color: color-mix(in srgb, var(--brand,#C46F3B) 30%, #ddd);
  }
  #voucher-page .table thead th{ white-space:nowrap }
  #voucher-page .table td, #voucher-page .table th{ vertical-align:middle }
  #voucher-page .actions .btn{ padding:.3rem .55rem }
</style>
@endpush

@section('content')
<div id="voucher-page" class="container-fluid">

  {{-- Header / Actions --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Quản lý Voucher</h1>
      <span class="text-muted small">Theo dõi mã khuyến mãi & giới hạn sử dụng</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tạo mới
      </a>
    </div>
  </div>

  {{-- List --}}
  <div class="card card-soft">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:160px">Mã</th>
            <th style="width:130px">Loại</th>
            <th style="width:150px">Giá trị</th>
            <th style="width:220px">Sử dụng</th>
            <th style="width:140px">Trạng thái</th>
            <th class="text-end" style="width:160px">Hành động</th>
          </tr>
        </thead>
        <tbody>
        @forelse($vouchers as $voucher)
          @php
            $isFixed = ($voucher->type === 'fixed');
            $limit   = $voucher->usage_limit;
            $used    = (int) $voucher->used_count;
            $pct     = $limit ? max(0, min(100, (int) floor($used / max(1,$limit) * 100))) : null;
          @endphp
          <tr>
            <td>
              <span class="chip brand mono">{{ $voucher->code }}</span>
            </td>
            <td>
              @if($isFixed)
                <span class="chip">Cố định</span>
              @else
                <span class="chip">Phần trăm</span>
              @endif
            </td>
            <td class="fw-semibold">
              @if($isFixed)
                {{ number_format($voucher->value) }} ₫
              @else
                {{ rtrim(rtrim(number_format($voucher->value, 2, '.', ''), '0'), '.') }}%
              @endif
            </td>
            <td>
              @if($limit)
                <div class="mb-1 small text-muted">{{ $used }} / {{ $limit }}</div>
                <div class="progress" style="height:8px">
                  <div class="progress-bar" role="progressbar"
                       style="width: {{ $pct }}%; background:var(--brand,#C46F3B)"
                       aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              @else
                <span class="text-muted">Không giới hạn</span>
                <span class="ms-1 mono">({{ $used }})</span>
              @endif
            </td>
            <td>
              @if($voucher->is_active)
                <span class="badge text-bg-success">Hoạt động</span>
              @else
                <span class="badge text-bg-danger">Không hoạt động</span>
              @endif
            </td>
            <td class="text-end actions">
              <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil-square"></i>
              </a>
              <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Bạn có chắc muốn xóa voucher này?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Chưa có voucher nào.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-body">
      {{ $vouchers->onEachSide(1)->links() }}
    </div>
  </div>
</div>
@endsection
