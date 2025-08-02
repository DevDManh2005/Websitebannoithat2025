@extends('layouts.app')

@section('title', 'Thanh toán')

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
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('checkout.placeOrder') }}" method="POST" id="checkout-form">
        @csrf
        <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">

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
                                <label for="province" class="form-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                <input type="hidden" name="city" id="province_name_input" value="{{ old('city', optional($user->profile)->province_name) }}">
                                <select class="form-select" id="province" required></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                <input type="hidden" name="district" id="district_name_input" value="{{ old('district', optional($user->profile)->district_name) }}">
                                <input type="hidden" name="district_id" id="district_id_input" value="{{ old('district_id') }}">
                                <select class="form-select" id="district" required></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ward" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                <input type="hidden" name="ward" id="ward_name_input" value="{{ old('ward', optional($user->profile)->ward_name) }}">
                                <input type="hidden" name="ward_code" id="ward_code_input" value="{{ old('ward_code') }}">
                                <select class="form-select" id="ward" required></select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ cụ thể (Số nhà, tên đường) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required placeholder="Ví dụ: 123 Nguyễn Văn Linh">
                        </div>
                        
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Phương thức vận chuyển và thanh toán --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Phương thức vận chuyển</h5>
                        <hr>
                        <div id="shipping-options-container">
                            <p class="text-muted">Vui lòng điền đầy đủ thông tin địa chỉ để tính phí vận chuyển.</p>
                        </div>
                    </div>
                </div>
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
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng của bạn</h5>
                        <hr>
                        @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span>{{ $item->variant->product->name }} x {{ $item->quantity }}</span>
                            <span>{{ number_format(($item->variant->sale_price > 0 ? $item->variant->sale_price : $item->variant->price) * $item->quantity) }} ₫</span>
                        </div>
                        @endforeach
                        <hr>
                        
                        <div id="voucher-section">
                            @if($voucherCode)
                                <div id="applied-voucher-info" class="d-flex justify-content-between align-items-center text-success">
                                    <span>Mã đã áp dụng: <strong>{{ $voucherCode }}</strong></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="remove-voucher-btn">Gỡ</button>
                                </div>
                            @else
                                <div id="voucher-form">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Nhập mã giảm giá" id="voucher-code-input">
                                        <button class="btn btn-primary" type="button" id="apply-voucher-btn">Áp dụng</button>
                                    </div>
                                </div>
                                <div id="applied-voucher-info" class="d-flex justify-content-between align-items-center text-success d-none">
                                    <span>Mã đã áp dụng: <strong id="applied-voucher-code"></strong></span>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="remove-voucher-btn">Gỡ</button>
                                </div>
                            @endif
                        </div>
                        <div id="voucher-message" class="mt-2 small"></div>
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <span>Tạm tính</span>
                            <span id="subtotal-display" data-subtotal="{{ $subtotal }}">{{ number_format($subtotal) }} ₫</span>
                        </div>
                        <div id="discount-row" class="d-flex justify-content-between text-success {{ $discount > 0 ? '' : 'd-none' }}">
                            <span>Giảm giá</span>
                            <span id="discount-display">-{{ number_format($discount) }} ₫</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Phí vận chuyển</span>
                            <strong id="shipping-fee-display">0 ₫</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Tổng cộng</span>
                            <span id="total-price-display">{{ number_format($subtotal - $discount) }} ₫</span>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary btn-lg" id="place-order-btn">Đặt hàng</button>
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
    const placeOrderBtn = document.getElementById('place-order-btn');
    const provinceNameInput = document.getElementById('province_name_input');
    const districtNameInput = document.getElementById('district_name_input');
    const districtIdInput = document.getElementById('district_id_input');
    const wardNameInput = document.getElementById('ward_name_input');
    const wardCodeInput = document.getElementById('ward_code_input');
    const shippingOptionsContainer = document.getElementById('shipping-options-container');
    const shippingFeeDisplay = document.getElementById('shipping-fee-display');
    const totalPriceDisplay = document.getElementById('total-price-display');
    const subtotalDisplay = document.getElementById('subtotal-display');
    const subtotal = parseFloat(subtotalDisplay.dataset.subtotal);
    const shippingFeeInput = document.getElementById('shipping_fee_input');
    const applyBtn = document.getElementById('apply-voucher-btn');
    const removeBtn = document.getElementById('remove-voucher-btn');
    const voucherInput = document.getElementById('voucher-code-input');
    const voucherMsg = document.getElementById('voucher-message');
    let currentDiscount = {{ $discount }};

    const savedAddress = {
        province: "{{ old('city', optional($user->profile)->province_name) }}",
        district: "{{ old('district', optional($user->profile)->district_name) }}",
        ward: "{{ old('ward', optional($user->profile)->ward_name) }}"
    };

    // === CÁC HÀM TIỆN ÍCH ===
    const formatCurrency = num => new Intl.NumberFormat('vi-VN').format(num) + ' ₫';
    
    // Hàm debounce để tránh gọi API liên tục
    function debounce(func, delay = 300) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }

    function updateFinalTotals() {
        const shippingFee = parseFloat(shippingFeeInput.value) || 0;
        const total = (subtotal - currentDiscount) + shippingFee;
        shippingFeeDisplay.textContent = formatCurrency(shippingFee);
        totalPriceDisplay.textContent = formatCurrency(total > 0 ? total : 0);
        const discountRow = document.getElementById('discount-row');
        if (currentDiscount > 0) {
            document.getElementById('discount-display').textContent = `-${formatCurrency(currentDiscount)}`;
            discountRow.classList.remove('d-none');
        } else {
            discountRow.classList.add('d-none');
        }
    }

    function resetShipping() {
        shippingOptionsContainer.innerHTML = '<p class="text-muted">Vui lòng điền đầy đủ thông tin địa chỉ để tính phí vận chuyển.</p>';
        shippingFeeInput.value = 0;
        updateFinalTotals();
    }

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

    // === LOGIC TÍNH PHÍ VẬN CHUYỂN ===
    const getShippingFee = debounce(async () => {
        if (!districtSelect.value || !wardSelect.value) {
            resetShipping();
            return;
        }
        shippingOptionsContainer.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';
        placeOrderBtn.disabled = true;
        try {
            const response = await fetch('{{ route("shipping.getFee") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({
                    to_district_id: districtSelect.value,
                    to_ward_code: wardSelect.value,
                })
            });
            const result = await response.json();

            if (result.success && result.data && typeof result.data.total !== 'undefined') {
                const fee = result.data.total;
                const serviceName = result.data.name || "Giao hàng nhanh";
                shippingOptionsContainer.innerHTML = `<div class="form-check"><input class="form-check-input" type="radio" name="shipping_option" checked><label class="form-check-label"><strong>${serviceName}:</strong> ${formatCurrency(fee)}</label></div>`;
                shippingFeeInput.value = fee;
            } else {
                shippingOptionsContainer.innerHTML = `<p class="text-danger">${result.message || 'Không thể tính phí.'}</p>`;
                shippingFeeInput.value = 0;
            }
        } catch (error) {
            console.error('Lỗi tính phí:', error);
            shippingOptionsContainer.innerHTML = '<p class="text-danger">Lỗi kết nối khi tính phí vận chuyển.</p>';
            shippingFeeInput.value = 0;
        } finally {
             placeOrderBtn.disabled = false;
             updateFinalTotals();
        }
    });

    // === LOGIC CHỌN ĐỊA CHỈ ===
    async function loadProvinces() {
        const provinces = await fetchApi('{{ route("address.provinces") }}');
        renderOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành phố', 'ProvinceID', 'ProvinceName', savedAddress.province);
    }

    provinceSelect.addEventListener('change', async function() {
        provinceNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
        renderOptions(districtSelect, [], 'Vui lòng chờ...', 'DistrictID', 'DistrictName');
        renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
        resetShipping();
        if (this.value) {
            const districts = await fetchApi(`{{ route("address.districts") }}?province_id=${this.value}`);
            renderOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', savedAddress.district);
        }
    });

    districtSelect.addEventListener('change', async function() {
        districtNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
        districtIdInput.value = this.value;
        renderOptions(wardSelect, [], 'Vui lòng chờ...', 'WardCode', 'WardName');
        resetShipping();
        if (this.value) {
            const wards = await fetchApi(`{{ route("address.wards") }}?district_id=${this.value}`);
            renderOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', savedAddress.ward);
        }
    });

    wardSelect.addEventListener('change', function() {
        wardNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
        wardCodeInput.value = this.value;
        getShippingFee();
    });
    
    // === LOGIC VOUCHER ===
    if (applyBtn) {
        applyBtn.addEventListener('click', async function() {
            const code = voucherInput.value.trim();
            if (!code) return;
            this.disabled = true; this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            const response = await fetch('{{ route("voucher.apply") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ code: code })
            });
            const result = await response.json();
            voucherMsg.className = result.success ? 'text-success small' : 'text-danger small';
            voucherMsg.textContent = result.message;
            if (result.success) {
                currentDiscount = result.discount;
                document.getElementById('voucher-form').classList.add('d-none');
                document.getElementById('applied-voucher-code').textContent = code;
                document.getElementById('applied-voucher-info').classList.remove('d-none');
                updateFinalTotals();
            }
            this.disabled = false; this.innerHTML = 'Áp dụng';
        });
    }

    if(removeBtn) {
        removeBtn.addEventListener('click', async function() {
             const response = await fetch('{{ route("voucher.remove") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            });
            const result = await response.json();
            if (result.success) {
                currentDiscount = 0;
                voucherInput.value = '';
                voucherMsg.textContent = '';
                document.getElementById('voucher-form').classList.remove('d-none');
                document.getElementById('applied-voucher-info').classList.add('d-none');
                updateFinalTotals();
            }
        });
    }

    // === KHỞI TẠO ===
    loadProvinces();
    updateFinalTotals();
});
</script>
@endsection