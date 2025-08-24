@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')



@section('title', 'Cập nhật Kho hàng')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.inventories.index') }}">Kho hàng</a></li>
      <li class="breadcrumb-item active" aria-current="page">Cập nhật hàng loạt</li>
    </ol>
  </nav>

  <div class="card card-soft">
    <div class="card-header"><h5 class="card-title mb-0">Cập nhật theo Sản phẩm</h5></div>
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
      @endif

      <form action="{{ route('admin.inventories.store') }}" method="POST">
        @csrf
        <div class="row">
          <div class="col-md-12 mb-3">
            <label for="product_id" class="form-label">Chọn Sản phẩm <span class="text-danger">*</span></label>
            <select class="form-select" id="product_id" name="product_id" required>
              <option value="">-- Chọn một sản phẩm --</option>
              @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <hr>

        <div id="variants-container">
          <p class="text-muted text-center">Vui lòng chọn một sản phẩm để hiển thị các biến thể.</p>
        </div>

        <div class="mt-3">
          <button type="submit" class="btn btn-primary" id="save-inventory-btn" style="display:none;">
            <i class="bi bi-save"></i> Lưu thay đổi
          </button>
          <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const productIdSelect   = document.getElementById('product_id');
  const variantsContainer = document.getElementById('variants-container');
  const saveBtn           = document.getElementById('save-inventory-btn');

  productIdSelect.addEventListener('change', async function() {
    const productId = this.value;
    variantsContainer.innerHTML =
      '<div class="text-center py-3"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    saveBtn.style.display = 'none';

    if (!productId) {
      variantsContainer.innerHTML = '<p class="text-muted text-center">Vui lòng chọn một sản phẩm để hiển thị các biến thể.</p>';
      return;
    }

    try {
      const url = `/admin/products/${productId}/variants-inventory`;
      const response = await fetch(url);
      const variants = await response.json();

      variantsContainer.innerHTML = '';

      if (Array.isArray(variants) && variants.length > 0) {
        variants.forEach((variant, index) => {
          const attrs = variant.attributes ? Object.entries(variant.attributes).map(([k,v]) => `${k}: ${v}`).join(', ') : '';
          const address = variant.inventory && variant.inventory.location ? (variant.inventory.location.address || '') : '';
          const quantity = variant.inventory ? (variant.inventory.quantity || 0) : 0;

          const html = `
            <div class="card card-soft mb-3">
              <div class="card-body">
                <h6 class="card-title mb-3">
                  ${variant.sku}
                  ${attrs ? `<small class="text-muted">(${attrs})</small>` : ''}
                </h6>
                <input type="hidden" name="variants[${index}][id]" value="${variant.id}">
                <div class="row">
                  <div class="col-md-4 mb-2">
                    <label class="form-label">Số lượng tồn kho</label>
                    <input type="number" class="form-control" name="variants[${index}][quantity]" value="${quantity}" min="0" required>
                  </div>
                  <div class="col-md-8 mb-2">
                    <label class="form-label">Địa chỉ kho</label>
                    <input type="text" class="form-control" name="variants[${index}][address]" value="${address}" placeholder="Ví dụ: Kho A, 123 Đường ABC...">
                  </div>
                </div>
              </div>
            </div>
          `;
          variantsContainer.insertAdjacentHTML('beforeend', html);
        });
        saveBtn.style.display = 'inline-block';
      } else {
        variantsContainer.innerHTML = '<p class="text-danger text-center">Sản phẩm này không có biến thể nào.</p>';
      }
    } catch (err) {
      console.error(err);
      variantsContainer.innerHTML = '<p class="text-danger text-center">Đã xảy ra lỗi khi tải biến thể.</p>';
    }
  });
});
</script>
@endpush
