@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn')

@section('content')
    {{-- =================== HERO / BREADCRUMB =================== --}}
    <section class="cart-hero position-relative overflow-hidden mb-5">
        <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop"
             alt="Cart Banner" class="hero-bg">
        <div class="hero-overlay"></div>
        <div class="container position-relative" data-aos="fade-down">
            <div class="row align-items-center" style="min-height: 220px;">
                <div class="col-12 text-center">
                    <h1 class="fw-bold text-white mb-2">Giỏ Hàng</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="hero-bc-link" href="{{ route('home') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">Giỏ hàng</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="hero-bottom wave-sep"></div>
    </section>

    <div class="container my-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" data-aos="fade-up">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" data-aos="fade-up">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($cartItems->isNotEmpty())
            <div class="row g-4">
                {{-- =================== LEFT: ITEMS =================== --}}
                <div class="col-lg-8">
                    <div class="card card-glass rounded-4" data-aos="fade-right">
                        <div class="card-header bg-transparent p-3 p-md-4 rounded-top-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select-all-items">
                                    <label class="form-check-label fw-bold" for="select-all-items">
                                        Chọn tất cả ({{ $cartItems->count() }} sản phẩm)
                                    </label>
                                </div>
                                <form action="{{ route('cart.removeSelected') }}" method="POST" id="delete-selected-form" onsubmit="return confirm('Bạn có chắc muốn xóa các sản phẩm đã chọn?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" id="delete-selected-btn" class="btn btn-sm btn-outline-danger rounded-pill" disabled>
                                        <i class="bi bi-trash me-1"></i> Xóa mục đã chọn
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            @foreach($cartItems as $item)
                                <div class="cart-item-row d-flex align-items-center p-3 p-md-4 border-bottom">
                                    <div class="form-check me-3 flex-shrink-0">
                                        <input
                                            class="form-check-input item-checkbox"
                                            type="checkbox"
                                            value="{{ $item->id }}"
                                            data-price="{{ $item->variant->sale_price ?: $item->variant->price }}"
                                            data-quantity="{{ $item->quantity }}"
                                            {{ $item->is_selected ? 'checked' : '' }}>
                                    </div>

                                    <a href="{{ route('product.show', $item->variant->product->slug) }}" class="me-3 flex-shrink-0 d-block rounded-3 overflow-hidden img-hover-zoom" style="width:84px;height:84px;">
                                        <img src="{{ optional($item->variant->product->primaryImage)->image_url_path ?? 'https://placehold.co/100x100' }}"
                                             alt="{{ $item->variant->product->name }}" class="w-100 h-100 object-fit-cover">
                                    </a>

                                    <div class="flex-grow-1">
                                        <a href="{{ route('product.show', $item->variant->product->slug) }}" class="text-dark fw-semibold text-decoration-none hover-underline text-truncate-2 d-block">
                                            {{ $item->variant->product->name }}
                                        </a>
                                        <div class="text-muted xsmall mt-1">
                                            @foreach($item->variant->attributes as $key => $value)
                                                <span class="me-2">{{ ucfirst($key) }}: <strong>{{ $value }}</strong></span>
                                            @endforeach
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-brand fw-bold">{{ number_format($item->variant->sale_price ?: $item->variant->price) }} ₫</span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center ms-3">
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-flex align-items-center">
                                            @csrf @method('PATCH')
                                            <div class="input-group input-group-sm qty-group">
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control text-center form-control-modern" style="width: 70px;" onchange="this.form.submit()">
                                            </div>
                                        </form>

                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="ms-2 ms-md-3">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-sm rounded-pill" title="Xóa sản phẩm">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- =================== RIGHT: SUMMARY =================== --}}
                <div class="col-lg-4" data-aos="fade-left">
                    <div class="card card-glass rounded-4 sticky-order">
                        <div class="card-body p-4 p-md-5">
                            <h5 class="card-title fw-bold text-brand mb-3">Tóm tắt đơn hàng</h5>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Tạm tính (<span id="selected-items-count">0</span> sản phẩm)</span>
                                <strong id="total-price-display">0 ₫</strong>
                            </div>

                            <div class="d-grid">
                                <a href="{{ route('checkout.index') }}" class="btn btn-brand btn-lg rounded-pill" id="checkout-btn">Tiến hành thanh toán</a>
                            </div>

                            <div class="small text-muted mt-3">
                                * Chỉ những sản phẩm bạn <strong>đã chọn</strong> mới được đưa sang bước thanh toán.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5 card card-glass rounded-4" data-aos="zoom-in">
                <div class="card-body">
                    <i class="bi bi-cart-x" style="font-size: 4rem; color: var(--muted);"></i>
                    <h5 class="mt-3 fw-bold">Giỏ hàng của bạn đang trống</h5>
                    <p class="text-muted">Hãy khám phá thêm các sản phẩm tuyệt vời của chúng tôi!</p>
                    <a href="{{ route('products.index') }}" class="btn btn-brand mt-2 rounded-pill">Tiếp tục mua sắm</a>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
/* =================== Hero Section =================== */
.cart-hero {
    background: var(--bg);
}
.cart-hero .hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: scale(1.03);
    filter: brightness(0.7);
}
.cart-hero .hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.35), rgba(0, 0, 0, 0.35));
}
.wave-sep {
    position: absolute;
    left: 0;
    right: 0;
    bottom: -1px;
    height: 28px;
    background: radial-gradient(36px 11px at 50% 0, var(--bg) 98%, transparent 100%) repeat-x;
    background-size: 36px 18px;
}
.hero-bc-link {
    color: var(--sand);
    text-decoration: none;
    transition: color 0.2s ease;
}
.hero-bc-link:hover {
    color: var(--brand);
}
.breadcrumb-item.active {
    color: var(--muted);
}

/* =================== Card and Elements =================== */
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
.text-brand {
    color: var(--brand) !important;
}
.text-dark {
    color: var(--text) !important;
}
.text-muted {
    color: var(--muted) !important;
}
.fw-bold {
    font-weight: 700 !important;
}
.xsmall {
    font-size: 0.825rem;
}

/* =================== Cart Rows =================== */
.cart-item-row {
    transition: background 0.2s ease;
}
.cart-item-row:hover {
    background: rgba(var(--brand-rgb), 0.05);
}
.img-hover-zoom img {
    transition: transform 0.35s ease;
    display: block;
}
.img-hover-zoom:hover img {
    transform: scale(1.06);
}
.hover-underline:hover {
    text-decoration: underline;
    text-underline-offset: 2px;
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* =================== Form Controls =================== */
.form-control-modern {
    border-radius: 0.8rem;
    border: 1px solid #e9ecef;
    background: var(--card);
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.form-control-modern:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 0.2rem var(--ring);
}
.form-check-input {
    border-color: var(--muted);
    transition: border-color 0.2s ease, background-color 0.2s ease;
}
.form-check-input:checked {
    background-color: var(--brand);
    border-color: var(--brand);
}
.form-check-input:focus {
    border-color: var(--brand);
    box-shadow: 0 0 0 0.2rem var(--ring);
}

/* =================== Buttons =================== */
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
.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
    transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease;
}
.btn-outline-danger:hover {
    background-color: #dc3545;
    color: #fff;
    transform: translateY(-2px);
}
.btn-light {
    background-color: var(--card);
    border-color: #e9ecef;
    transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease;
}
.btn-light:hover {
    background-color: #f8f9fa;
    color: var(--text);
    transform: translateY(-2px);
}
.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.1rem;
}

/* =================== Sticky Summary =================== */
.sticky-order {
    position: sticky;
    top: var(--sticky-offset, 88px);
    z-index: 1;
}
.container, .row {
    overflow: visible;
}

/* =================== Empty Cart =================== */
.bi-cart-x {
    transition: color 0.2s ease;
}

/* =================== Responsive Design =================== */
@media (max-width: 991px) {
    .cart-hero {
        min-height: 180px;
    }
    .cart-hero h1 {
        font-size: 2rem;
    }
    .col-lg-8, .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .sticky-order {
        position: static !important;
    }
    .card-body {
        padding: 1.5rem;
    }
    .img-hover-zoom {
        width: 72px !important;
        height: 72px !important;
    }
}

@media (max-width: 767px) {
    .cart-hero {
        min-height: 160px;
    }
    .cart-hero h1 {
        font-size: 1.8rem;
    }
    .container {
        padding-left: 15px;
        padding-right: 15px;
    }
    .card-body {
        padding: 1rem;
    }
    .cart-item-row {
        padding: 1rem;
    }
    .img-hover-zoom {
        width: 60px !important;
        height: 60px !important;
    }
    .form-control-modern {
        width: 60px !important;
        font-size: 0.9rem;
    }
    .btn-sm {
        padding: 0.35rem 0.7rem;
        font-size: 0.85rem;
    }
    .xsmall {
        font-size: 0.75rem;
    }
    .btn-lg {
        padding: 0.6rem 1.2rem;
        font-size: 1rem;
    }
}

@media (max-width: 575px) {
    .cart-hero {
        min-height: 140px;
    }
    .cart-hero h1 {
        font-size: 1.6rem;
    }
    .breadcrumb {
        font-size: 0.85rem;
    }
    .card-body {
        padding: 0.75rem;
    }
    .cart-item-row {
        padding: 0.75rem;
    }
    .img-hover-zoom {
        width: 50px !important;
        height: 50px !important;
    }
    .form-control-modern {
        width: 50px !important;
        font-size: 0.85rem;
    }
    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    .xsmall {
        font-size: 0.7rem;
    }
    .alert {
        font-size: 0.85rem;
        padding: 0.5rem;
    }
    .bi-cart-x {
        font-size: 3rem;
    }
}
</style>
@endpush

@push('scripts-page')
<script>
    // ===== Auto sticky offset theo chiều cao header (home/internal khác nhau)
    (function(){
        function updateStickyOffset(){
            const header = document.querySelector('header.sticky-top, header.navbar-transparent, header');
            const h = header ? header.offsetHeight : 72;
            const gap = 16;
            document.documentElement.style.setProperty('--sticky-offset', (h + gap) + 'px');
        }
        window.addEventListener('load', updateStickyOffset);
        window.addEventListener('resize', updateStickyOffset);
        setTimeout(updateStickyOffset, 300);

        // AOS init
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                once: true,
                offset: 80
            });
        }
    })();

    // ===== Logic chọn / tính tiền
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const selectAllCheckbox = document.getElementById('select-all-items');
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    const totalPriceDisplay = document.getElementById('total-price-display');
    const checkoutBtn = document.getElementById('checkout-btn');
    const selectedItemsCount = document.getElementById('selected-items-count');
    const formatCurrency = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';

    let debounceTimer;
    function syncSelected(ids){
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetch('{{ route("cart.toggleSelect") }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ items: ids })
            }).catch(()=>{});
        }, 350);
    }

    function updateTotalsAndUI() {
        const selected = Array.from(document.querySelectorAll('.item-checkbox:checked'));
        let total = 0;
        selected.forEach(cb => total += parseFloat(cb.dataset.price) * parseFloat(cb.dataset.quantity));

        totalPriceDisplay.textContent = formatCurrency(total);
        selectedItemsCount.textContent = selected.length;

        // Disable/enable actions
        const disabled = selected.length === 0;
        checkoutBtn.classList.toggle('disabled', disabled);
        deleteSelectedBtn.disabled = disabled;

        // Select-all state
        if (checkboxes.length > 0) {
            selectAllCheckbox.checked = selected.length === checkboxes.length && selected.length > 0;
        }

        // Sync to server (debounced)
        syncSelected(selected.map(cb => cb.value));
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateTotalsAndUI();
        });
    }
    checkboxes.forEach(cb => cb.addEventListener('change', updateTotalsAndUI));

    // Init on load
    updateTotalsAndUI();
</script>
@endpush