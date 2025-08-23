@extends('layouts.app')

@section('title', $product->name)

@section('content')
    {{-- =================== BREADCRUMB =================== --}}
    <div class="py-3 card-glass border-bottom">
        <div class="container" data-aos="fade-in">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    @if($category = $product->categories->first())
                        <li class="breadcrumb-item">
                            <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container my-5">
        <div class="row g-4 g-lg-5">
            {{-- =================== GALLERY =================== --}}
            <div class="col-lg-6" data-aos="fade-right">
                <div class="product-gallery">
                    <div class="swiper main-image-swiper rounded-4 shadow-sm">
                        <div class="swiper-wrapper">
                            @forelse($product->images as $image)
                                <div class="swiper-slide">
                                    <img src="{{ $image->image_url_path }}" class="w-100 h-100 object-fit-cover"
                                        alt="{{ $product->name }}">
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <img src="https://placehold.co/800x800?text=No+Image" class="w-100 h-100 object-fit-cover"
                                        alt="No Image Available">
                                </div>
                            @endforelse
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>

                    @if($product->images->count() > 1)
                        <div class="swiper thumbnail-swiper mt-3">
                            <div class="swiper-wrapper">
                                @foreach($product->images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ $image->image_url_path }}" class="img-fluid rounded-3 border" alt="Thumbnail">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- =================== INFO / ACTIONS =================== --}}
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h1 class="fw-bold mb-0 text-dark pe-3">{{ $product->name }}</h1>
                    <div class="ms-3">
                        <x-wishlist-icon :productId="$product->id" :isWishlisted="$isWished" />
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    @include('frontend.components.star-rating', ['rating' => $product->average_rating])
                    <a href="#reviews-content" class="ms-3 text-muted small text-decoration-none border-start ps-3">
                        {{ $product->approvedReviews->count() }} đánh giá
                    </a>
                </div>

                {{-- Dùng card-glass và border-brand để đồng bộ --}}
                <div class="product-price-box card-glass p-4 rounded-4 mb-4 border-brand">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-soft-brand text-uppercase fw-semibold">Giá</span>
                        <h3 class="text-brand fw-bolder mb-0" id="product-price-display">Vui lòng chọn thuộc tính</h3>
                    </div>
                    <small class="text-muted d-block mt-2">Mã SP: <span id="product-sku">N/A</span></small>
                </div>

                <form id="action-form" class="product-actions">
                    @csrf
                    <input type="hidden" name="product_variant_id" id="selected-variant-id">

                    {{-- Thuộc tính / biến thể --}}
                    <div id="variant-options" class="mb-4">
                        @foreach($attributeGroups as $name => $values)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ Str::ucfirst($name) }}:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($values as $value)
                                        @php $id = Str::slug($name . '-' . $value); @endphp
                                        <input type="radio" class="btn-check variant-option" name="{{ $name }}" value="{{ $value }}"
                                            id="{{ $id }}" autocomplete="off">
                                        {{-- Dùng btn-outline-brand để đồng bộ màu sắc --}}
                                        <label class="btn btn-outline-brand btn-variant" for="{{ $id }}">{{ $value }}</label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Số lượng --}}
                    <div class="d-flex align-items-center mb-4">
                        <label class="me-3 fw-semibold">Số lượng:</label>
                        <div class="input-group quantity-group">
                            <button type="button" class="btn btn-outline-brand" id="quantity-minus"
                                aria-label="Giảm số lượng">−</button>
                            <input type="number" class="form-control text-center" name="quantity" id="quantity-selector"
                                value="1" min="1" inputmode="numeric">
                            <button type="button" class="btn btn-outline-brand" id="quantity-plus"
                                aria-label="Tăng số lượng">+</button>
                        </div>
                    </div>

                    {{-- CTA --}}
                    @auth
                        <div class="d-grid gap-2 d-sm-flex">
                            <button type="button" class="btn btn-outline-brand btn-lg flex-grow-1 rounded-pill"
                                id="add-to-cart-btn" disabled>
                                <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ
                            </button>
                            <button type="button" class="btn btn-brand btn-lg flex-grow-1 rounded-pill" id="buy-now-btn"
                                disabled>
                                <i class="bi bi-bag-check-fill me-2"></i>Mua ngay
                            </button>
                        </div>
                    @else
                        <a href="{{ route('login.form') }}" class="btn btn-brand btn-lg mt-3 w-100 rounded-pill">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập để mua hàng
                        </a>
                    @endauth
                </form>
            </div>
        </div>

        {{-- =================== TABS =================== --}}
        <div class="row mt-5" data-aos="fade-up">
            <div class="col-12">
                <ul class="nav nav-underline nav-tabs-modern gap-3" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description-content">
                            Mô tả sản phẩm
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews-content">
                            Đánh giá ({{ $product->approvedReviews->count() }})
                        </button>
                    </li>
                </ul>

                {{-- Sửa lỗi: Lớp nền bị trùng lặp --}}
                <div class="tab-content p-4 rounded-4 mt-3 card-glass">
                    {{-- Sửa lỗi: Đã đổi thứ tự tab content --}}
                    <div class="tab-pane fade show active" id="description-content">
                        {!! $product->description !!}
                    </div>

                    <div class="tab-pane fade" id="reviews-content">
                        @forelse($product->approvedReviews as $review)
                            @include('frontend.components.review-item', ['review' => $review])
                        @empty
                            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                        @endforelse

                        @auth
                            @if($userHasPurchased)
                                @include('frontend.components.review-form', ['product' => $product])
                            @else
                                <div class="alert alert-warning mt-4">Bạn cần mua sản phẩm để đánh giá.</div>
                            @endif
                        @else
                            <div class="alert alert-info mt-4">
                                <a href="{{ route('login.form') }}">Đăng nhập</a> để gửi đánh giá.
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* CSS cục bộ để đồng bộ màu sắc và style */
        .breadcrumb-item a {
            text-decoration: none;
            color: var(--muted);
        }

        .breadcrumb-item a:hover {
            color: var(--brand);
        }

        .breadcrumb-item.active {
            color: var(--brand);
        }

        .nav-tabs-modern .nav-link {
            border: none;
            color: var(--muted);
            position: relative;
            padding: .5rem 0;
        }

        .nav-tabs-modern .nav-link.active {
            color: var(--brand);
        }

        .nav-tabs-modern .nav-link.active::after,
        .nav-tabs-modern .nav-link:hover::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -8px;
            height: 2px;
            background: var(--brand);
        }

        .btn-variant {
            border-radius: 999px;
            padding: .35rem .85rem;
        }

        .btn-check:checked+.btn-variant {
            background: var(--brand);
            border-color: var(--brand);
            color: var(--card);
            box-shadow: 0 6px 14px var(--ring);
        }

        .product-price-box h3.text-danger {
            color: var(--brand) !important;
        }

        /* Các style còn lại được giữ nguyên */
        .product-gallery .main-image-swiper {
            height: 520px;
            background: #fff;
        }

        .product-gallery .thumbnail-swiper {
            height: 96px;
            padding: 8px 0;
        }

        .product-gallery .thumbnail-swiper .swiper-slide {
            width: 25%;
            height: 100%;
            opacity: .6;
            transition: opacity .25s ease, transform .25s ease;
            cursor: pointer;
        }

        .product-gallery .thumbnail-swiper .swiper-slide-thumb-active {
            opacity: 1;
            transform: translateY(-2px);
            outline: 2px solid var(--brand);
            outline-offset: 2px;
            border-radius: .5rem;
        }

        .product-gallery .thumbnail-swiper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: .5rem;
        }

        .quantity-group {
            width: 160px;
        }

        .quantity-group .form-control {
            border-left: 0;
            border-right: 0;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .bg-light-subtle {
            background: #f8f9fa;
        }
    </style>
@endpush

@push('scripts-page')
    <script>
        const variantsData = @json($product->variants);
        const priceDisplay = document.getElementById('product-price-display');
        const skuDisplay = document.getElementById('product-sku');
        const selectedVariantInput = document.getElementById('selected-variant-id');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const buyNowBtn = document.getElementById('buy-now-btn');
        const quantitySelector = document.getElementById('quantity-selector');
        const attributeGroupCount = {{ count($attributeGroups) }};

        function updateProductInfo() {
            const selectedOptions = {};
            document.querySelectorAll('.variant-option:checked').forEach(radio => {
                selectedOptions[radio.name] = radio.value;
            });

            if (Object.keys(selectedOptions).length < attributeGroupCount) return;

            const matchedVariant = variantsData.find(v => {
                if (!v.attributes || Object.keys(v.attributes).length !== Object.keys(selectedOptions).length) return false;
                return Object.entries(selectedOptions).every(([key, value]) => v.attributes[key] === value);
            });

            const formatCurrency = num => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num);

            if (matchedVariant) {
                selectedVariantInput.value = matchedVariant.id;
                skuDisplay.textContent = matchedVariant.sku;
                const price = parseFloat(matchedVariant.price);
                const salePrice = parseFloat(matchedVariant.sale_price) || 0;

                priceDisplay.innerHTML = (salePrice > 0 && salePrice < price)
                    ? `<span class="text-muted text-decoration-line-through me-2">${formatCurrency(price)}</span> ${formatCurrency(salePrice)}`
                    : formatCurrency(price);

                if (addToCartBtn) addToCartBtn.disabled = false;
                if (buyNowBtn) buyNowBtn.disabled = false;
            } else {
                selectedVariantInput.value = '';
                skuDisplay.textContent = 'N/A';
                priceDisplay.innerHTML = '<span class="text-brand">Phiên bản không có sẵn</span>';
                if (addToCartBtn) addToCartBtn.disabled = true;
                if (buyNowBtn) buyNowBtn.disabled = true;
            }
        }
        document.querySelectorAll('.variant-option').forEach(radio => radio.addEventListener('change', updateProductInfo));

        document.getElementById('quantity-minus')?.addEventListener('click', () => {
            let currentVal = parseInt(quantitySelector.value || '1', 10);
            if (currentVal > 1) quantitySelector.value = currentVal - 1;
        });
        document.getElementById('quantity-plus')?.addEventListener('click', () => {
            let currentVal = parseInt(quantitySelector.value || '1', 10);
            quantitySelector.value = currentVal + 1;
        });

        async function handleCartAction(url, formData) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                return await response.json();
            } catch (error) {
                console.error('Lỗi:', error);
                return { success: false, message: 'Có lỗi xảy ra, vui lòng thử lại.' };
            }
        }

        addToCartBtn?.addEventListener('click', async function () {
            this.disabled = true;
            const formData = new FormData(document.getElementById('action-form'));
            const result = await handleCartAction('{{ route("cart.add") }}', formData);

            if (result.success) {
                const cartBadgeById = document.getElementById('cart-count');
                const cartBadge = cartBadgeById || document.querySelector('[data-cart-badge]');
                if (cartBadge) {
                    cartBadge.textContent = result.cart_count ?? cartBadge.textContent;
                    cartBadge.style.display = parseInt(cartBadge.textContent || '0', 10) > 0 ? 'inline-block' : 'none';
                }
                Swal.fire({ icon: 'success', title: 'Đã thêm vào giỏ!', showConfirmButton: false, timer: 1400, toast: true, position: 'top-end' });
            } else {
                Swal.fire({ icon: 'error', title: 'Thất bại', text: result.message || 'Không thể thêm vào giỏ.' });
            }
            this.disabled = false;
        });

        buyNowBtn?.addEventListener('click', async function () {
            this.disabled = true;
            const formData = new FormData(document.getElementById('action-form'));
            const result = await handleCartAction('{{ route("cart.buyNow") }}', formData);

            if (result.success && result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                Swal.fire({ icon: 'error', title: 'Thất bại', text: result.message || 'Không thể xử lý đơn hàng.' });
                this.disabled = false;
            }
        });

        // Swiper init
        if (document.querySelector('.main-image-swiper')) {
            const hasThumb = document.querySelectorAll('.thumbnail-swiper .swiper-slide').length > 0;
            const thumbSwiper = hasThumb ? new Swiper('.thumbnail-swiper', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
                breakpoints: { 0: { slidesPerView: 4 }, 576: { slidesPerView: 5 }, 992: { slidesPerView: 6 } }
            }) : null;

            new Swiper('.main-image-swiper', {
                spaceBetween: 10,
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                thumbs: hasThumb ? { swiper: thumbSwiper } : undefined
            });
        }
    </script>
@endpush