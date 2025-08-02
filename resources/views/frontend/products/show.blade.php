@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container my-5">
    <div class="row">
        {{-- Cột bên trái: Thư viện ảnh --}}
        <div class="col-lg-6">
            <div class="product-gallery">
                <div class="main-image-container mb-3">
                    <img src="{{ $product->images->where('is_primary', true)->first()->image_url_path ?? 'https://placehold.co/600x600' }}" id="main-product-image" class="img-fluid rounded" alt="{{ $product->name }}">
                </div>
                <div class="thumbnail-images d-flex flex-wrap">
                    @foreach($product->images as $image)
                        <img src="{{ $image->image_url_path }}" class="img-thumbnail me-2 mb-2 gallery-thumbnail {{ $image->is_primary ? 'active' : '' }}" alt="Thumbnail" style="width: 80px; height: 80px; cursor: pointer;">
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Cột bên phải: Thông tin và lựa chọn --}}
        <div class="col-lg-6">
            <div class="product-details">
                <h1 class="product-title fw-bold">{{ $product->name }}</h1>
                <div class="d-flex align-items-center mb-3">
                    @include('frontend.components.star-rating', ['rating' => $product->average_rating])
                    <a href="#reviews" class="ms-2 text-muted text-decoration-none">({{ $product->approvedReviews->count() }} đánh giá)</a>
                </div>

                <div class="price-container mb-3">
                    <span id="product-price-display" class="fs-3 fw-bold text-danger">Vui lòng chọn thuộc tính</span>
                </div>

                {{-- Lựa chọn biến thể --}}
                <div id="variant-options">
                    @foreach($attributeGroups as $name => $values)
                    <div class="mb-3 variant-group" data-attribute-name="{{ $name }}">
                        <label class="form-label fw-bold">{{ ucfirst($name) }}:</label>
                        <div>
                            @foreach($values as $value)
                            <input type="radio" class="btn-check variant-option" name="{{ $name }}" value="{{ $value }}" id="{{ Str::slug($name) }}-{{ Str::slug($value) }}" autocomplete="off">
                            <label class="btn btn-outline-secondary me-2" for="{{ Str::slug($name) }}-{{ Str::slug($value) }}">{{ $value }}</label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <p class="text-muted small">Mã sản phẩm: <span id="product-sku">N/A</span></p>

                {{-- Hiển thị form/nút bấm dựa trên trạng thái đăng nhập --}}
                @auth
                    {{-- Form cho người dùng đã đăng nhập --}}
                    <form action="{{ route('cart.add') }}" method="POST" id="action-form">
                        @csrf
                        <input type="hidden" name="product_variant_id" id="selected-variant-id">
                        <input type="hidden" name="quantity" id="form-quantity-input" value="1">

                        <div class="d-flex align-items-center mb-3">
                            <label for="quantity-selector" class="form-label me-3 mb-0 fw-bold">Số lượng:</label>
                            <div class="input-group" style="width: 120px;">
                                <button class="btn btn-outline-secondary" type="button" id="quantity-minus">-</button>
                                <input type="text" class="form-control text-center" id="quantity-selector" value="1" min="1">
                                <button class="btn btn-outline-secondary" type="button" id="quantity-plus">+</button>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-sm-flex">
                            <button type="submit" name="action" value="add_to_cart" class="btn btn-primary btn-lg flex-grow-1" id="add-to-cart-btn" disabled>
                                <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                            </button>
                            <button type="submit" name="action" value="buy_now" class="btn btn-success btn-lg flex-grow-1" id="buy-now-btn" disabled>
                                Mua ngay
                            </button>
                        </div>
                    </form>
                @else
                    {{-- Giao diện cho khách (chưa đăng nhập) --}}
                    <div class="d-flex align-items-center mb-3">
                        <label class="form-label me-3 mb-0 fw-bold">Số lượng:</label>
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-outline-secondary" type="button" disabled>-</button>
                            <input type="text" class="form-control text-center" value="1" disabled>
                            <button class="btn btn-outline-secondary" type="button" disabled>+</button>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-sm-flex">
                         <a href="{{ route('login.form') }}" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="bi bi-cart-plus"></i> Đăng nhập để mua hàng
                        </a>
                    </div>
                    <div class="alert alert-info mt-3 small">Vui lòng <a href="{{ route('login.form') }}">đăng nhập</a> hoặc <a href="{{ route('register.form') }}">đăng ký</a> để có thể mua hàng.</div>
                @endguest

                <div class="product-meta mt-4 pt-3 border-top">
                    @if($product->categories->isNotEmpty())
                        <p class="mb-1"><strong>Danh mục:</strong> 
                            @foreach($product->categories as $category)
                                <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">{{ $category->name }}</a>@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    @endif
                    @if($product->brand)
                        <p class="mb-0"><strong>Thương hiệu:</strong> 
                            <a href="#" class="text-decoration-none">{{ $product->brand->name }}</a>
                        </p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Tabs Mô tả và Đánh giá --}}
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description-content" type="button" role="tab">Mô Tả Sản Phẩm</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-content" type="button" role="tab">Đánh Giá ({{ $product->approvedReviews->count() }})</button>
                </li>
            </ul>
            <div class="tab-content pt-4" id="productTabContent">
                <div class="tab-pane fade show active" id="description-content" role="tabpanel">
                    {!! nl2br(e($product->description)) !!}
                </div>
                <div class="tab-pane fade" id="reviews-content" role="tabpanel">
                    @forelse($product->approvedReviews as $review)
                        @include('frontend.components.review-item', ['review' => $review])
                    @empty
                        <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Sản phẩm liên quan --}}
    @if($relatedProducts->isNotEmpty())
    <div class="related-products mt-5">
        <h3 class="fw-bold mb-4">Sản phẩm liên quan</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $relatedProduct)
                <div class="col-6 col-md-4 col-lg-3">
                    @include('frontend.components.product-card', ['product' => $relatedProduct])
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logic này chỉ chạy khi có các lựa chọn biến thể
    const variantOptions = document.getElementById('variant-options');
    if (variantOptions) {
        const variantsData = @json($product->variants);
        const priceDisplay = document.getElementById('product-price-display');
        const skuDisplay = document.getElementById('product-sku');
        const selectedVariantInput = document.getElementById('selected-variant-id');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const buyNowBtn = document.getElementById('buy-now-btn');
        const quantitySelector = document.getElementById('quantity-selector');
        const formQuantityInput = document.getElementById('form-quantity-input');

        function updateProductInfo() {
            const selectedOptions = {};
            document.querySelectorAll('.variant-option:checked').forEach(radio => {
                selectedOptions[radio.name] = radio.value;
            });

            if (Object.keys(selectedOptions).length < {{ count($attributeGroups) }}) {
                return;
            }

            const matchedVariant = variantsData.find(variant => {
                const variantAttributes = variant.attributes || {};
                if (Object.keys(variantAttributes).length !== Object.keys(selectedOptions).length) return false;
                for (const key in selectedOptions) {
                    if (!variantAttributes.hasOwnProperty(key) || variantAttributes[key] !== selectedOptions[key]) {
                        return false;
                    }
                }
                return true;
            });

            if (matchedVariant) {
                if(selectedVariantInput) selectedVariantInput.value = matchedVariant.id;
                if(skuDisplay) skuDisplay.textContent = matchedVariant.sku;
                
                const price = parseFloat(matchedVariant.price);
                const salePrice = parseFloat(matchedVariant.sale_price) || 0;
                const formatCurrency = num => new Intl.NumberFormat('vi-VN').format(num) + ' ₫';

                if (salePrice > 0 && salePrice < price) {
                    priceDisplay.innerHTML = `<span class="text-muted text-decoration-line-through me-2">${formatCurrency(price)}</span> ${formatCurrency(salePrice)}`;
                } else {
                    priceDisplay.innerHTML = formatCurrency(price);
                }
                if(addToCartBtn) addToCartBtn.disabled = false;
                if(buyNowBtn) buyNowBtn.disabled = false;
                if(addToCartBtn) addToCartBtn.innerHTML = '<i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng';
            } else {
                if(selectedVariantInput) selectedVariantInput.value = '';
                if(skuDisplay) skuDisplay.textContent = 'N/A';
                priceDisplay.innerHTML = '<span class="text-danger">Sản phẩm không có sẵn</span>';
                if(addToCartBtn) addToCartBtn.disabled = true;
                if(buyNowBtn) buyNowBtn.disabled = true;
                if(addToCartBtn) addToCartBtn.innerHTML = '<i class="bi bi-x-circle"></i> Không có hàng';
            }
        }

        document.querySelectorAll('.variant-option').forEach(radio => {
            radio.addEventListener('change', updateProductInfo);
        });

        // Quantity selector
        const minusBtn = document.getElementById('quantity-minus');
        const plusBtn = document.getElementById('quantity-plus');

        if(minusBtn && quantitySelector && formQuantityInput) {
            minusBtn.addEventListener('click', () => {
                let currentVal = parseInt(quantitySelector.value);
                if (currentVal > 1) {
                    quantitySelector.value = currentVal - 1;
                    formQuantityInput.value = currentVal - 1;
                }
            });
        }
        if(plusBtn && quantitySelector && formQuantityInput) {
            plusBtn.addEventListener('click', () => {
                let currentVal = parseInt(quantitySelector.value);
                quantitySelector.value = currentVal + 1;
                formQuantityInput.value = currentVal + 1;
            });
        }
        if(quantitySelector && formQuantityInput) {
            quantitySelector.addEventListener('change', () => {
                formQuantityInput.value = quantitySelector.value;
            });
        }
    }

    // Image gallery (chạy cho tất cả)
    const mainImage = document.getElementById('main-product-image');
    document.querySelectorAll('.gallery-thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function() {
            mainImage.src = this.src;
            document.querySelector('.gallery-thumbnail.active')?.classList.remove('active');
            this.classList.add('active');
        });
    });
});
</script>
@endpush
