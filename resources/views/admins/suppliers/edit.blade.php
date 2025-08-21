@extends('admins.layouts.app')

@section('title', 'Sửa nhà cung cấp')

@push('styles')
<style>
  #supplier-form .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #supplier-form .input-group-text{ background:#fff; border-right:0 }
  #supplier-form .form-control{ border-left:0 }
  #supplier-form .form-help{ color:var(--muted); font-size:.875rem }
</style>
@endpush

@section('content')
<div id="supplier-form" class="container-fluid">
  {{-- Header --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Sửa nhà cung cấp</h1>
      <span class="text-muted small">#{{ $supplier->id }}</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left-short me-1"></i> Quay lại
      </a>
      <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST"
            onsubmit="return confirm('Xóa nhà cung cấp này?')" class="d-inline">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">
          <i class="bi bi-trash me-1"></i> Xóa
        </button>
      </form>
      <button form="supplier-edit-form" class="btn btn-primary">
        <i class="bi bi-save2 me-1"></i> Cập nhật
      </button>
    </div>
  </div>

  @includeIf('admins.shared.flash')

  <form id="supplier-edit-form" action="{{ route('admin.suppliers.update', $supplier->id) }}" method="POST" novalidate>
    @csrf @method('PUT')

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
                     value="{{ old('name',$supplier->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Người liên hệ</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <input type="text" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror"
                     value="{{ old('contact_name',$supplier->contact_name) }}">
              @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Điện thoại</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-telephone"></i></span>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                     value="{{ old('phone',$supplier->phone) }}">
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                     value="{{ old('email',$supplier->email) }}">
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">Địa chỉ</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
              <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                     value="{{ old('address',$supplier->address) }}">
              @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-help mt-1">Cập nhật để in chứng từ đúng địa chỉ giao dịch.</div>
          </div>
        </div>
      </div>

      <div class="card-footer d-flex gap-2">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light">Hủy</a>
        <button class="btn btn-primary">Cập nhật</button>
      </div>
    </div>
  </form>
</div>

@push('scripts')
<script>
  // Ctrl+S để lưu
  window.addEventListener('keydown', function(e){
    if((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='s'){
      e.preventDefault(); document.getElementById('supplier-edit-form')?.submit();
    }
  });
</script>
@endpush
@endsection
