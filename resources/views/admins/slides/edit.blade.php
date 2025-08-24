@extends(auth()->user()->role->name === 'staff' ? 'staff.layouts.app' : 'admins.layouts.app')



@section('title', 'Sửa Slide')

@section('content')
<div class="container-fluid" id="page-slides-edit">

  {{-- Thanh tiêu đề + hành động nhanh --}}
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 mb-0 fw-bold">Sửa Slide</h1>
      <span class="text-muted small">#{{ $slide->id }}</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.slides.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
      </a>
    </div>
  </div>

  {{-- Flash messages --}}
  @includeIf('admins.shared.flash')

  {{-- Form --}}
  <form action="{{ route('admin.slides.update', $slide) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
    @csrf
    @method('PUT')

    @include('admins.slides._form')

    {{-- Action bar --}}
    <div class="d-flex justify-content-end gap-2 mt-3">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> Cập nhật
      </button>
      <a href="{{ route('admin.slides.index') }}" class="btn btn-outline-secondary">
        Hủy
      </a>
    </div>
  </form>
</div>
@endsection
