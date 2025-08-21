@extends('admins.layouts.app')

@section('title', 'Quản lý nhà cung cấp')

@push('styles')
<style>
  /* Page polish + sticky header cho bảng */
  #suppliers-page .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #suppliers-page .search-input{ max-width: 360px }
  #suppliers-page .table-wrap{ border-radius:16px; overflow:hidden; }
  #suppliers-page .table thead th{
    position: sticky; top: 0; z-index: 1;
    background: var(--card);
    border-bottom: 1px dashed rgba(32,25,21,.12) !important;
  }
  #suppliers-page .addr{ max-width: 360px }
  @media (max-width: 1200px){ #suppliers-page .addr{ max-width: 260px } }
  @media (max-width: 992px) { #suppliers-page .addr{ max-width: 220px } }
</style>
@endpush

@section('content')
<div id="suppliers-page" class="container-fluid">

  {{-- Header + actions --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
      <h1 class="h5 fw-bold mb-0">Nhà cung cấp</h1>
      @isset($suppliers)
        <span class="text-muted small">Tổng: <strong>{{ number_format($suppliers->total() ?? $suppliers->count()) }}</strong></span>
      @endisset
    </div>

    <div class="d-flex align-items-center gap-2">
      <form action="{{ route('admin.suppliers.index') }}" method="GET" class="d-flex align-items-center gap-2">
        <div class="position-relative">
          <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted"></i>
          <input name="q"
                 value="{{ request('q') }}"
                 class="form-control form-control-sm ps-5 search-input"
                 placeholder="Tìm theo tên, email, điện thoại…">
        </div>
        @if(request('q'))
          <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-outline-secondary">Xoá lọc</a>
        @endif
        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search me-1"></i>Tìm</button>
      </form>

      <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Thêm mới
      </a>
    </div>
  </div>

  {{-- Flash --}}
  @includeIf('admins.shared.flash')

  {{-- Table --}}
  <div class="card">
    <div class="card-header py-2">
      <strong>Danh sách</strong>
    </div>

    @if(($suppliers->count() ?? 0) === 0)
      <div class="p-4 text-center text-muted">
        <div class="mb-2"><i class="bi bi-inboxes fs-2"></i></div>
        Chưa có nhà cung cấp nào.
        <div class="mt-2">
          <a class="btn btn-sm btn-primary" href="{{ route('admin.suppliers.create') }}">
            <i class="bi bi-plus-circle me-1"></i> Thêm nhà cung cấp đầu tiên
          </a>
        </div>
      </div>
    @else
      <div class="table-responsive table-wrap">
        <table class="table table-hover align-middle mb-0">
          <thead class="text-muted">
            <tr>
              <th class="text-nowrap">#</th>
              <th class="text-nowrap">Tên</th>
              <th class="text-nowrap">Liên hệ</th>
              <th class="text-nowrap">Điện thoại</th>
              <th class="text-nowrap">Email</th>
              <th class="text-nowrap">Địa chỉ</th>
              <th class="text-end text-nowrap">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @foreach($suppliers as $s)
              <tr>
                <td class="text-muted">#{{ $s->id }}</td>

                <td>
                  <a href="{{ route('admin.suppliers.show', $s->id) }}" class="text-decoration-none fw-semibold">
                    {{ $s->name }}
                  </a>
                </td>

                <td class="text-nowrap">
                  {{ $s->contact_name ?? '—' }}
                </td>

                <td class="text-nowrap">
                  @if($s->phone)
                    <a href="tel:{{ $s->phone }}" class="text-decoration-none">
                      <i class="bi bi-telephone me-1"></i>{{ $s->phone }}
                    </a>
                  @else
                    —
                  @endif
                </td>

                <td class="text-nowrap">
                  @if($s->email)
                    <a href="mailto:{{ $s->email }}" class="text-decoration-none">
                      <i class="bi bi-envelope me-1"></i>{{ $s->email }}
                    </a>
                  @else
                    —
                  @endif
                </td>

                <td>
                  <div class="addr text-truncate" title="{{ $s->address ?? '' }}">
                    {{ $s->address ?? '—' }}
                  </div>
                </td>

                <td class="text-end">
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.suppliers.show', $s->id) }}"
                       class="btn btn-sm btn-outline-secondary" title="Xem">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.suppliers.edit', $s->id) }}"
                       class="btn btn-sm btn-outline-primary" title="Sửa">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.suppliers.destroy', $s->id) }}"
                          method="POST" class="d-inline"
                          onsubmit="return confirm('Xóa nhà cung cấp này?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger" title="Xóa">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
          Hiển thị
          <strong>{{ $suppliers->firstItem() }}</strong>–<strong>{{ $suppliers->lastItem() }}</strong>
          / <strong>{{ $suppliers->total() }}</strong>
        </div>
        <div class="ms-auto">
          {{ $suppliers->withQueryString()->links() }}
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
