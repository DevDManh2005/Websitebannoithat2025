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
                        
                        {{-- Hidden inputs for location names --}}
                        <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', optional($user->profile)->province_name) }}">
                        <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', optional($user->profile)->district_name) }}">
                        <input type="hidden" name="ward_name" id="ward_name" value="{{ old('ward_name', optional($user->profile)->ward_name) }}">

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="province" class="form-label">Tỉnh/Thành phố</label>
                                <select class="form-select" id="province"></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district" class="form-label">Quận/Huyện</label>
                                <select class="form-select" id="district"></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ward" class="form-label">Phường/Xã</label>
                                <select class="form-select" id="ward"></select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Tiểu sử</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', optional($user->profile)->bio) }}</textarea>
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
    
    const oldProvinceName = "{{ old('province_name', optional($user->profile)->province_name) }}";
    const oldDistrictName = "{{ old('district_name', optional($user->profile)->district_name) }}";
    const oldWardName = "{{ old('ward_name', optional($user->profile)->ward_name) }}";

    async function fetchAddressData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            const result = await response.json();
            if (Array.isArray(result)) {
                return result;
            }
            return [];
        } catch (error) {
            console.error('Failed to fetch address data:', error);
            return [];
        }
    }

    function renderAddressOptions(selectElement, data, placeholder, valueKey, textKey, selectedValue = "") {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        if (!Array.isArray(data)) return;

        data.forEach(item => {
            const option = new Option(item[textKey], item[valueKey]);
            if (item[textKey] === selectedValue) {
                option.selected = true;
            }
            selectElement.add(option);
        });
        if (selectedValue && selectElement.value) {
            selectElement.dispatchEvent(new Event('change'));
        }
    }

    async function loadProvinces() {
        const provinces = await fetchAddressData('{{ route("address.provinces") }}');
        renderAddressOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành phố', 'id', 'name', oldProvinceName);
    }

    provinceSelect.addEventListener('change', async function() {
        provinceNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if (this.value) {
            const districts = await fetchAddressData(`{{ route("address.districts") }}?province_id=${this.value}`);
            renderAddressOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'id', 'name', oldDistrictName);
        }
    });

    districtSelect.addEventListener('change', async function() {
        districtNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if (this.value) {
            const wards = await fetchAddressData(`{{ route("address.wards") }}?district_id=${this.value}`);
            renderAddressOptions(wardSelect, wards, 'Chọn Phường/Xã', 'id', 'name', oldWardName);
        }
    });

    wardSelect.addEventListener('change', function() {
        wardNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
    });

    loadProvinces();
});
</script>
@endpush
