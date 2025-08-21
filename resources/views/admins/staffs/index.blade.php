@extends('admins.layouts.app')
@section('title','Nhân viên')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $staffs */
    $total = method_exists($staffs,'total') ? $staffs->total() : (is_countable($staffs) ? count($staffs) : 0);
@endphp

<style>
  :root{
    --soft-border: 1px solid rgba(32,25,21,.08);
    --soft-shadow: 0 6px 22px rgba(18,38,63,.06);
  }
  /* Thanh tiêu đề + hành động */
  .filter-bar{
    border-radius:16px; padding:12px;
    background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:var(--soft-border); box-shadow:var(--soft-shadow);
  }
  .card-soft{ border-radius:16px; border:var(--soft-border); box-shadow:var(--soft-shadow); }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
  .table td, .table th{ vertical-align: middle; }
  .table-hover tbody tr:hover{ background:#fafbff; }

  /* Chip quyền */
  .perm-chip{
    display:inline-flex; align-items:center; gap:6px;
    padding:4px 8px; margin:2px 6px 2px 0;
    background:#f7f7fb; border:1px dashed rgba(23,26,31,.15); border-radius:999px;
    font: 500 .85rem ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", "Courier New", monospace;
    white-space:nowrap;
  }
  .perm-chip .bi{ opacity:.7; }

  /* Fix các CSS ngoài “đắp” icon prev/next quá khổ */
  .pagination::before, .pagination::after,
  .pagination *::before, .pagination *::after{ content:none!important; display:none!important; }
</style>

<div class="container-fluid">

  {{-- Header + actions --}}
  <div class="filter-bar mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <h1 class="h5 fw-bold mb-0">Nhân viên</h1>
        <span class="text-muted small">(tổng {{ number_format($total) }})</span>
      </div>
      <a href="{{ route('admin.staffs.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Thêm nhân viên
      </a>
    </div>

    {{-- Nếu sau này bạn muốn lọc theo tên/email, chỉ cần mở form này
         và thêm when($request->filled('q')) vào controller --}}
    {{-- <form class="row g-2 mt-2" method="get" action="{{ route('admin.staffs.index') }}">
      <div class="col-12 col-lg-6">
        <div class="input-group">
          <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                 placeholder="Tìm họ tên hoặc email…">
        </div>
      </div>
      <div class="col-12 col-lg-2 d-grid">
        <button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i> Lọc</button>
      </div>
      @if(request()->hasAny(['q']))
        <div class="col-12">
          <a href="{{ route('admin.staffs.index') }}" class="small text-muted text-decoration-none">
            <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
          </a>
        </div>
      @endif
    </form> --}}
  </div>

  @includeIf('admins.shared.flash')

  <div class="card card-soft">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Danh sách nhân viên</strong>
      @if(method_exists($staffs,'firstItem') && method_exists($staffs,'lastItem'))
        <span class="text-muted small">
          Hiển thị {{ number_format($staffs->firstItem() ?? 0) }}–{{ number_format($staffs->lastItem() ?? 0) }} / {{ number_format($total) }}
        </span>
      @else
        <span class="text-muted small">Tổng: {{ number_format($total) }}</span>
      @endif
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:80px">#</th>
            <th style="width:26%">Họ tên / Email</th>
            <th style="width:14%">Vai trò</th>
            <th>Quyền trực tiếp</th>
            <th class="text-end" style="width:160px">Thao tác</th>
          </tr>
        </thead>
        <tbody>
        @forelse($staffs as $u)
          @php
            $allChips = $u->directPermissions->map(fn($p) => $p->module_name.'.'.$p->action);
            $maxShow = 8;
            $visible = $allChips->take($maxShow);
            $hidden  = max(0, $allChips->count() - $maxShow);
          @endphp
          <tr>
            <td class="text-muted">#{{ $u->id }}</td>

            <td>
              <div class="fw-semibold">{{ $u->name }}</div>
              <div class="small text-muted">{{ $u->email }}</div>
            </td>

            <td>
              <span class="badge {{ optional($u->role)->name === 'admin' ? 'text-bg-dark' : 'text-bg-secondary' }}">
                {{ optional($u->role)->name ?? '—' }}
              </span>
            </td>

            <td style="max-width:520px">
              @if($visible->isEmpty())
                <span class="text-muted small">—</span>
              @else
                <div class="d-flex flex-wrap align-items-center">
                  @foreach($visible as $chip)
                    <span class="perm-chip"><i class="bi bi-key"></i> {{ $chip }}</span>
                  @endforeach
                  @if($hidden > 0)
                    <span class="perm-chip" title="{{ $allChips->implode(', ') }}">+{{ $hidden }} nữa</span>
                  @endif
                </div>
              @endif
            </td>

            <td class="text-end">
              {{-- Desktop actions --}}
              <div class="d-none d-md-inline-flex gap-1">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.staffs.edit', $u->id) }}" title="Sửa">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form class="d-inline" action="{{ route('admin.staffs.destroy', $u->id) }}" method="POST"
                      onsubmit="return confirm('Xóa nhân viên này?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="Xóa">
                    <i class="bi bi-trash3"></i>
                  </button>
                </form>
              </div>

              {{-- Mobile actions --}}
              <div class="dropdown d-inline d-md-none">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Thao tác</button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.staffs.edit', $u->id) }}">
                      <i class="bi bi-pencil-square me-2"></i> Sửa
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form action="{{ route('admin.staffs.destroy', $u->id) }}" method="POST"
                          onsubmit="return confirm('Xóa nhân viên này?')">
                      @csrf @method('DELETE')
                      <button class="dropdown-item text-danger">
                        <i class="bi bi-trash3 me-2"></i> Xóa
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">Chưa có nhân viên.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="text-muted small">
        @if(method_exists($staffs,'firstItem') && method_exists($staffs,'lastItem'))
          Hiển thị {{ number_format($staffs->firstItem() ?? 0) }}–{{ number_format($staffs->lastItem() ?? 0) }} / {{ number_format($total) }}
        @else
          Tổng: {{ number_format($total) }}
        @endif
      </div>
      <div>
        @if(method_exists($staffs,'appends'))
          {{ $staffs->appends(request()->query())->links() }}
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
