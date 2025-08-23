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
    <button type="button"
            class="btn-icon-wishlist toggle-wishlist-btn {{ $isWishlisted ? 'active' : '' }}"
            data-product-id="{{ $product->id }}"
            aria-label="Thêm vào danh sách yêu thích">
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
        .product-card-v3 {
            background: linear-gradient(180deg, rgba(255, 255, 255, .7), rgba(255, 255, 255, .9));
            border: 1px solid rgba(0,0,0, .05);
            border-radius: var(--radius, 12px);
            box-shadow: var(--shadow);
            padding: 0.75rem;
            position: relative;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .product-card-v3:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1);
        }

        .product-image-wrapper {
            display: block;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 0.75rem;
        }
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform .3s ease;
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
            text-shadow: 0 1px 1px rgba(0,0,0,0.2);
            text-transform: uppercase;
            text-align: center;
            left: -30px;
            top: 20px;
            transform: rotate(-45deg);
        }

        .btn-icon-wishlist {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 36px; height: 36px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0,0,0,0.1);
            backdrop-filter: blur(4px);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            transition: all .2s ease;
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

        .product-rating { color: var(--brand); font-size: 0.9rem; margin-bottom: 0.5rem; }
        .product-rating-placeholder { height: 21px; margin-bottom: 0.5rem; } /* Same height as rating */
        
        .product-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            height: 48px; /* 2 lines height */
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .product-title a {
            color: var(--text);
            text-decoration: none;
            transition: color .2s ease;
        }
        .product-title a:hover { color: var(--brand); }

        .product-price {
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
        }
        .price-current, .price-sale { font-weight: 700; color: var(--brand); }
        .price-old { color: var(--muted); text-decoration: line-through; font-size: 0.9rem; margin-right: 0.5rem; }

        .card-actions-on-hover {
            opacity: 0;
            color: #fff;
            transform: translateY(10px);
            transition: opacity .2s ease, transform .2s ease;
        }
        .product-card-v3:hover .card-actions-on-hover {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @endpush
@endonce