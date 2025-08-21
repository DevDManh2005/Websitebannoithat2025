{{-- resources/views/admins/route_permissions/index.blade.php --}}
@extends('admins.layouts.app')
@section('title','Ánh xạ tuyến ↔ quyền')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $rows */
    $total = method_exists($rows,'total') ? $rows->total() : (is_countable($rows) ? count($rows) : 0);

    $areaLabel = static fn($v) => $v === 'admin' ? 'Quản trị' : 'Nhân viên';

    $actionLabels = [
        'view'=>'Xem','show'=>'Xem',
        'create'=>'Thêm','store'=>'Lưu',
        'edit'=>'Sửa','update'=>'Cập nhật',
        'delete'=>'Xóa','destroy'=>'Xóa',
        'moderate'=>'Duyệt/Ẩn','ready_to_ship'=>'Sẵn sàng giao','cod_paid'=>'Đã thu COD',
    ];
@endphp

<style>
  /* ====== Scope riêng cho trang này để tránh ảnh hưởng chéo ====== */
  #rp-page .rp-card{ border-radius:16px; border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06); background:var(--card) }
  #rp-page .rp-card .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }

  #rp-page .rp-filter{
    border-radius:16px; padding:12px;
    background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }

  /* Chip hiển thị route */
  #rp-page .code-chip{
    display:inline-flex; align-items:center; gap:6px; padding:6px 10px;
    background:#f7f7fb; border:1px dashed rgba(23,26,31,.15); border-radius:10px;
    font: 500 .9375rem ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", "Courier New", monospace;
  }
  #rp-page .code-chip .copy-btn{
    border:0; background:transparent; cursor:pointer; padding:0; line-height:1; color:#6c757d;
  }
  #rp-page .code-chip .copy-btn:hover{ color:#000 }

  /* Badge mềm theo brand: dùng cho cột Hành động để đảm bảo tương phản */
  #rp-page .badge-action{
    background: rgba(196,111,59,.16);
    color: #592d16; /* đậm hơn để dễ đọc trên nền sáng */
    border:1px solid rgba(196,111,59,.28);
    font-weight:600;
  }

  #rp-page .badge-soft-success{ background:#e5f7ed; color:#1e6b3a }
  #rp-page .badge-soft-secondary{ background:#f0f0f0; color:#555 }

  #rp-page .table td, #rp-page .table th{ vertical-align:middle }
  #rp-page .table-hover tbody tr:hover{ background:#fafbff }

  #rp-page .empty-state{ padding:48px 16px; text-align:center }
  #rp-page .empty-state .emoji{ font-size:40px; line-height:1 }
  #rp-page .tiny-muted{ font-size:.875rem; color:#6c757d }
</style>

<div id="rp-page" class="container-fluid">

  {{-- Thanh tiêu đề + hành động nhanh --}}
  <div class="rp-filter mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <h1 class="h5 mb-0 fw-bold">Ánh xạ tuyến ↔ quyền</h1>
        <span class="tiny-muted">(tổng {{ number_format($total) }})</span>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.route-permissions.create') }}" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Thêm ánh xạ
        </a>
        <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
          <i class="bi bi-arrow-clockwise me-1"></i> Tải lại
        </button>
      </div>
    </div>

    {{-- Bộ lọc --}}
    <form id="rpFilterForm" class="row g-2 mt-2" method="get" action="{{ route('admin.route-permissions.index') }}">
      <input type="hidden" name="page" value="1"><!-- luôn về trang 1 khi lọc -->
      <div class="col-12 col-lg-6">
        <div class="input-group">
          <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                 placeholder="Tìm theo tên route / module / hành động…">
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <select name="area" class="form-select">
          <option value="">Khu vực: Tất cả</option>
          <option value="admin" @selected(request('area')==='admin')>Quản trị</option>
          <option value="staff" @selected(request('area')==='staff')>Nhân viên</option>
        </select>
      </div>
      <div class="col-6 col-lg-2">
        <select name="status" class="form-select">
          <option value="">Trạng thái: Tất cả</option>
          <option value="1" @selected(request('status')==='1')>Đang bật</option>
          <option value="0" @selected(request('status')==='0')>Tắt</option>
        </select>
      </div>
      <div class="col-12 col-lg-2 d-grid">
        <button type="submit" class="btn btn-outline-secondary">
          <i class="bi bi-funnel me-1"></i> Lọc
        </button>
      </div>

      @if(request()->hasAny(['q','area','status']))
        <div class="col-12">
          <a href="{{ route('admin.route-permissions.index') }}" class="tiny-muted text-decoration-none">
            <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
          </a>
        </div>
      @endif
    </form>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
  @endif

  {{-- Danh sách --}}
  <div class="card rp-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Danh sách ánh xạ</strong>
      <span class="tiny-muted">Hiển thị theo thứ tự mới nhất</span>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
        <tr>
          <th style="width:80px">#</th>
          <th>Tuyến</th>
          <th class="d-none d-md-table-cell" style="width:120px">Khu vực</th>
          <th class="d-none d-lg-table-cell" style="width:220px">Module</th>
          <th style="width:180px">Hành động</th>
          <th style="width:120px">Trạng thái</th>
          <th class="text-end" style="width:160px">Thao tác</th>
        </tr>
        </thead>
        <tbody>
        @forelse($rows as $rp)
          @php
            $module = (string)($rp->module_name ?? '');
            $action = (string)($rp->action ?? '');
            $actText = $actionLabels[$action] ?? ($action ?: '-');
          @endphp
          <tr>
            <td class="text-muted">#{{ $rp->id }}</td>

            <td>
              <span class="code-chip" title="Tên tuyến (route)">
                <code class="me-1">{{ $rp->route_name }}</code>
                <button class="copy-btn js-copy" type="button" data-value="{{ $rp->route_name }}" data-bs-toggle="tooltip" data-bs-title="Sao chép">
                  <i class="bi bi-clipboard"></i>
                </button>
              </span>
            </td>

            <td class="d-none d-md-table-cell">
              <span class="badge {{ $rp->area === 'admin' ? 'text-bg-dark' : 'text-bg-secondary' }}">
                {{ $areaLabel($rp->area) }}
              </span>
            </td>

            <td class="d-none d-lg-table-cell">
              <span class="badge text-bg-light">{{ $module ?: '-' }}</span>
            </td>

            <td>
              <span class="badge badge-action">{{ $actText }}</span>
            </td>

            <td>
              @if($rp->is_active)
                <span class="badge badge-soft-success">Đang bật</span>
              @else
                <span class="badge badge-soft-secondary">Tắt</span>
              @endif
            </td>

            <td class="text-end">
              <div class="d-none d-md-inline-flex gap-1">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.route-permissions.edit', $rp) }}" data-bs-toggle="tooltip" data-bs-title="Sửa">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('admin.route-permissions.destroy', $rp) }}" method="post" class="d-inline"
                      onsubmit="return confirm('Xóa ánh xạ này?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-title="Xóa">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>

              <div class="dropdown d-inline d-md-none">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Thao tác</button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.route-permissions.edit', $rp) }}">
                      <i class="bi bi-pencil-square me-2"></i> Sửa
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form action="{{ route('admin.route-permissions.destroy', $rp) }}" method="post"
                          onsubmit="return confirm('Xóa ánh xạ này?');">
                      @csrf @method('DELETE')
                      <button class="dropdown-item text-danger">
                        <i class="bi bi-trash me-2"></i> Xóa
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7">
              <div class="empty-state">
                <div class="emoji mb-2">🗂️</div>
                <div class="fw-semibold mb-1">Chưa có ánh xạ nào.</div>
                <div class="tiny-muted">Nhấn “Thêm ánh xạ” để tạo mới.</div>
              </div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="tiny-muted">
        @if(method_exists($rows,'firstItem') && method_exists($rows,'lastItem'))
          Hiển thị {{ number_format($rows->firstItem() ?? 0) }}–{{ number_format($rows->lastItem() ?? 0) }} / {{ number_format($total) }}
        @else
          Tổng: {{ number_format($total) }}
        @endif
      </div>
      <div>
        {{ $rows->appends(request()->except('page'))->links() }}
      </div>
    </div>
  </div>
</div>

{{-- JS: Tooltip + Copy + Auto submit lọc --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Tooltip
  if (typeof bootstrap !== 'undefined') {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
  }

  // Copy route name
  document.querySelectorAll('.js-copy').forEach(btn => {
    btn.addEventListener('click', async () => {
      const txt = btn.getAttribute('data-value') || '';
      try {
        await navigator.clipboard.writeText(txt);
        const old = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-clipboard-check"></i>';
        setTimeout(() => btn.innerHTML = old, 1200);
      } catch (e) {
        const ta = document.createElement('textarea');
        ta.value = txt; document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta);
      }
    });
  });

  // Auto submit khi đổi select + trim ô tìm kiếm
  const form = document.getElementById('rpFilterForm');
  if (form) {
    form.querySelectorAll('select[name="area"], select[name="status"]').forEach(sel => {
      sel.addEventListener('change', () => form.requestSubmit());
    });
    const q = form.querySelector('input[name="q"]');
    if (q) {
      form.addEventListener('submit', () => { q.value = (q.value || '').trim(); });
    }
  }
});
</script>
@endsection
