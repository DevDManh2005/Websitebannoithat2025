@extends('admins.layouts.app')

@section('title', 'Chỉnh sửa Người dùng')

@push('styles')
<style>
  /* ===== Scoped styles cho trang Edit User ===== */
  #user-edit .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #user-edit .card-soft{
    border-radius:16px; border:1px solid rgba(32,25,21,.08);
    box-shadow:0 6px 22px rgba(18,38,63,.06); background:var(--card);
  }
  #user-edit .card-soft .card-header{
    background:transparent; border-bottom:1px dashed rgba(32,25,21,.12);
  }
  #user-edit .hint{ color:#6c757d; font-size:.875rem }
  #user-edit .required::after{ content:' *'; color:#dc3545; }
  #user-edit .section-title{ font-weight:700; font-size:1rem }
  #user-edit .grid-2{ display:grid; gap:12px; grid-template-columns:1fr; }
  @media (min-width: 992px){ #user-edit .grid-2{ grid-template-columns:1fr 1fr; } }

  /* Nút action cố định cạnh dưới card */
  #user-edit .sticky-actions{
    position: sticky; bottom: -1px; z-index: 1; background: linear-gradient(180deg, rgba(234,223,206,.25), var(--card) 30%);
    border-top:1px dashed rgba(32,25,21,.12); padding-top:10px;
  }

  /* Select loading state */
  #user-edit .loading{ opacity:.6; pointer-events:none }
</style>
@endpush

@section('content')
<div id="user-edit" class="container-fluid">
  {{-- Header bar --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Chỉnh sửa Người dùng</h1>
      <span class="text-muted small">#{{ $user->id }}</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.users.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left-short me-1"></i> Quay lại
      </a>
      <button form="user-edit-form" class="btn btn-primary">
        <i class="bi bi-save2 me-1"></i> Cập nhật
      </button>
    </div>
  </div>

  {{-- Errors --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Vui lòng kiểm tra lại:</div>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form --}}
  <form id="user-edit-form" action="{{ route('admin.users.update', $user->id) }}" method="POST" novalidate>
    @csrf @method('PUT')

    <div class="card card-soft mb-3">
      <div class="card-header">
        <span class="section-title">Thông tin tài khoản</span>
      </div>
      <div class="card-body">
        <div class="grid-2">
          <div class="mb-3">
            <label for="name" class="form-label required">Họ và tên</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label required">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới</label>
            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">
            <div class="form-text">Để trống nếu không muốn thay đổi.</div>
          </div>
          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
          </div>
        </div>
      </div>
    </div>

    <div class="card card-soft">
      <div class="card-header">
        <span class="section-title">Thông tin hồ sơ</span>
      </div>
      <div class="card-body">
        <div class="grid-2">
          <div class="mb-3">
            <label for="dob" class="form-label">Ngày sinh</label>
            <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob', optional($user->profile)->dob) }}">
          </div>
          <div class="mb-3">
            <label for="gender" class="form-label">Giới tính</label>
            <select class="form-select" id="gender" name="gender">
              <option value="">Chọn giới tính</option>
              <option value="Nam"  {{ old('gender', optional($user->profile)->gender) == 'Nam'  ? 'selected' : '' }}>Nam</option>
              <option value="Nữ"   {{ old('gender', optional($user->profile)->gender) == 'Nữ'   ? 'selected' : '' }}>Nữ</option>
              <option value="Khác" {{ old('gender', optional($user->profile)->gender) == 'Khác' ? 'selected' : '' }}>Khác</option>
            </select>
          </div>
        </div>

        {{-- Hidden text names giữ song song với ID --}}
        <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', optional($user->profile)->province_name) }}">
        <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', optional($user->profile)->district_name) }}">
        <input type="hidden" name="ward_name"     id="ward_name"     value="{{ old('ward_name',     optional($user->profile)->ward_name) }}">

        <div class="grid-2">
          <div class="mb-3">
            <label for="province" class="form-label">Tỉnh/Thành phố</label>
            <select class="form-select" id="province" name="province_id"></select>
            <div class="hint">Tải danh sách từ API địa chỉ.</div>
          </div>
          <div class="mb-3">
            <label for="district" class="form-label">Quận/Huyện</label>
            <select class="form-select" id="district" name="district_id"></select>
          </div>
          <div class="mb-3">
            <label for="ward" class="form-label">Phường/Xã</label>
            <select class="form-select" id="ward" name="ward_code"></select>
          </div>
          <div class="mb-3">
            <label for="address" class="form-label">Địa chỉ chi tiết</label>
            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', optional($user->profile)->address) }}</textarea>
          </div>
        </div>
      </div>

      <div class="card-body sticky-actions">
        <div class="d-flex justify-content-end gap-2">
          <a href="{{ route('admin.users.index') }}" class="btn btn-light">Hủy</a>
          <button class="btn btn-primary"><i class="bi bi-save2 me-1"></i> Cập nhật người dùng</button>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const provinceSelect = document.getElementById('province');
  const districtSelect = document.getElementById('district');
  const wardSelect     = document.getElementById('ward');

  const provinceNameInput = document.getElementById('province_name');
  const districtNameInput = document.getElementById('district_name');
  const wardNameInput     = document.getElementById('ward_name');

  const savedAddress = {
    province: @json(old('province_name', optional($user->profile)->province_name)),
    district: @json(old('district_name', optional($user->profile)->district_name)),
    ward:     @json(old('ward_name',     optional($user->profile)->ward_name))
  };

  function setLoading(el, loading, placeholder){
    if(!el) return;
    el.classList.toggle('loading', !!loading);
    el.disabled = !!loading;
    if(placeholder){
      el.innerHTML = `<option value="">${placeholder}</option>`;
    }
  }

  async function fetchApi(url) {
    try {
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      if (!res.ok) return [];
      return await res.json();
    } catch (e) {
      console.error('Lỗi fetch API:', e);
      return [];
    }
  }

  function renderOptions(selectEl, data, placeholder, valueKey, textKey, selectedText = "") {
    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
    if (!Array.isArray(data)) return;

    let selectedValue = null;
    data.forEach(item => {
      const opt = new Option(item[textKey], item[valueKey]);
      if (selectedText && item[textKey] === selectedText) {
        opt.selected = true;
        selectedValue = item[valueKey];
      }
      selectEl.add(opt);
    });

    if (selectedValue) {
      selectEl.value = selectedValue;
      selectEl.dispatchEvent(new Event('change'));
    }
  }

  async function loadProvinces() {
    setLoading(provinceSelect, true, 'Đang tải Tỉnh/Thành…');
    const provinces = await fetchApi(@json(route('address.provinces')));
    renderOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành phố', 'ProvinceID', 'ProvinceName', savedAddress.province);
    setLoading(provinceSelect, false);
  }

  provinceSelect.addEventListener('change', async function(){
    provinceNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : "";
    // reset dưới
    setLoading(districtSelect, true, 'Đang tải Quận/Huyện…');
    renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
    wardNameInput.value = '';

    if (this.value) {
      const url = @json(route('address.districts')) + '?province_id=' + encodeURIComponent(this.value);
      const districts = await fetchApi(url);
      renderOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', savedAddress.district);
    } else {
      renderOptions(districtSelect, [], 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName');
    }
    setLoading(districtSelect, false);
  });

  districtSelect.addEventListener('change', async function(){
    districtNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : "";
    // reset dưới
    setLoading(wardSelect, true, 'Đang tải Phường/Xã…');
    wardNameInput.value = '';

    if (this.value) {
      const url = @json(route('address.wards')) + '?district_id=' + encodeURIComponent(this.value);
      const wards = await fetchApi(url);
      renderOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', savedAddress.ward);
    } else {
      renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
    }
    setLoading(wardSelect, false);
  });

  wardSelect.addEventListener('change', function(){
    wardNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : "";
  });

  // Kickstart
  loadProvinces();
});
</script>
@endpush
