@extends('layouts.app')

@section('title', 'Thông tin tài khoản')

@section('content')
<div class="container my-4">
    <h2>Thông tin tài khoản</h2>
    <p class="text-muted">Quản lý thông tin hồ sơ để bảo mật tài khoản của bạn.</p>
    <hr>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Cột thông tin chính --}}
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="name" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly disabled>
                    <div class="form-text">Bạn không thể thay đổi email.</div>
                </div>
                <div class="mb-3">
                    <label for="dob" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob', optional($user->profile)->dob) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Giới tính</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="male" value="Nam" {{ old('gender', optional($user->profile)->gender) == 'Nam' ? 'checked' : '' }}>
                            <label class="form-check-label" for="male">Nam</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="female" value="Nữ" {{ old('gender', optional($user->profile)->gender) == 'Nữ' ? 'checked' : '' }}>
                            <label class="form-check-label" for="female">Nữ</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="other" value="Khác" {{ old('gender', optional($user->profile)->gender) == 'Khác' ? 'checked' : '' }}>
                            <label class="form-check-label" for="other">Khác</label>
                        </div>
                    </div>
                </div>

                {{-- Phần địa chỉ dùng API --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="province" class="form-label">Tỉnh/Thành phố</label>
                        <select class="form-select" id="province"></select>
                        <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', optional($user->profile)->province_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="district" class="form-label">Quận/Huyện</label>
                        <select class="form-select" id="district"></select>
                        <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', optional($user->profile)->district_name) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ward" class="form-label">Phường/Xã</label>
                        <select class="form-select" id="ward"></select>
                         <input type="hidden" name="ward_name" id="ward_name" value="{{ old('ward_name', optional($user->profile)->ward_name) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">Giới thiệu</label>
                    <textarea class="form-control" id="bio" name="bio" rows="3">{{ old('bio', optional($user->profile)->bio) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Lưu thay đổi thông tin</button>
            </div>

            {{-- Cột Avatar --}}
            <div class="col-md-4 text-center">
                @php
                    $avatar_path = optional($user->profile)->avatar;
                    $is_url = $avatar_path && Illuminate\Support\Str::startsWith($avatar_path, 'http');
                @endphp
                
                <img src="{{ $is_url ? $avatar_path : ($avatar_path ? asset('storage/' . $avatar_path) : 'https://via.placeholder.com/150') }}" 
                     alt="Avatar" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">

                <div class="mb-3">
                    <label for="avatar" class="form-label">Chọn ảnh mới</label>
                    <input type="file" class="form-control" name="avatar" id="avatar">
                </div>
                
                <div class="mb-3">
                    <label for="avatar_url" class="form-label">Hoặc dán link ảnh</label>
                    <input type="text" class="form-control" name="avatar_url" id="avatar_url" placeholder="https://example.com/image.png">
                </div>
            </div>
        </div>
    </form>

    <hr class="my-5">

    {{-- FORM ĐỔI MẬT KHẨU --}}
    <h2 class="mt-4">Đổi mật khẩu</h2>
    <p class="text-muted">Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác.</p>
    
    <form action="{{ route('profile.change-password') }}" method="POST" class="mt-3">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                     @error('new_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-danger">Đổi mật khẩu</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');

    const provinceNameInput = document.getElementById('province_name');
    const districtNameInput = document.getElementById('district_name');
    const wardNameInput = document.getElementById('ward_name');
    
    const savedAddress = {
        province: provinceNameInput.value,
        district: districtNameInput.value,
        ward: wardNameInput.value
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
@endsection