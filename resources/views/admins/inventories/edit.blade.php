@extends('admins.layouts.app')

@section('title', 'Chỉnh sửa Kho hàng')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumb --}}
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
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.inventories.update', $inventory->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- Product and Variant Selection --}}
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
                            <option value="">Chọn biến thể (nếu có)</option>
                            @foreach($variants as $variant)
                                <option value="{{ $variant->id }}" data-product-id="{{ $variant->product_id }}" class="d-none" {{ old('product_variant_id', $inventory->product_variant_id) == $variant->id ? 'selected' : '' }}>{{ $variant->getDisplayNameAttribute() }}</option>
                            @endforeach
                        </select>
                        @error('product_variant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $inventory->quantity) }}" required min="0">
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <hr>
                <h5 class="mt-4">Thông tin Vị trí</h5>

                {{-- Hidden inputs for location names --}}
                <input type="hidden" name="city_name" id="city_name" value="{{ old('city_name', optional($inventory->location)->city_name) }}">
                <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', optional($inventory->location)->district_name) }}">
                <input type="hidden" name="ward_name" id="ward_name" value="{{ old('ward_name', optional($inventory->location)->ward_name) }}">

                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ chi tiết (Số nhà, đường)</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', optional($inventory->location)->address) }}" placeholder="Ví dụ: 123 Đường ABC">
                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="province" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                        <select class="form-select @error('city_name') is-invalid @enderror" id="province" required></select>
                        @error('city_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="district" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                        <select class="form-select @error('district_name') is-invalid @enderror" id="district" required></select>
                        @error('district_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ward" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                        <select class="form-select @error('ward_name') is-invalid @enderror" id="ward" required></select>
                        @error('ward_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
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
    // --- Product & Variant Logic ---
    const productIdSelect = document.getElementById('product_id');
    const variantIdSelect = document.getElementById('product_variant_id');
    
    function filterVariants() {
        const selectedProductId = productIdSelect.value;
        const currentVariantId = variantIdSelect.value;
        let shouldResetVariant = true;

        Array.from(variantIdSelect.options).forEach(option => {
            if (option.value === '') return;
            const isVisible = option.dataset.productId === selectedProductId;
            option.classList.toggle('d-none', !isVisible);
            if (isVisible && option.value === currentVariantId) {
                shouldResetVariant = false;
            }
        });
        if (shouldResetVariant) {
            variantIdSelect.value = '';
        }
    }
    productIdSelect.addEventListener('change', filterVariants);
    if (productIdSelect.value) {
        filterVariants();
    }

    // --- Location Logic (sử dụng API GHTK) ---
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const provinceNameInput = document.getElementById('city_name');
    const districtNameInput = document.getElementById('district_name');
    const wardNameInput = document.getElementById('ward_name');
    
    const oldProvince = provinceNameInput.value;
    const oldDistrict = districtNameInput.value;
    const oldWard = wardNameInput.value;

    async function fetchAddressData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            const result = await response.json();
            // API công cộng của GHTK trả về mảng trực tiếp nếu thành công
            if (Array.isArray(result.data)) {
                return result.data;
            }
            return [];
        } catch (error) {
            console.error('Failed to fetch address data:', error); 
            return [];
        }
    }

    function renderAddressOptions(selectElement, data, placeholder, valueKey, textKey, selectedValue = "") {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = item[textKey];
            if (item[textKey] === selectedValue) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });
        if (selectedValue && selectElement.value) {
             selectElement.dispatchEvent(new Event('change'));
        }
    }

    async function loadProvinces() {
        const provinces = await fetchAddressData('{{ route("address.provinces") }}');
        renderAddressOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành phố', 'ProvinceID', 'ProvinceName', oldProvince);
    }

    provinceSelect.addEventListener('change', async function() {
        provinceNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if (this.value) {
            const districts = await fetchAddressData(`{{ route("address.districts") }}?province_id=${this.value}`);
            renderAddressOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', oldDistrict);
        }
    });

    districtSelect.addEventListener('change', async function() {
        districtNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if (this.value) {
            const wards = await fetchAddressData(`{{ route("address.wards") }}?district_id=${this.value}`);
            renderAddressOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', oldWard);
        }
    });

    wardSelect.addEventListener('change', function() {
        wardNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
    });

    loadProvinces();
});
</script>
@endpush
