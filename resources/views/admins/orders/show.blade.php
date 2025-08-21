@extends('admins.layouts.app')

@section('title', 'Chi tiết Đơn hàng #' . $order->order_code)

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
  .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
  .badge.bg-danger-soft{ background:#fde7e7; color:#992f2f }
  .badge.bg-info-soft{ background:#e6f1ff; color:#0b4a8b }
  .badge.bg-primary-soft{ background:#e8ebff; color:#2a3cff }
  .badge.bg-warning-soft{ background:#fff4d6; color:#8b6b00 }
</style>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Chi tiết Đơn hàng #{{ $order->order_code }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  @if(session('success')) <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div> @endif

  <div class="row g-3">
    <div class="col-lg-8">
      {{-- SẢN PHẨM --}}
      <div class="card card-soft">
        <div class="card-header"><h5 class="card-title mb-0">Sản phẩm trong đơn</h5></div>
        <div class="card-body">
          @forelse($order->items as $item)
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="me-3">
                <strong>{{ $item->variant->product->name }}</strong><br>
                <small class="text-muted">
                  @forelse((array)$item->variant->attributes as $key => $value)
                    {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                  @empty
                    Sản phẩm gốc
                  @endforelse
                </small>
              </div>
              <div class="text-end">
                {{ number_format($item->price) }} ₫ x {{ $item->quantity }}<br>
                <strong>{{ number_format($item->subtotal) }} ₫</strong>
              </div>
            </div>
            @if(!$loop->last) <hr class="my-2"> @endif
          @empty
            <div class="text-muted">Không có sản phẩm.</div>
          @endforelse
        </div>
      </div>

      {{-- TỔNG CỘNG --}}
      <div class="card card-soft mt-3">
        <div class="card-header"><h5 class="card-title mb-0">Tổng cộng đơn hàng</h5></div>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <span>Tạm tính:</span>
            <span>{{ number_format($order->total_amount) }} ₫</span>
          </div>
          @if($order->discount > 0)
            <div class="d-flex justify-content-between text-success">
              <span>Giảm giá (Voucher):</span>
              <span>-{{ number_format($order->discount) }} ₫</span>
            </div>
          @endif
          <div class="d-flex justify-content-between">
            <span>Phí vận chuyển:</span>
            <span>{{ number_format(optional($order->shipment)->shipping_fee ?? 0) }} ₫</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between fw-bold fs-5">
            <span>Thành tiền:</span>
            <span class="text-danger">{{ number_format($order->final_amount) }} ₫</span>
          </div>
        </div>
      </div>

      {{-- THÔNG TIN GIAO HÀNG --}}
      <div class="card card-soft mt-3">
        <div class="card-header"><h5 class="card-title mb-0">Thông tin giao hàng</h5></div>
        <div class="card-body">
          <form action="{{ route('admin.orders.updateShippingInfo', $order) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="mb-3">
              <label class="form-label">Người nhận</label>
              <input type="text" name="receiver_name" class="form-control" value="{{ optional($order->shipment)->receiver_name }}">
            </div>
            <div class="mb-3">
              <label class="form-label">Điện thoại</label>
              <input type="text" name="phone" class="form-control" value="{{ optional($order->shipment)->phone }}">
            </div>

            {{-- text Province/District/Ward (để Controller nhận tên) --}}
            <input type="hidden" name="city" id="province_name_input"   value="{{ optional($order->shipment)->city }}">
            <input type="hidden" name="district" id="district_name_input" value="{{ optional($order->shipment)->district }}">
            <input type="hidden" name="ward" id="ward_name_input"         value="{{ optional($order->shipment)->ward }}">

            <div class="row">
              <div class="col-md-4 mb-3">
                <label class="form-label">Tỉnh/Thành phố</label>
                <select class="form-select" id="province"></select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Quận/Huyện</label>
                <select class="form-select" id="district" name="district_id"></select>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Phường/Xã</label>
                <select class="form-select" id="ward" name="ward_code"></select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Địa chỉ chi tiết</label>
              <input type="text" name="address" class="form-control" value="{{ optional($order->shipment)->address }}">
            </div>

            <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Cập nhật giao hàng</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      {{-- KHÁCH HÀNG --}}
      <div class="card card-soft">
        <div class="card-header"><h5 class="card-title mb-0">Thông tin Khách hàng</h5></div>
        <div class="card-body">
          <p class="mb-1"><strong>Tên:</strong> {{ $order->user->name }}</p>
          <p class="mb-1"><strong>Email:</strong> {{ $order->user->email }}</p>
          @if(optional($order->shipment)->tracking_code)
            <p class="mb-0"><strong>Mã vận đơn:</strong> <span class="text-primary fw-bold">{{ $order->shipment->tracking_code }}</span></p>
          @endif
        </div>
      </div>

      {{-- HÀNH ĐỘNG --}}
      <div class="card card-soft mt-3">
        <div class="card-header"><h5 class="card-title mb-0">Hành động</h5></div>
        <div class="card-body">
          @if(in_array($order->status, ['cancelled', 'received']))
            <div class="alert alert-info mb-0">
              Đơn hàng đã ở trạng thái cuối ({{ $order->status == 'cancelled' ? 'Đã hủy' : 'Khách đã nhận' }}) và không thể thay đổi.
            </div>
          @else
            {{-- Cập nhật trạng thái (không có "received") --}}
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mb-3">
              @csrf @method('PATCH')
              <label for="order_status" class="form-label">Cập nhật trạng thái:</label>
              <div class="input-group">
                <select name="status" id="order_status" class="form-select">
                  <option value="pending"    @selected($order->status == 'pending')>Đang chờ</option>
                  <option value="processing" @selected($order->status == 'processing')>Đang xử lý</option>
                  <option value="shipping"   @selected($order->status == 'shipping')>Đang giao</option>
                  <option value="delivered"  @selected($order->status == 'delivered')>Đã giao</option>
                  <option value="cancelled"  @selected($order->status == 'cancelled')>Đã hủy</option>
                </select>
                <button type="submit" class="btn btn-primary">Cập nhật</button>
              </div>
            </form>

            {{-- Nút Đã thu COD --}}
            @php
              $isCod  = ($order->payment_method ?? 'cod') === 'cod';
              $isPaid = (($order->payment_status ?? 'unpaid') === 'paid') || ($order->is_paid ?? false);
            @endphp
            @if($isCod && !$isPaid)
              <form action="{{ route('admin.orders.cod-paid', $order) }}" method="POST" class="d-grid mb-3">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline-success">Đã thu COD</button>
              </form>
            @endif

            <hr>

            {{-- Nội bộ: ready to ship --}}
            @if(!optional($order->shipment)->tracking_code && in_array($order->status, ['pending','processing']))
              <form action="{{ route('admin.orders.ready-to-ship', $order) }}" method="POST" class="d-grid">
                @csrf
                <button type="submit" class="btn btn-success">Sẵn sàng giao (nội bộ)</button>
              </form>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const provinceSelect = document.getElementById('province');
  const districtSelect = document.getElementById('district');
  const wardSelect     = document.getElementById('ward');

  const provinceNameInput = document.getElementById('province_name_input');
  const districtNameInput = document.getElementById('district_name_input');
  const wardNameInput     = document.getElementById('ward_name_input');

  const savedAddress = {
    province: "{{ optional($order->shipment)->city }}",
    district: "{{ optional($order->shipment)->district }}",
    ward:     "{{ optional($order->shipment)->ward }}"
  };

  async function fetchApi(url) {
    try {
      const res = await fetch(url);
      if (!res.ok) return [];
      return await res.json();
    } catch { return []; }
  }

  function renderOptions(selectEl, data, placeholder, valueKey, textKey, selectedText = "") {
    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
    if (!Array.isArray(data)) return;

    let selectedValue = null;
    data.forEach(item => {
      const opt = new Option(item[textKey], item[valueKey]);
      if (item[textKey] === selectedText) {
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
    const provinces = await fetchApi('{{ route("address.provinces") }}');
    renderOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành phố', 'ProvinceID', 'ProvinceName', savedAddress.province);
  }

  provinceSelect.addEventListener('change', async function() {
    provinceNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
    renderOptions(districtSelect, [], 'Vui lòng chờ...', 'DistrictID', 'DistrictName');
    renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
    if (this.value) {
      const districts = await fetchApi(`{{ route("address.districts") }}?province_id=${this.value}`);
      renderOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', savedAddress.district);
    }
  });

  districtSelect.addEventListener('change', async function() {
    districtNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
    renderOptions(wardSelect, [], 'Vui lòng chờ...', 'WardCode', 'WardName');
    if (this.value) {
      const wards = await fetchApi(`{{ route("address.wards") }}?district_id=${this.value}`);
      renderOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', savedAddress.ward);
    }
  });

  wardSelect.addEventListener('change', function() {
    wardNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
  });

  loadProvinces();
});
</script>
@endpush
