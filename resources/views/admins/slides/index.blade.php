{{-- resources/views/admins/slides/index.blade.php --}}
@extends('admins.layouts.app')
@section('title','Quản lý Slide')

@section('content')
@php
  /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|array $slides */

  // Tổng số item an toàn cho cả paginator/collection/array
  $total = method_exists($slides,'total')
      ? (int)$slides->total()
      : (is_countable($slides) ? count($slides) : 0);

  // Lấy collection để foreach/pluck
  $items = $slides instanceof \Illuminate\Contracts\Pagination\Paginator
      ? $slides->getCollection()
      : collect($slides ?? []);
@endphp

{{-- Flash chung --}}
@includeWhen(view()->exists('admins.shared.flash'),'admins.shared.flash')

<style>
  /* ===== Scope riêng để không ảnh hưởng trang khác ===== */
  #slides-page .filter-bar{
    border-radius:16px; padding:12px;
    background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08);
    box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #slides-page .card-soft{
    border-radius:16px; border:1px solid rgba(32,25,21,.08);
    box-shadow:0 6px 22px rgba(18,38,63,.06);
    background: var(--card);
  }
  #slides-page .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }

  /* Ảnh thumbnail 16:9, bo góc */
  #slides-page .thumb{
    width: 160px; aspect-ratio: 16/9;
    border-radius:10px; overflow:hidden; background:#f6f6f6;
    display:grid; place-items:center; border:1px solid rgba(0,0,0,.06);
  }
  #slides-page .thumb img{ width:100%; height:100%; object-fit:cover }

  /* Bảng */
  #slides-page .table td, #slides-page .table th{ vertical-align: middle; }
  #slides-page .table-hover tbody tr:hover{ background:#fafbff; }

  /* Badge mềm */
  #slides-page .badge-soft-success{ background:#e5f7ed; color:#1e6b3a }
  #slides-page .badge-soft-danger { background:#fde7e7; color:#992f2f }

  /* Sửa các mũi tên phân trang lạ nếu có plugin “đắp” ::before/::after */
  #slides-page .pagination::before, #slides-page .pagination::after,
  #slides-page .pagination *::before, #slides-page .pagination *::after{
    content:none !important; background:none !important; border:0 !important; box-shadow:none !important;
  }
</style>

<div id="slides-page" class="container-fluid">
  {{-- Header + nút tạo --}}
  <div class="filter-bar mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <h1 class="h5 mb-0 fw-bold">Quản lý Slide</h1>
        <span class="text-muted small">(tổng {{ number_format($total) }})</span>
      </div>
      <a href="{{ route('admin.slides.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tạo mới
      </a>
    </div>

    {{-- Lọc nhanh --}}
    <form class="row g-2 mt-2" method="get" action="{{ route('admin.slides.index') }}">
      <div class="col-12 col-lg-6">
        <div class="input-group">
          <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                 placeholder="Tìm theo tiêu đề…">
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <select name="status" class="form-select">
          <option value="">-- Trạng thái --</option>
          <option value="1" @selected(request('status')==='1')>Hoạt động</option>
          <option value="0" @selected(request('status')==='0')>Không hoạt động</option>
        </select>
      </div>
      <div class="col-6 col-lg-2 d-grid">
        <button class="btn btn-outline-secondary">
          <i class="bi bi-funnel me-1"></i> Lọc
        </button>
      </div>

      @if(request()->hasAny(['q','status']))
        <div class="col-12">
          <a class="small text-decoration-none text-muted" href="{{ route('admin.slides.index') }}">
            <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
          </a>
        </div>
      @endif
    </form>
  </div>

  {{-- Danh sách --}}
  <div class="card card-soft">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Danh sách Slide</strong>
      <span class="text-muted small">
        @if(method_exists($slides,'firstItem') && method_exists($slides,'lastItem'))
          Hiển thị {{ number_format($slides->firstItem() ?? 0) }}–{{ number_format($slides->lastItem() ?? 0) }} / {{ number_format($total) }}
        @else
          Tổng: {{ number_format($total) }}
        @endif
      </span>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
          <thead class="table-light">
            <tr>
              <th style="width:180px">Ảnh</th>
              <th>Tiêu đề</th>
              <th style="width:120px">Vị trí</th>
              <th style="width:160px">Trạng thái</th>
              <th class="text-end" style="width:180px">Thao tác</th>
            </tr>
          </thead>
          <tbody>
          @forelse($items as $slide)
            <tr>
              <td>
                <div class="thumb">
                  @if($slide->image)
                    <img src="{{ asset('storage/' . $slide->image) }}" alt="{{ $slide->title }}">
                  @else
                    <span class="text-muted small">Không có ảnh</span>
                  @endif
                </div>
              </td>
              <td class="fw-medium">
                {{ $slide->title ?: '—' }}
                @if(!empty($slide->subtitle))
                  <div class="small text-muted">{{ $slide->subtitle }}</div>
                @endif
                @if(!empty($slide->link))
                  <div class="small">
                    <a href="{{ $slide->link }}" target="_blank" rel="noopener" class="text-decoration-none">
                      <i class="bi bi-box-arrow-up-right"></i> Xem liên kết
                    </a>
                  </div>
                @endif
              </td>
              <td>{{ $slide->position }}</td>
              <td>
                @if($slide->is_active)
                  <span class="badge badge-soft-success">Hoạt động</span>
                @else
                  <span class="badge badge-soft-danger">Không hoạt động</span>
                @endif
              </td>
              <td class="text-end">
                <a href="{{ route('admin.slides.edit', $slide) }}" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-pencil-square"></i> Sửa
                </a>
                <form action="{{ route('admin.slides.destroy', $slide) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Bạn có chắc muốn xóa slide này?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash"></i> Xóa
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">Chưa có slide nào.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
          @if(method_exists($slides,'firstItem') && method_exists($slides,'lastItem'))
            Hiển thị {{ number_format($slides->firstItem() ?? 0) }}–{{ number_format($slides->lastItem() ?? 0) }} / {{ number_format($total) }}
          @else
            Tổng: {{ number_format($total) }}
          @endif
        </div>
        <div>
          @if(method_exists($slides,'links'))
            {{ $slides->appends(request()->query())->links() }}
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
