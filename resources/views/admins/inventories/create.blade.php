@extends('admins.layouts.app')

@section('title', 'Thêm mới Kho hàng')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventories.index') }}">Kho hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Tạo mới bản ghi Kho hàng</h5></div>
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
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.inventories.store') }}" method="POST">
                @csrf
                {{-- Các trường thông tin sản phẩm và số lượng --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                        <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                            <option value="">Chọn sản phẩm</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="product_variant_id" class="form-label">Biến thể</label>
                        <select class="form-select @error('product_variant_id') is-invalid @enderror" id="product_variant_id" name="product_variant_id">
                            <option value="">Chọn biến thể (nếu có)</option>
                            @foreach($variants as $variant)
                                <option value="{{ $variant->id }}" data-product-id="{{ $variant->product_id }}" class="d-none" {{ old('product_variant_id') == $variant->id ? 'selected' : '' }}>{{ $variant->getDisplayNameAttribute() }}</option>
                            @endforeach
                        </select>
                        @error('product_variant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" required min="0">
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <hr>
                <h5 class="mt-4">Thông tin Vị trí</h5>
                
                {{-- Các trường ẩn để lưu tên địa chỉ --}}
                <input type="hidden" name="city_name" id="city_name">
                <input type="hidden" name="district_name" id="district_name">
                <input type="hidden" name="ward_name" id="ward_name">

                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ chi tiết (Số nhà, đường)</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" placeholder="Ví dụ: 123 Đường ABC">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="city_id" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                        <select class="form-select @error('city_name') is-invalid @enderror" id="city_id" name="city_id" required>
                            <option value="">Chọn Tỉnh/Thành phố</option>
                             @foreach($cities as $city)
                                <option value="{{ $city['code'] }}" {{ old('city_id') == $city['code'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                            @endforeach
                        </select>
                        @error('city_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="district_id" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                        <select class="form-select @error('district_name') is-invalid @enderror" id="district_id" name="district_id" required>
                            <option value="">Chọn Quận/Huyện</option>
                        </select>
                        @error('district_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ward_id" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                        <select class="form-select @error('ward_name') is-invalid @enderror" id="ward_id" name="ward_id" required>
                            <option value="">Chọn Phường/Xã</option>
                        </select>
                        @error('ward_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Tạo mới</button>
                <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Product & Variant Logic ---
    const productIdSelect = document.getElementById('product_id');
    const variantIdSelect = document.getElementById('product_variant_id');
    
    function filterVariants() {
        const selectedProductId = productIdSelect.value;
        variantIdSelect.value = '';
        Array.from(variantIdSelect.options).forEach(option => {
            if (option.value === '') return;
            option.classList.toggle('d-none', option.dataset.productId !== selectedProductId);
        });
    }
    productIdSelect.addEventListener('change', filterVariants);
    if (productIdSelect.value) {
        filterVariants();
    }

    // --- Location Logic (sử dụng API provinces.open-api.vn) ---
    const citySelect = document.getElementById('city_id');
    const districtSelect = document.getElementById('district_id');
    const wardSelect = document.getElementById('ward_id');

    const cityNameInput = document.getElementById('city_name');
    const districtNameInput = document.getElementById('district_name');
    const wardNameInput = document.getElementById('ward_name');

    // old values for re-populating on validation error
    const oldCityCode = '{{ old('city_id') }}';
    const oldDistrictCode = '{{ old('district_id') }}';
    const oldWardCode = '{{ old('ward_id') }}';

    async function fetchData(url, selectElement, placeholder, selectedValue = null) {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        if (!url) return;
        try {
            const response = await fetch(url);
            const data = await response.json();
            data.forEach(item => {
                const option = new Option(item.name, item.code);
                if (item.code == selectedValue) {
                    option.selected = true;
                }
                selectElement.add(option);
            });
            // If a value was pre-selected, dispatch change to load next level
            if (selectedValue && selectElement.value === selectedValue) {
                selectElement.dispatchEvent(new Event('change'));
            }
        } catch (error) {
            console.error('Failed to fetch data:', error);
        }
    }

    citySelect.addEventListener('change', function() {
        cityNameInput.value = this.options[this.selectedIndex].textContent;
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        districtNameInput.value = '';
        wardNameInput.value = '';
        if (this.value) {
            fetchData(`{{ url('admin/inventories/getDistricts') }}/${this.value}`, districtSelect, 'Chọn Quận/Huyện', oldDistrictCode);
        }
    });

    districtSelect.addEventListener('change', function() {
        districtNameInput.value = this.options[this.selectedIndex].textContent;
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        wardNameInput.value = '';
        if (this.value) {
            fetchData(`{{ url('admin/inventories/getWards') }}/${this.value}`, wardSelect, 'Chọn Phường/Xã', oldWardCode);
        }
    });

    wardSelect.addEventListener('change', function() {
        wardNameInput.value = this.options[this.selectedIndex].textContent;
    });

    // Populate old values if validation failed
    if (oldCityCode) {
        const cityOption = Array.from(citySelect.options).find(opt => opt.value === oldCityCode);
        if (cityOption) {
            cityOption.selected = true;
            citySelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endpush
