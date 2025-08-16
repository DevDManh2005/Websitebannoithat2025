@extends('admins.layouts.app')

@section('title', 'Chỉnh sửa Người dùng')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Chỉnh sửa Người dùng: {{ $user->name }}</h1>

    <div class="card shadow mb-4">
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

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <h5>Thông tin tài khoản</h5>
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Để trống nếu không muốn thay đổi.</small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>

                        <hr class="my-4">
                        <h5>Thông tin hồ sơ</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dob" class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob', optional($user->profile)->dob) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Giới tính</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Chọn giới tính</option>
                                    <option value="Nam" {{ old('gender', optional($user->profile)->gender) == 'Nam' ? 'selected' : '' }}>Nam</option>
                                    <option value="Nữ" {{ old('gender', optional($user->profile)->gender) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                    <option value="Khác" {{ old('gender', optional($user->profile)->gender) == 'Khác' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Thêm các input ẩn để lưu tên địa chỉ --}}
                        <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', optional($user->profile)->province_name) }}">
                        <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', optional($user->profile)->district_name) }}">
                        <input type="hidden" name="ward_name" id="ward_name" value="{{ old('ward_name', optional($user->profile)->ward_name) }}">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="province" class="form-label">Tỉnh/Thành phố</label>
                                {{-- Thêm name="province_id" để gửi ID đi --}}
                                <select class="form-select" id="province" name="province_id"></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district" class="form-label">Quận/Huyện</label>
                                {{-- Thêm name="district_id" để gửi ID đi --}}
                                <select class="form-select" id="district" name="district_id"></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ward" class="form-label">Phường/Xã</label>
                                {{-- Thêm name="ward_code" để gửi mã đi --}}
                                <select class="form-select" id="ward" name="ward_code"></select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ chi tiết</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', optional($user->profile)->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật người dùng</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');

    const provinceNameInput = document.getElementById('province_name');
    const districtNameInput = document.getElementById('district_name');
    const wardNameInput = document.getElementById('ward_name');
    
    // Lấy tên địa chỉ cũ đã lưu trong DB để chọn lại
    const savedAddress = {
        province: "{{ old('province_name', optional($user->profile)->province_name) }}",
        district: "{{ old('district_name', optional($user->profile)->district_name) }}",
        ward: "{{ old('ward_name', optional($user->profile)->ward_name) }}"
    };

    async function fetchApi(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) return [];
            return await response.json();
        } catch (error) {
            console.error('Lỗi fetch API:', error); 
            return [];
        }
    }
    
    function renderOptions(selectElement, data, placeholder, valueKey, textKey, selectedText = "") {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        if (!Array.isArray(data)) return;

        let selectedValue = null;
        data.forEach(item => {
            const option = new Option(item[textKey], item[valueKey]);
            if (item[textKey] === selectedText) {
                option.selected = true;
                selectedValue = item[valueKey];
            }
            selectElement.add(option);
        });
        
        if (selectedValue) {
            selectElement.value = selectedValue;
            selectElement.dispatchEvent(new Event('change'));
        }
    }

    async function loadProvinces() {
        const provinces = await fetchApi('{{ route("address.provinces") }}');
        renderOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành phố', 'ProvinceID', 'ProvinceName', savedAddress.province);
    }

    provinceSelect.addEventListener('change', async function() {
        provinceNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : "";
        renderOptions(districtSelect, [], 'Vui lòng chờ...', 'DistrictID', 'DistrictName');
        renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
        if (this.value) {
            const districts = await fetchApi(`{{ route("address.districts") }}?province_id=${this.value}`);
            renderOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', savedAddress.district);
        }
    });

    districtSelect.addEventListener('change', async function() {
        districtNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : "";
        renderOptions(wardSelect, [], 'Vui lòng chờ...', 'WardCode', 'WardName');
        if (this.value) {
            const wards = await fetchApi(`{{ route("address.wards") }}?district_id=${this.value}`);
            renderOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', savedAddress.ward);
        }
    });

    wardSelect.addEventListener('change', function() {
        wardNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : "";
    });

    loadProvinces();
});
</script>
@endpush