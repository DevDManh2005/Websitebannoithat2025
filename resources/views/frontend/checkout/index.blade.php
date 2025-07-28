@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4">Thanh toán</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.placeOrder') }}" method="POST">
        @csrf
        {{-- Input ẩn để lưu phí ship và tên dịch vụ --}}
        <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">
        <input type="hidden" name="shipping_service_name" id="shipping_service_name_input" value="">

        <div class="row">
            {{-- Cột thông tin giao hàng --}}
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin giao hàng</h5>
                        <p class="text-muted">Vui lòng điền đầy đủ thông tin để nhận hàng.</p>
                        <hr>
                        <div class="mb-3">
                            <label for="receiver_name" class="form-label">Họ và tên người nhận</label>
                            <input type="text" class="form-control" id="receiver_name" name="receiver_name" value="{{ old('receiver_name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', optional($user->profile)->phone) }}" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="province" class="form-label">Tỉnh/Thành phố</label>
                                <select class="form-select" id="province" required></select>
                                <input type="hidden" name="city" id="province_name" value="{{ old('city', optional($user->profile)->province_name) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district" class="form-label">Quận/Huyện</label>
                                <select class="form-select" id="district" required></select>
                                <input type="hidden" name="district" id="district_name" value="{{ old('district', optional($user->profile)->district_name) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ward" class="form-label">Phường/Xã</label>
                                <select class="form-select" id="ward" required></select>
                                <input type="hidden" name="ward" id="ward_name" value="{{ old('ward', optional($user->profile)->ward_name) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ cụ thể (Số nhà, tên đường)</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required placeholder="Ví dụ: 123 Nguyễn Văn Linh">
                        </div>
                        
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Phương thức vận chuyển --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Phương thức vận chuyển</h5>
                        <hr>
                        <div id="shipping-options-container">
                            <p class="text-muted">Vui lòng điền đầy đủ thông tin địa chỉ để tính phí vận chuyển.</p>
                        </div>
                    </div>
                </div>

                {{-- Phương thức thanh toán --}}
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Phương thức thanh toán</h5>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="vnpay">
                            <label class="form-check-label" for="vnpay">Thanh toán qua VNPAY</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo">
                            <label class="form-check-label" for="momo">Thanh toán qua ví MoMo</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột tóm tắt đơn hàng --}}
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng của bạn</h5>
                        <hr>
                        @foreach($cartItems as $item)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ $item->variant->product->name }} x {{ $item->quantity }}</span>
                                @php
                                    $price = $item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price;
                                @endphp
                                <span>{{ number_format($price * $item->quantity) }} ₫</span>
                            </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Tạm tính</span>
                            <span id="subtotal-display">{{ number_format($totalPrice) }} ₫</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Phí vận chuyển</span>
                            <strong id="shipping-fee-display">0 ₫</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Tổng cộng</span>
                            <span id="total-price-display">{{ number_format($totalPrice) }} ₫</span>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">Đặt hàng</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // === CÁC BIẾN DOM ===
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const addressInput = document.getElementById('address');
    const provinceNameInput = document.getElementById('province_name');
    const districtNameInput = document.getElementById('district_name');
    const wardNameInput = document.getElementById('ward_name');
    const shippingOptionsContainer = document.getElementById('shipping-options-container');
    const shippingFeeDisplay = document.getElementById('shipping-fee-display');
    const totalPriceDisplay = document.getElementById('total-price-display');
    const subtotal = {{ $totalPrice }};
    const shippingFeeInput = document.getElementById('shipping_fee_input');
    const shippingServiceNameInput = document.getElementById('shipping_service_name_input');

    // === LOGIC TÍNH PHÍ VẬN CHUYỂN ===
    async function getShippingFee() {
        const province = provinceNameInput.value;
        const district = districtNameInput.value;
        const ward = wardNameInput.value;
        const address = addressInput.value;

        if (!province || !district || !ward || !address) {
            resetShipping();
            return;
        }

        shippingOptionsContainer.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

        try {
            const response = await fetch('{{ route("shipping.getFee") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ province, district, ward, address })
            });

            if (!response.ok) throw new Error('Network response was not ok.');
            
            const result = await response.json();

            if (result.fee !== undefined) {
                const fee = result.fee;
                const serviceName = result.name;
                shippingOptionsContainer.innerHTML = `<p><strong>${serviceName}:</strong> ${new Intl.NumberFormat('vi-VN').format(fee)} ₫</p>`;
                updateTotal(fee, serviceName);
            } else {
                shippingOptionsContainer.innerHTML = `<p class="text-danger">${result.error || 'Không thể tính phí vận chuyển cho địa chỉ này.'}</p>`;
                resetShipping();
            }
        } catch (error) {
            console.error('Error fetching shipping fee:', error);
            shippingOptionsContainer.innerHTML = '<p class="text-danger">Đã có lỗi xảy ra khi tính phí vận chuyển.</p>';
            resetShipping();
        }
    }

    function updateTotal(shippingFee = 0, serviceName = '') {
        const total = subtotal + shippingFee;
        shippingFeeDisplay.textContent = `${new Intl.NumberFormat('vi-VN').format(shippingFee)} ₫`;
        totalPriceDisplay.textContent = `${new Intl.NumberFormat('vi-VN').format(total)} ₫`;
        shippingFeeInput.value = shippingFee;
        shippingServiceNameInput.value = serviceName;
    }

    function resetShipping() {
        shippingOptionsContainer.innerHTML = '<p class="text-muted">Vui lòng điền đầy đủ thông tin địa chỉ để tính phí vận chuyển.</p>';
        updateTotal(0, '');
    }

    // === LOGIC CHỌN ĐỊA CHỈ (Sử dụng API của GHTK) ===
    const oldProvince = provinceNameInput.value;
    const oldDistrict = districtNameInput.value;
    const oldWard = wardNameInput.value;

    async function fetchAddressData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            // API công cộng của GHTK trả về mảng trực tiếp, không có key 'data'
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch address data:', error); 
            return [];
        }
    }

    function renderAddressOptions(selectElement, data, placeholder, valueKey, textKey, selectedValue = "") {
        selectElement.innerHTML = `<option value="">${placeholder}</option>`;
        if (!Array.isArray(data)) return;

        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = item[textKey];
            if (item[textKey] === selectedValue) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });
        if (selectedValue) {
             selectElement.dispatchEvent(new Event('change'));
        }
    }

    async function loadProvinces() {
        const provinces = await fetchAddressData('{{ route("address.provinces") }}');
        renderAddressOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành phố', 'id', 'name', oldProvince);
    }

    provinceSelect.addEventListener('change', async function() {
        provinceNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        resetShipping();
        if (this.value) {
            const districts = await fetchAddressData(`{{ route("address.districts") }}?province_id=${this.value}`);
            renderAddressOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'id', 'name', oldDistrict);
        }
    });

    districtSelect.addEventListener('change', async function() {
        districtNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        resetShipping();
        if (this.value) {
            const wards = await fetchAddressData(`{{ route("address.wards") }}?district_id=${this.value}`);
            renderAddressOptions(wardSelect, wards, 'Chọn Phường/Xã', 'id', 'name', oldWard);
        }
    });

    wardSelect.addEventListener('change', function() {
        wardNameInput.value = this.value ? this.options[this.selectedIndex].text : "";
        getShippingFee();
    });
    
    addressInput.addEventListener('blur', getShippingFee);

    loadProvinces();
});
</script>
@endsection
