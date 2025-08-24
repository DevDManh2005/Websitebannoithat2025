{{-- resources/views/admins/brands/create.blade.php --}}
@extends('admins::layouts.app')

@section('title','Tạo Thương hiệu')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="container-fluid">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Tạo Thương hiệu</h1>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="row g-3" novalidate>
    @csrf

    <div class="col-lg-8">
      <div class="card card-soft">
        <div class="card-header"><strong>Thông tin thương hiệu</strong></div>
        <div class="card-body">
          <div class="mb-3">
            <label for="name" class="form-label">Tên thương hiệu <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="logo_file" class="form-label">Logo (Upload file)</label>
              <input type="file" name="logo_file" id="logo_file" class="form-control" accept="image/*">
              <div class="form-text">Ưu tiên file upload nếu bạn chọn cả file và URL.</div>
            </div>
            <div class="col-md-6">
              <label for="logo_url" class="form-label">Logo (URL)</label>
              <input type="url" name="logo_url" id="logo_url" class="form-control" value="{{ old('logo_url') }}" placeholder="https://example.com/logo.png">
            </div>
          </div>

          <div class="mt-3">
            <img id="logoPreview" class="img-fluid rounded d-none" alt="Logo preview">
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card card-soft">
        <div class="card-header"><strong>Cài đặt</strong></div>
        <div class="card-body">
          <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Kích hoạt</label>
          </div>
        </div>
        <div class="card-footer bg-white border-0 d-grid">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-save me-1"></i> Lưu lại
          </button>
        </div>
      </div>
    </div>

  </form>
</div>
@endsection

@push('scripts')
<script>
  const fileInput = document.getElementById('logo_file');
  const urlInput  = document.getElementById('logo_url');
  const preview   = document.getElementById('logoPreview');

  function showPreviewFromUrl(url){
    if(!url){ preview.classList.add('d-none'); preview.src=''; return; }
    preview.src = url;
    preview.classList.remove('d-none');
  }

  fileInput?.addEventListener('change', e => {
    const f = e.target.files?.[0];
    if(!f){ showPreviewFromUrl(urlInput.value); return; }
    const reader = new FileReader();
    reader.onload = ev => showPreviewFromUrl(ev.target.result);
    reader.readAsDataURL(f);
  });

  urlInput?.addEventListener('input', e => {
    if(fileInput.files && fileInput.files.length){ return; } // ưu tiên file
    showPreviewFromUrl(e.target.value);
  });

  // khởi tạo preview nếu có sẵn URL
  if(urlInput?.value){ showPreviewFromUrl(urlInput.value); }
</script>
@endpush
