@extends('layouts.app')

@section('title', 'Thông tin tài khoản')

@section('content')
    {{-- Banner --}}
    <div class="support-banner d-flex align-items-center justify-content-center text-white mb-5">
        <div class="container text-center" data-aos="fade-in">
            <h1 class="display-4">Tài Khoản Của Tôi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thông tin tài khoản</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container my-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-up">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" data-aos="fade-up">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            {{-- Cột Menu Trái --}}
            <div class="col-lg-3">
                <div class="profile-menu card-glass rounded-4" data-aos="fade-right">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            @php
                                use Illuminate\Support\Str;
                                $user = Auth::user();
                                $avatar_path = optional($user->profile)->avatar;
                                $is_url = $avatar_path && Str::startsWith($avatar_path, 'http');
                                $avatar_url = $is_url ? $avatar_path : ($avatar_path ? asset('storage/' . $avatar_path) : 'https://via.placeholder.com/150');
                            @endphp
                            <img src="{{ $avatar_url }}" alt="Avatar" class="profile-avatar rounded-circle mb-3">
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#profile-info" class="list-group-item list-group-item-action active" data-bs-toggle="tab"><i class="bi bi-person-circle me-2"></i>Thông tin hồ sơ</a>
                            <a href="#change-password" class="list-group-item list-group-item-action" data-bs-toggle="tab"><i class="bi bi-key-fill me-2"></i>Đổi mật khẩu</a>
                            <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action"><i class="bi bi-box-seam me-2"></i>Đơn hàng của tôi</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột Nội Dung Phải --}}
            <div class="col-lg-9">
                <div class="tab-content" data-aos="fade-left" data-aos-delay="100">
                    {{-- Tab Thông Tin Hồ Sơ --}}
                    <div class="tab-pane fade show active" id="profile-info">
                        <div class="card card-glass rounded-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 text-brand">Chỉnh sửa thông tin</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4 text-center mb-4 mb-md-0">
                                            <img src="{{ $avatar_url }}" alt="Avatar" id="avatar-preview" class="img-thumbnail rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                                            <input type="file" class="form-control form-control-modern" name="avatar" id="avatar" onchange="document.getElementById('avatar-preview').src = window.URL.createObjectURL(this.files[0])">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Họ và tên</label>
                                                <input type="text" class="form-control form-control-modern" id="name" name="name" value="{{ old('name', $user->name) }}">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="dob" class="form-label">Ngày sinh</label>
                                                    <input type="date" class="form-control form-control-modern" id="dob" name="dob" value="{{ old('dob', optional($user->profile)->dob) }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Giới tính</label>
                                                    <div class="pt-2">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender" id="male" value="Nam" {{ old('gender', optional($user->profile)->gender) == 'Nam' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="male">Nam</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender" id="female" value="Nữ" {{ old('gender', optional($user->profile)->gender) == 'Nữ' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="female">Nữ</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h6 class="mt-3 text-brand">Địa chỉ mặc định</h6>
                                            <hr class="mt-1">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="province_id" class="form-label">Tỉnh/Thành phố</label>
                                                    <select class="form-select form-control-modern" id="province_id" name="province_id"></select>
                                                    <input type="hidden" name="province_name" id="province_name_input" value="{{ old('province_name', optional($user->profile)->province_name) }}">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="district_id" class="form-label">Quận/Huyện</label>
                                                    <select class="form-select form-control-modern" id="district_id" name="district_id"></select>
                                                    <input type="hidden" name="district_name" id="district_name_input" value="{{ old('district_name', optional($user->profile)->district_name) }}">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="ward_code" class="form-label">Phường/Xã</label>
                                                    <select class="form-select form-control-modern" id="ward_code" name="ward_code"></select>
                                                    <input type="hidden" name="ward_name" id="ward_name_input" value="{{ old('ward_name', optional($user->profile)->ward_name) }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Địa chỉ cụ thể</label>
                                                <input type="text" class="form-control form-control-modern" id="address" name="address" value="{{ old('address', optional($user->profile)->address) }}" placeholder="Ví dụ: 123 Nguyễn Văn Linh">
                                            </div>
                                            <button type="submit" class="btn btn-brand"><i class="bi bi-save me-2"></i>Lưu thay đổi</button喧

System: đổi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Tab Đổi Mật Khẩu --}}
                        <div class="tab-pane fade" id="change-password">
                            <div class="card card-glass rounded-4">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0 text-brand">Đổi mật khẩu</h5>
                                </div>
                                <div class="card-body p-4">
                                    <form action="{{ route('profile.change-password') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                            <input type="password" class="form-control form-control-modern @error('current_password') is-invalid @enderror" name="current_password" required>
                                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                                            <input type="password" class="form-control form-control-modern @error('new_password') is-invalid @enderror" name="new_password" required>
                                            @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                            <input type="password" class="form-control form-control-modern" name="new_password_confirmation" required>
                                        </div>
                                        <button type="submit" class="btn btn-brand"><i class="bi bi-shield-lock me-2"></i>Đổi mật khẩu</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* =================== Banner =================== */
        .support-banner {
            height: 250px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
                url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .support-banner .breadcrumb-item a {
            color: var(--sand);
            text-decoration: none;
        }
        .support-banner .breadcrumb-item a:hover {
            color: var(--brand);
        }
        .support-banner .breadcrumb-item.active {
            color: var(--muted);
        }

        /* =================== Profile Menu =================== */
        .profile-avatar {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 4px solid var(--card);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .profile-menu .list-group-item {
            border: none;
            padding: 0.9rem 1.25rem;
            font-weight: 500;
            color: var(--text);
            transition: background 0.2s ease;
        }
        .profile-menu .list-group-item.active {
            background-color: var(--brand);
            color: #fff;
            border-radius: 8px;
        }
        .profile-menu .list-group-item:not(.active):hover {
            background-color: var(--sand);
            color: var(--brand);
        }

        /* =================== Form and Button Styles =================== */
        .form-control-modern, .form-select.form-control-modern {
            border-radius: 0.8rem;
            border: 1px solid #e9ecef;
            background: #fff;
            font-size: 1rem;
        }
        .form-control-modern:focus, .form-select.form-control-modern:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.2rem var(--ring);
        }
        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
            padding: 0.5rem 1rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .btn-brand:hover {
            background-color: var(--brand-600);
            border-color: var(--brand-600);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .text-brand {
            color: var(--brand);
        }
        .text-muted {
            color: var(--muted);
        }
        .card-header.bg-white {
            background: transparent !important;
        }

        /* =================== Card Styles =================== */
        .card-glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-glass:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        .rounded-4 {
            border-radius: 1rem !important;
        }

        /* =================== Links =================== */
        a {
            color: var(--brand);
            text-decoration: none;
        }
        a:hover {
            color: var(--brand-600);
        }

        /* =================== Responsive Design =================== */
        @media (max-width: 991px) {
            .support-banner {
                height: 220px;
            }
            .support-banner .display-4 {
                font-size: 2rem;
            }
            .col-lg-3, .col-lg-9 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .profile-avatar {
                width: 80px;
                height: 80px;
            }
            .card-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .support-banner {
                height: 180px;
            }
            .support-banner .display-4 {
                font-size: 1.8rem;
            }
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            .card-body {
                padding: 1rem;
            }
            .btn-brand {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
            .form-control-modern, .form-select.form-control-modern {
                font-size: 0.9rem;
            }
            .profile-avatar {
                width: 70px;
                height: 70px;
            }
            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 1rem;
            }
            .col-md-8 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 575px) {
            .support-banner {
                height: 160px;
            }
            .support-banner .display-4 {
                font-size: 1.6rem;
            }
            .support-banner .breadcrumb {
                font-size: 0.85rem;
            }
            .card-glass {
                padding: 0.75rem;
            }
            .card-body {
                padding: 0.75rem;
            }
            .btn-brand {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .form-control-modern, .form-select.form-control-modern {
                font-size: 0.85rem;
            }
            .profile-avatar {
                width: 60px;
                height: 60px;
            }
            .list-group-item {
                padding: 0.7rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
@endpush

@push('scripts-page')
<script>
    // ===== GHN address selects =====
    (function () {
        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');
        const wardSelect = document.getElementById('ward_code');
        if (!(provinceSelect && districtSelect && wardSelect)) return;

        const provinceNameInput = document.getElementById('province_name_input');
        const districtNameInput = document.getElementById('district_name_input');
        const wardNameInput = document.getElementById('ward_name_input');

        const saved = {
            province: provinceNameInput?.value || '',
            district: districtNameInput?.value || '',
            ward: wardNameInput?.value || ''
        };

        const fetchJson = async (url) => {
            try { 
                const r = await fetch(url, { headers: { 'Accept': 'application/json' } }); 
                return r.ok ? r.json() : []; 
            } catch { return []; }
        };
        const renderOptions = (select, list, placeholder, valKey, textKey, pickedText = '') => {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            if (!Array.isArray(list)) return;
            let pickedVal = null;
            for (const item of list) {
                const opt = new Option(item[textKey], item[valKey]);
                if (pickedText && item[textKey] === pickedText) { opt.selected = true; pickedVal = item[valKey]; }
                select.add(opt);
            }
            if (pickedVal) {
                select.value = pickedVal;
                setTimeout(() => select.dispatchEvent(new Event('change', { bubbles: true })), 0);
            }
        };

        const loadProvinces = async () => {
            const provinces = await fetchJson('{{ route("address.provinces") }}');
            renderOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành', 'ProvinceID', 'ProvinceName', saved.province);
        };

        provinceSelect.addEventListener('change', async function () {
            if (provinceNameInput) provinceNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
            renderOptions(districtSelect, [], 'Vui lòng chờ...', 'DistrictID', 'DistrictName');
            renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
            if (this.value) {
                const districts = await fetchJson(`{{ route("address.districts") }}?province_id=${this.value}`);
                renderOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', saved.district);
            }
        });

        districtSelect.addEventListener('change', async function () {
            if (districtNameInput) districtNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
            renderOptions(wardSelect, [], 'Vui lòng chờ...', 'WardCode', 'WardName');
            if (this.value) {
                const wards = await fetchJson(`{{ route("address.wards") }}?district_id=${this.value}`);
                renderOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', saved.ward);
            }
        });

        wardSelect.addEventListener('change', function () {
            if (wardNameInput) wardNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
        });

        loadProvinces();
    })();
</script>
@endpush