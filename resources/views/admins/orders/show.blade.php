@extends('admins.layouts.app')

@section('title', 'Chi tiết Đơn hàng #' . $order->order_code)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết Đơn hàng #{{ $order->order_code }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {{-- CARD SẢN PHẨM --}}
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Sản phẩm trong đơn hàng</h5></div>
                <div class="card-body">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <strong>{{ $item->variant->product->name }}</strong>
                                <br>
                                <small class="text-muted">
                                    @forelse((array)$item->variant->attributes as $key => $value)
                                        {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                    @empty
                                        Sản phẩm gốc
                                    @endforelse
                                </small>
                            </div>
                            <div class="text-end">
                                {{ number_format($item->price) }} ₫ x {{ $item->quantity }}
                                <br>
                                <strong>{{ number_format($item->subtotal) }} ₫</strong>
                            </div>
                        </div>
                        @if(!$loop->last) <hr> @endif
                    @endforeach
                </div>
            </div>
            
            {{-- CARD TỔNG CỘNG --}}
            <div class="card shadow mb-4">
                <div class="card-header">Tổng cộng Đơn hàng</div>
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

            {{-- CARD THÔNG TIN GIAO HÀNG --}}
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Thông tin giao hàng</h5></div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.updateShippingInfo', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3"><label class="form-label">Người nhận</label><input type="text" name="receiver_name" class="form-control" value="{{ optional($order->shipment)->receiver_name }}"></div>
                        <div class="mb-3"><label class="form-label">Điện thoại</label><input type="text" name="phone" class="form-control" value="{{ optional($order->shipment)->phone }}"></div>
                        
                        <input type="hidden" name="city" id="province_name_input" value="{{ optional($order->shipment)->city }}">
                        <input type="hidden" name="district" id="district_name_input" value="{{ optional($order->shipment)->district }}">
                        <input type="hidden" name="ward" id="ward_name_input" value="{{ optional($order->shipment)->ward }}">

                        <div class="row">
                            <div class="col-md-4 mb-3"><label for="province" class="form-label">Tỉnh/Thành phố</label><select class="form-select" id="province"></select></div>
                            <div class="col-md-4 mb-3"><label for="district" class="form-label">Quận/Huyện</label><select class="form-select" id="district" name="district_id"></select></div>
                            <div class="col-md-4 mb-3"><label for="ward" class="form-label">Phường/Xã</label><select class="form-select" id="ward" name="ward_code"></select></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Địa chỉ chi tiết</label><input type="text" name="address" class="form-control" value="{{ optional($order->shipment)->address }}"></div>
                        <button type="submit" class="btn btn-warning">Cập nhật thông tin giao hàng</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- CARD THÔNG TIN KHÁCH HÀNG --}}
            <div class="card shadow mb-4">
                <div class="card-header">Thông tin Khách hàng</div>
                <div class="card-body">
                    <p><strong>Tên:</strong> {{ $order->user->name }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    @if(optional($order->shipment)->tracking_code)
                        <p><strong>Mã vận đơn GHN:</strong> <strong class="text-primary">{{ $order->shipment->tracking_code }}</strong></p>
                    @endif
                </div>
            </div>
            
            {{-- CARD HÀNH ĐỘNG --}}
            <div class="card shadow mb-4">
                <div class="card-header">Hành động</div>
                <div class="card-body">
                    @if(in_array($order->status, ['cancelled', 'received']))
                        <div class="alert alert-info">
                            Đơn hàng đã ở trạng thái cuối cùng ({{ $order->status == 'cancelled' ? 'Đã hủy' : 'Khách đã nhận' }}) và không thể thay đổi.
                        </div>
                    @else
                        <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <label for="order_status" class="form-label">Cập nhật trạng thái:</label>
                            <div class="input-group">
                                <select name="status" id="order_status" class="form-select">
                                    <option value="pending" @selected($order->status == 'pending')>Đang chờ</option>
                                    <option value="processing" @selected($order->status == 'processing')>Đang xử lý</option>
                                    <option value="shipped_to_shipper" @selected($order->status == 'shipped_to_shipper')>Đã giao cho shipper</option>
                                    <option value="shipping" @selected($order->status == 'shipping')>Đang giao</option>
                                    <option value="delivered" @selected($order->status == 'delivered')>Đã giao</option>
                                    <option value="cancelled" @selected($order->status == 'cancelled')>Đã hủy</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>
                        </form>
                        <hr>
                        @if(!$order->shipment->tracking_code && in_array($order->status, ['pending', 'processing']))
                            <form action="{{ route('admin.orders.create-ghn', $order) }}" method="POST" class="d-grid mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success">Gửi đơn hàng qua GHN</button>
                            </form>
                        @endif
                        @if($order->shipment->tracking_code && in_array($order->status, ['shipped_to_shipper', 'shipping']))
                            <form action="{{ route('admin.orders.cancel-ghn', $order) }}" method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này trên GHN?');">Hủy đơn GHN</button>
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
    const wardSelect = document.getElementById('ward');
    
    const provinceNameInput = document.getElementById('province_name_input');
    const districtNameInput = document.getElementById('district_name_input');
    const wardNameInput = document.getElementById('ward_name_input');
    
    const savedAddress = {
        province: "{{ optional($order->shipment)->city }}",
        district: "{{ optional($order->shipment)->district }}",
        ward: "{{ optional($order->shipment)->ward }}"
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