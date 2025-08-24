@props(['product'])

@php
    $mainVariant = $product->variants->where('is_main_variant', true)->first() ?? $product->variants->first();
    $primaryImage = $product->images->where('is_primary', true)->first();
    $imageUrl = $primaryImage ? $primaryImage->image_url_path : 'https://placehold.co/300x300?text=No+Image';
    $hasSale = $mainVariant && $mainVariant->sale_price > 0 && $mainVariant->sale_price < $mainVariant->price;
    $isWishlisted = auth()->check() && auth()->user()->wishlist->contains($product->id);
    $averageRating = $product->average_rating;
@endphp

<div class="product-card-v3">
    {{-- Image Wrapper --}}
    <a href="{{ route('product.show', $product->slug) }}" class="product-image-wrapper">
        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="product-image">
    </a>

    {{-- Sale Ribbon --}}
    @if($hasSale)
        <div class="sale-ribbon"><span>SALE</span></div>
    @endif

    {{-- Wishlist Button --}}
    <button type="button" class="btn-icon-wishlist toggle-wishlist-btn {{ $isWishlisted ? 'active' : '' }}"
        data-product-id="{{ $product->id }}" aria-label="Thêm vào danh sách yêu thích">
        <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
    </button>

    {{-- Product Info --}}
    <div class="product-info-wrapper">
        <div class="product-meta">
            @if($averageRating > 0)
                <div class="product-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= round($averageRating) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            @else
                <div class="product-rating-placeholder">&nbsp;</div> {{-- Giữ chiều cao --}}
            @endif

            <h6 class="product-title">
                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
            </h6>

            <div class="product-price">
                @if($hasSale)
                    <span class="price-old">{{ number_format($mainVariant->price) }}₫</span>
                    <span class="price-sale">{{ number_format($mainVariant->sale_price) }}₫</span>
                @else
                    <span class="price-current">{{ number_format($mainVariant->price) }}₫</span>
                @endif
            </div>
        </div>

        {{-- Actions on Hover --}}
        <div class="card-actions-on-hover">
            <a href="{{ route('product.show', $product->slug) }}" class="btn btn-brand w-100">
                <i class="bi bi-eye me-1"></i> Xem Chi Tiết
            </a>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            /* =================== CSS Variables =================== */
            :root {
                --radius: 12px;
                --shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                --brand: #A20E38;
                --brand-600: #8E0D30;
                --muted: #6c757d;
                --text: #333;
            }

            /* =================== Product Card =================== */
            .product-card-v3 {
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 1));
                border: 1px solid rgba(0, 0, 0, 0.05);
                border-radius: var(--radius);
                box-shadow: var(--shadow);
                padding: 0.75rem;
                position: relative;
                overflow: hidden;
                transition: transform 0.25s ease, box-shadow 0.25s ease;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .product-card-v3:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            }

            .product-image-wrapper {
                display: block;
                border-radius: calc(var(--radius) - 2px);
                overflow: hidden;
                margin-bottom: 0.75rem;
                aspect-ratio: 1 / 1;
            }

            .product-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .product-card-v3:hover .product-image {
                transform: scale(1.05);
            }

            .sale-ribbon {
                position: absolute;
                top: -5px;
                left: -5px;
                width: 80px;
                height: 80px;
                overflow: hidden;
            }

            .sale-ribbon span {
                position: absolute;
                display: block;
                width: 120px;
                padding: 5px 0;
                background-color: var(--brand);
                color: #fff;
                font-size: 0.75rem;
                font-weight: bold;
                text-align: center;
                left: -30px;
                top: 20px;
                transform: rotate(-45deg);
            }

            .btn-icon-wishlist {
                position: absolute;
                top: 0.75rem;
                right: 0.75rem;
                width: 34px;
                height: 34px;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.85);
                border: 1px solid rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--muted);
                transition: all 0.2s ease;
                z-index: 5;
            }

            .btn-icon-wishlist:hover {
                color: var(--brand);
                transform: scale(1.1);
            }

            .btn-icon-wishlist.active {
                color: var(--brand);
            }

            .product-info-wrapper {
                display: flex;
                flex-direction: column;
                flex-grow: 1;
            }

            .product-meta {
                flex-grow: 1;
            }

            .product-rating {
                color: var(--brand);
                font-size: 0.85rem;
                margin-bottom: 0.5rem;
            }

            .product-rating-placeholder {
                height: 20px;
                margin-bottom: 0.5rem;
            }

            .product-title {
                font-size: 1rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
                height: 48px;
                overflow: hidden;
                text-overflow: ellipsis;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .product-title a {
                color: var(--text);
                text-decoration: none;
                transition: color 0.2s ease;
            }

            .product-title a:hover {
                color: var(--brand);
            }

            .product-price {
                margin-bottom: 0.75rem;
                font-size: 1.05rem;
            }

            .price-current,
            .price-sale {
                font-weight: 700;
                color: var(--brand);
            }

            .price-old {
                color: var(--muted);
                text-decoration: line-through;
                font-size: 0.85rem;
                margin-right: 0.5rem;
            }

            .card-actions-on-hover {
                opacity: 0;
                transform: translateY(10px);
                transition: opacity 0.2s ease, transform 0.2s ease;
            }

            .product-card-v3:hover .card-actions-on-hover {
                opacity: 1;
                transform: translateY(0);
            }

            .btn-brand {
                background-color: var(--brand);
                border-color: var(--brand);
                color: #fff;
                font-size: 0.9rem;
                padding: 0.5rem;
            }

            .btn-brand:hover {
                background-color: var(--brand-600);
                border-color: var(--brand-600);
            }

            /* =================== Responsive Design =================== */
            @media (max-width: 991px) {
                .product-image-wrapper {
                    aspect-ratio: 1 / 1;
                }

                .product-title {
                    font-size: 0.95rem;
                    height: 44px;
                }

                .product-price {
                    font-size: 1rem;
                }

                .btn-brand {
                    font-size: 0.85rem;
                    padding: 0.4rem;
                }
            }

            @media (max-width: 767px) {
                .product-card-v3 {
                    padding: 0.5rem;
                }

                .product-image-wrapper {
                    margin-bottom: 0.5rem;
                }

                .product-title {
                    font-size: 0.9rem;
                    height: 42px;
                }

                .product-price {
                    font-size: 0.95rem;
                }

                .btn-icon-wishlist {
                    width: 32px;
                    height: 32px;
                }

                .sale-ribbon {
                    width: 70px;
                    height: 70px;
                }

                .sale-ribbon span {
                    font-size: 0.7rem;
                    top: 18px;
                    left: -28px;
                }
            }

            @media (max-width: 575px) {
                .product-card-v3 {
                    padding: 0.4rem;
                }

                .product-image-wrapper {
                    border-radius: calc(var(--radius) - 4px);
                }

                .product-title {
                    font-size: 0.85rem;
                    height: 40px;
                }

                .product-price {
                    font-size: 0.9rem;
                }

                .btn-brand {
                    font-size: 0.8rem;
                    padding: 0.35rem;
                }

                .btn-icon-wishlist {
                    width: 30px;
                    height: 30px;
                    top: 0.5rem;
                    right: 0.5rem;
                }

                .sale-ribbon {
                    width: 60px;
                    height: 60px;
                }

                .sale-ribbon span {
                    font-size: 0.65rem;
                    top: 16px;
                    left: -26px;
                }
            }
        </style>
    @endpush
@endonce