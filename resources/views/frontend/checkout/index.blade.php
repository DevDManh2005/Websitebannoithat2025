@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
    {{-- =================== HERO / BREADCRUMB =================== --}}
    <section class="checkout-hero position-relative overflow-hidden mb-5">
        <img src="https://anphonghouse.com/wp-content/uploads/2018/06/hinh-nen-noi-that-dep-full-hd-so-43-0.jpg" alt="Checkout Banner" class="hero-bg">
        <div class="hero-overlay"></div>
        <div class="container position-relative" data-aos="fade-down">
            <div class="row align-items-center" style="min-height: 220px;">
                <div class="col-12 text-center">
                    <h1 class="fw-bold text-white mb-2">Thanh Toán Đơn Hàng</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="hero-bc-link" href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a class="hero-bc-link" href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">Thanh toán</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="hero-bottom wave-sep"></div>
    </section>

    <div class="container my-5">
        {{-- Alerts --}}
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm" data-aos="fade-up">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger shadow-sm" data-aos="fade-up">{{ session('error') }}</div>
        @endif

        <form action="{{ route('checkout.placeOrder') }}" method="POST" id="checkout-form">
            @csrf
            <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="0">

            <div class="row g-4 g-lg-5">
                {{-- =================== LEFT: INFO =================== --}}
                <div class="col-lg-7">
                    {{-- Shipping address --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-right">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-3">
                                <span class="step-badge me-2">1</span>
                                <h4 class="card-title fw-bold mb-0">Địa chỉ giao hàng</h4>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="receiver_name" class="form-label">Họ và tên người nhận</label>
                                    <input type="text" class="form-control form-control-modern" name="receiver_name" value="{{ old('receiver_name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control form-control-modern" name="phone" value="{{ old('phone', optional($user->profile)->phone) }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label for="province_id" class="form-label">Tỉnh/Thành <span class="text-danger">*</span></label>
                                    <input type="hidden" name="city" id="province_name_input" value="{{ old('city', optional($user->profile)->province_name) }}">
                                    <select class="form-select form-control-modern" id="province_id" name="province_id" required></select>
                                </div>
                                <div class="col-md-4">
                                    <label for="district_id" class="form-label">Quận/Huyện <span class="text-danger">*</span></label>
                                    <input type="hidden" name="district" id="district_name_input" value="{{ old('district', optional($user->profile)->district_name) }}">
                                    <select class="form-select form-control-modern" id="district_id" name="district_id" required></select>
                                </div>
                                <div class="col-md-4">
                                    <label for="ward_code" class="form-label">Phường/Xã <span class="text-danger">*</span></label>
                                    <input type="hidden" name="ward" id="ward_name_input" value="{{ old('ward', optional($user->profile)->ward_name) }}">
                                    <select class="form-select form-control-modern" id="ward_code" name="ward_code" required></select>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Địa chỉ cụ thể (số nhà, tên đường...)</label>
                                    <input type="text" class="form-control form-control-modern" id="address" name="address" value="{{ old('address', optional($user->profile)->address) }}" required placeholder="Ví dụ: 123 Nguyễn Văn Linh">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Payment & shipping --}}
                    <div class="card border-0 shadow-sm rounded-4" data-aos="fade-right" data-aos-delay="100">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-3">
                                <span class="step-badge me-2">2</span>
                                <h4 class="card-title fw-bold mb-0">Thanh toán & Vận chuyển</h4>
                            </div>

                            <div class="mb-4">
                                <h6 class="mb-2 fw-semibold">Phương thức vận chuyển</h6>
                                <div id="shipping-options-container" class="text-muted p-3 bg-light rounded-3 small">
                                    Vui lòng điền đủ địa chỉ để tính phí.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="note" class="form-label">Ghi chú đơn hàng (tuỳ chọn)</label>
                                <textarea class="form-control form-control-modern" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                            </div>

                            <div>
                                <h6 class="mb-2 fw-semibold">Phương thức thanh toán</h6>
                                <div class="border rounded-3 p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                        <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                                    </div>
                                    <hr class="my-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="vnpay">
                                        <label class="form-check-label" for="vnpay">Thanh toán qua VNPAY</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- =================== RIGHT: SUMMARY =================== --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 sticky-order" data-aos="fade-left" data-aos-delay="150">

                        <div class="card-body p-4 p-md-5">
                            <h4 class="card-title fw-bold mb-3">Tổng quan đơn hàng</h4>

                            <div class="order-summary-items mb-3">
                                @foreach($cartItems as $item)
                                    <div class="d-flex align-items-center mb-3 summary-item">
                                        <div class="position-relative me-3">
                                            <img src="{{ optional($item->variant->product->primaryImage)->image_url_path ?? 'https://placehold.co/100x100' }}"
                                                 alt="{{ $item->variant->product->name }}" class="rounded-3"
                                                 style="width: 64px; height: 64px; object-fit: cover;">
                                            <span class="badge bg-dark rounded-pill position-absolute top-0 start-100 translate-middle">{{ $item->quantity }}</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0 small fw-semibold text-truncate-2">{{ $item->variant->product->name }}</p>
                                            @if($item->variant?->attributes)
                                                <div class="text-muted xsmall mt-1">
                                                    @foreach($item->variant->attributes as $k => $v)
                                                        <span class="me-2">{{ ucfirst($k) }}: <strong>{{ $v }}</strong></span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-end ms-2">
                                            <span class="text-muted small">
                                                {{ number_format(($item->variant->sale_price ?: $item->variant->price) * $item->quantity) }} ₫
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr>

                            {{-- Voucher --}}
                            <div id="voucher-section" class="mb-3">
                                @if(session('voucher'))
                                    @php $voucher = session('voucher'); @endphp
                                    <div id="applied-voucher-info" class="d-flex justify-content-between align-items-center text-success">
                                        <span>Mã đã áp dụng: <strong>{{ $voucher['code'] }}</strong></span>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" id="remove-voucher-btn">Gỡ</button>
                                    </div>
                                @else
                                    <div id="voucher-form">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-modern" placeholder="Nhập mã giảm giá" id="voucher-code-input">
                                            <button class="btn btn-primary" type="button" id="apply-voucher-btn">Áp dụng</button>
                                        </div>
                                    </div>
                                    <div id="applied-voucher-info" class="d-none justify-content-between align-items-center text-success small mt-2">
                                        <span>Mã đã áp dụng: <strong id="applied-voucher-code"></strong></span>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" id="remove-voucher-btn-js">Gỡ</button>
                                    </div>
                                @endif
                            </div>
                            <div id="voucher-message" class="mt-2 small"></div>

                            <hr>

                            {{-- Totals --}}
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính</span>
                                <span id="subtotal-display" data-value="{{ $subtotal }}">{{ number_format($subtotal) }} ₫</span>
                            </div>
                            <div id="discount-row" class="d-flex justify-content-between text-success mb-2 {{ $discount > 0 ? '' : 'd-none' }}">
                                <span>Giảm giá</span>
                                <span id="discount-display">-{{ number_format($discount) }} ₫</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Phí vận chuyển</span>
                                <strong id="shipping-fee-display">Chọn địa chỉ</strong>
                            </div>

                            <hr class="border-dashed">

                            <div class="d-flex justify-content-between align-items-center fw-bold fs-5">
                                <span>Tổng cộng</span>
                                <span class="text-danger" id="total-price-display">{{ number_format($total) }} ₫</span>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill" id="place-order-btn">
                                    <i class="bi bi-shield-check me-2"></i>Đặt hàng ngay
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- /RIGHT --}}
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
/* ===== HERO (sáng, overlay) ===== */
.checkout-hero{ background:#fff; }
.checkout-hero .hero-bg{
    position:absolute; inset:0; width:100%; height:100%; object-fit:cover; transform:scale(1.03);
    filter: brightness(0.7);
}
.checkout-hero .hero-overlay{ position:absolute; inset:0; background:linear-gradient(180deg, rgba(0,0,0,.35), rgba(0,0,0,.35)); }
.wave-sep{
    position:absolute; left:0; right:0; bottom:-1px; height:28px;
    background: radial-gradient(36px 11px at 50% 0, #fff 98%, transparent 100%) repeat-x;
    background-size:36px 18px;
}
.hero-bc-link{ color:#f8f9fa; text-decoration:none; }
.hero-bc-link:hover{ text-decoration:underline; }

/* ===== Modern inputs ===== */
.form-control-modern, .form-select.form-control-modern{
    border-radius: .8rem;
    border: 1px solid #e9ecef;
    background: #fff;
}
.form-control-modern:focus, .form-select.form-control-modern:focus{
    border-color:#A20E38;
    box-shadow: 0 0 0 .2rem rgba(162,14,56,.15);
}

/* ===== Card tweaks ===== */
.rounded-4{ border-radius:1rem !important; }
.card-title{ color:#1c1f23; }
.step-badge{
    display:inline-flex; width:28px; height:28px; border-radius:50%;
    align-items:center; justify-content:center;
    background:#A20E38; color:#fff; font-weight:700; font-size:.9rem;
}

/* ===== Order summary ===== */
.order-summary-items{ max-height: 230px; overflow-y:auto; }
.summary-item{ transition: background .2s ease; border-radius: .8rem; padding:.25rem; }
.summary-item:hover{ background: #f8f9fa; }
.text-truncate-2{
    display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}

/* ===== Dashed hr ===== */
.border-dashed{ border-top:1px dashed #dee2e6; }

/* ===== Buttons ===== */
.btn-primary{
    background-color:#A20E38; border-color:#A20E38;
}
.btn-primary:hover{ background-color:#8b0c30; border-color:#8b0c30; }

@media (max-width: 575.98px){
    .checkout-hero h1{ font-size:1.5rem; }
}

.sticky-order{
  position: sticky;
  top: var(--sticky-offset, 88px);
  z-index: 1;                  
}
.container, .row { overflow: visible; }

</style>
@endpush

@push('scripts-page')
<script>
// ===== Tính khoảng cách sticky theo chiều cao header =====
(function(){
  function updateStickyOffset(){
    // Header trang chủ có header-home; các trang trong có header-internal
    const header = document.querySelector('header.sticky-top, header.navbar-transparent, header'); 
    // nếu là home, header có thể cao hơn do logo lớn
    const headerHeight = header ? header.offsetHeight : 72;
    // chừa thêm 12–16px cho thoáng
    const gap = 16;
    document.documentElement.style.setProperty('--sticky-offset', (headerHeight + gap) + 'px');
  }
  // gọi lúc load + resize + khi navbar co giãn
  window.addEventListener('load', updateStickyOffset);
  window.addEventListener('resize', updateStickyOffset);
  // đề phòng font/ảnh logo load chậm làm thay đổi chiều cao
  setTimeout(updateStickyOffset, 300);
})();

    // ===== Currency helper =====
    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN', { style:'currency', currency:'VND' }).format(amount);
    let currentDiscount = {{ $discount }};

    function updateFinalTotals(){
        const shippingFee = parseFloat(document.getElementById('shipping_fee_input').value) || 0;
        const subtotal = parseFloat(document.getElementById('subtotal-display').dataset.value) || 0;
        const discountRow = document.getElementById('discount-row');
        const discountDisplay = document.getElementById('discount-display');
        const total = (subtotal - currentDiscount) + shippingFee;

        document.getElementById('shipping-fee-display').textContent = isNaN(shippingFee) ? 'Chọn địa chỉ' : formatCurrency(shippingFee);
        document.getElementById('total-price-display').textContent = formatCurrency(Math.max(total, 0));

        if (currentDiscount > 0) {
            discountDisplay.textContent = `-${formatCurrency(currentDiscount)}`;
            discountRow.classList.remove('d-none');
        } else {
            discountRow.classList.add('d-none');
        }
    }

    // ===== GHN fee via backend route =====
    async function calculateShippingFee(){
        const districtId = document.getElementById('district_id').value;
        const wardCode   = document.getElementById('ward_code').value;
        const box        = document.getElementById('shipping-options-container');
        const feeInput   = document.getElementById('shipping_fee_input');

        if (!districtId || !wardCode) {
            box.innerHTML = '<div class="text-muted">Vui lòng điền đủ địa chỉ.</div>';
            feeInput.value = 0; updateFinalTotals(); return;
        }

        box.innerHTML = '<div class="d-inline-flex align-items-center gap-2"><span class="spinner-border spinner-border-sm"></span> Đang tính…</div>';

        try{
            const res = await fetch('{{ route("shipping.getFee") }}', {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept':'application/json'
                },
                body: JSON.stringify({ to_district_id: districtId, to_ward_code: wardCode })
            });
            const result = await res.json();

            if (result?.success && result.data) {
                box.innerHTML = `
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="fw-semibold">${result.data.name || 'Giao hàng'}</div>
                            <div class="text-muted xsmall">Dự kiến: ${result.data.leadtime_text || '—'}</div>
                        </div>
                        <div class="fw-bold">${formatCurrency(result.data.total)}</div>
                    </div>`;
                feeInput.value = result.data.total;
            } else {
                box.innerHTML = `<div class="text-danger">${result.message || 'Lỗi tính phí.'}</div>`;
                feeInput.value = 0;
            }
        }catch(e){
            console.error(e);
            box.innerHTML = '<div class="text-danger">Lỗi kết nối.</div>';
            feeInput.value = 0;
        }
        updateFinalTotals();
    }

    // ===== Voucher apply/remove =====
    const applyBtn    = document.getElementById('apply-voucher-btn');
    const removeBtn   = document.getElementById('remove-voucher-btn') || document.getElementById('remove-voucher-btn-js');
    const voucherInput= document.getElementById('voucher-code-input');
    const voucherMsg  = document.getElementById('voucher-message');

    if (applyBtn) {
        applyBtn.addEventListener('click', async function(){
            const code = (voucherInput?.value || '').trim();
            if (!code) return;
            this.disabled = true; this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try{
                const res = await fetch('{{ route("voucher.apply") }}', {
                    method:'POST',
                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':'application/json'
                    },
                    body: JSON.stringify({ code })
                });
                const result = await res.json();

                voucherMsg.className = result.success ? 'text-success small' : 'text-danger small';
                voucherMsg.textContent = result.message || '';

                if (result.success){
                    currentDiscount = result.discount || 0;
                    document.getElementById('voucher-form').classList.add('d-none');
                    document.getElementById('applied-voucher-code').textContent = code;
                    document.getElementById('applied-voucher-info').classList.remove('d-none');
                    updateFinalTotals();
                }
            }catch(e){
                voucherMsg.className = 'text-danger small';
                voucherMsg.textContent = 'Có lỗi xảy ra, vui lòng thử lại.';
            }finally{
                this.disabled = false; this.innerHTML = 'Áp dụng';
            }
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', async function(){
            try{
                const res = await fetch('{{ route("voucher.remove") }}', {
                    method:'POST',
                    headers:{
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':'application/json'
                    }
                });
                const result = await res.json();
                if (result.success){
                    currentDiscount = 0;
                    voucherInput && (voucherInput.value = '');
                    voucherMsg.textContent = '';
                    document.querySelectorAll('#applied-voucher-info').forEach(el => el.classList.add('d-none'));
                    const voucherForm = document.getElementById('voucher-form');
                    if (voucherForm) voucherForm.classList.remove('d-none');
                    updateFinalTotals();
                }
            }catch(e){
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            }
        });
    }

    document.getElementById('district_id')?.addEventListener('change', calculateShippingFee);
    document.getElementById('ward_code')?.addEventListener('change', calculateShippingFee);
    updateFinalTotals();
    </script>
@endpush
