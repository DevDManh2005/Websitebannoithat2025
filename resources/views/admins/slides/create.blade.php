@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')


@section('title', 'Tạo Slide mới')

@section('content')
<div class="container-fluid" id="page-slides-create">

  {{-- Thanh tiêu đề + hành động nhanh --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 mb-0 fw-bold">Tạo Slide mới</h1>
      <span class="text-muted small">Trang chủ / Slide</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.slides.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
      </a>
    </div>
  </div>

  {{-- Flash messages (thành công/lỗi) --}}
  @includeIf('admins.shared.flash')

  {{-- Form --}}
  <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
    @csrf

    @include('admins.slides._form')

    {{-- Action bar --}}
    <div class="d-flex justify-content-end gap-2 mt-3">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check2-circle me-1"></i> Tạo mới
      </button>
      <a href="{{ route('admin.slides.index') }}" class="btn btn-outline-secondary">
        Hủy
      </a>
    </div>
  </form>
</div>
@endsection
