@extends('admins.layouts.app')

@section('title', 'Thêm nhà cung cấp')

@push('styles')
<style>
  #supplier-form .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #supplier-form .input-group-text{
    background:#fff; border-right:0;
  }
  #supplier-form .form-control{
    border-left:0;
  }
  #supplier-form .form-help{ color:var(--muted); font-size:.875rem }
</style>
@endpush

@section('content')
<div id="supplier-form" class="container-fluid">
  {{-- Header --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Thêm nhà cung cấp</h1>
      <span class="text-muted small">Tạo đối tác mới cho đơn nhập/ký gửi…</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left-short me-1"></i> Quay lại
      </a>
      <button form="supplier-create-form" class="btn btn-primary">
        <i class="bi bi-save2 me-1"></i> Lưu
      </button>
    </div>
  </div>

  @includeIf('admins.shared.flash')

  <form id="supplier-create-form" action="{{ route('admin.suppliers.store') }}" method="POST" novalidate>
    @csrf

    <div class="card">
      <div class="card-header">
        <strong>Thông tin nhà cung cấp</strong>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Tên <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-building"></i></span>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                     placeholder="Công ty A / Cửa hàng B" value="{{ old('name') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Người liên hệ</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <input type="text" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror"
                     placeholder="Nguyễn Văn A" value="{{ old('contact_name') }}">
              @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Điện thoại</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-telephone"></i></span>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                     placeholder="09xx xxx xxx" value="{{ old('phone') }}">
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                     placeholder="supplier@domain.com" value="{{ old('email') }}">
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Địa chỉ</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
              <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                     placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành"
                     value="{{ old('address') }}">
              @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-help mt-1">Thông tin này sẽ hiển thị trên chứng từ nhập hàng.</div>
          </div>
        </div>
      </div>

      <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light">Hủy</a>
        <button class="btn btn-primary">Lưu</button>
      </div>
    </div>
  </form>
</div>

@push('scripts')
<script>
  // Ctrl+S để lưu
  window.addEventListener('keydown', function(e){
    if((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='s'){
      e.preventDefault(); document.getElementById('supplier-create-form')?.submit();
    }
  });
</script>
@endpush
@endsection
