@extends('admins.layouts.app')

@section('title', 'Chỉnh sửa Kho hàng')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventories.index') }}">Kho hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Chỉnh sửa bản ghi Kho hàng #{{ $inventory->id }}</h5></div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.inventories.update', $inventory->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                        <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                            <option value="">Chọn sản phẩm</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $inventory->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="product_variant_id" class="form-label">Biến thể</label>
                        <select class="form-select @error('product_variant_id') is-invalid @enderror" id="product_variant_id" name="product_variant_id">
                            <option value="">Chọn biến thể (nếu sản phẩm có)</option>
                            {{-- Options sẽ được thêm bằng JavaScript --}}
                        </select>
                        @error('product_variant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $inventory->quantity) }}" required min="0">
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ kho</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', optional($inventory->location)->address) }}" placeholder="Ví dụ: 123 Đường ABC, Quận Sơn Trà, Đà Nẵng">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allVariants = @json($variants);
    const productIdSelect = document.getElementById('product_id');
    const variantIdSelect = document.getElementById('product_variant_id');
    // Lấy ID biến thể cũ từ old() hoặc từ bản ghi inventory đang sửa
    const oldVariantId = '{{ old('product_variant_id', $inventory->product_variant_id) }}';

    function filterVariants() {
        const selectedProductId = productIdSelect.value;
        variantIdSelect.innerHTML = '<option value="">Chọn biến thể (nếu sản phẩm có)</option>';
        
        if (!selectedProductId) return;

        const filteredVariants = allVariants.filter(v => v.product_id == selectedProductId);
        
        if (filteredVariants.length === 0) {
            variantIdSelect.disabled = true;
        } else {
            variantIdSelect.disabled = false;
            filteredVariants.forEach(variant => {
                const parts = Object.entries(variant.attributes).map(([key, value]) => `${key}: ${value}`);
                const displayName = `${variant.sku} (${parts.join(', ')})`;
                const option = new Option(displayName, variant.id);
                // Kiểm tra và chọn lại biến thể cũ
                if(variant.id == oldVariantId) {
                    option.selected = true;
                }
                variantIdSelect.add(option);
            });
        }
    }

    productIdSelect.addEventListener('change', filterVariants);
    if (productIdSelect.value) {
        filterVariants();
    }
});
</script>
@endpush