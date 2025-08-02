@props(['product'])

<div class="card h-100 product-card shadow-sm border-0 text-center">
    @php
        // Lấy thông tin cần thiết từ sản phẩm
        $mainVariant = $product->variants->where('is_main_variant', true)->first() ?? $product->variants->first();
        $primaryImage = $product->images->where('is_primary', true)->first();
        $imageUrl = $primaryImage && Storage::disk('public')->exists($primaryImage->image_url) 
                    ? asset('storage/' . $primaryImage->image_url) 
                    : 'https://placehold.co/300x300?text=No+Image';
        
        $hasSale = $mainVariant && $mainVariant->sale_price > 0 && $mainVariant->sale_price < $mainVariant->price;
        $isWishlisted = Auth::check() && Auth::user()->wishlist->contains($product->id);
    @endphp

    <a href="{{ route('product.show', $product->slug) }}" class="position-relative d-block">
        {{-- Ảnh sản phẩm --}}
        <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $product->name }}">
        
        {{-- Nhãn SALE --}}
        @if($hasSale)
            <div class="badge bg-danger position-absolute top-0 start-0 m-3 fs-6">SALE</div>
        @endif
    </a>

    <div class="card-body d-flex flex-column">
        {{-- Tên sản phẩm --}}
        <h5 class="card-title fs-6 mt-2 mb-2 flex-grow-1">
            <a href="{{ route('product.show', $product->slug) }}" class="text-dark text-decoration-none product-name">
                {{ $product->name }}
            </a>
        </h5>
        
        {{-- Giá sản phẩm --}}
        <div class="mt-auto">
            @if($mainVariant)
                <p class="card-text fw-bold mb-3">
                    @if($hasSale)
                        <span class="text-muted text-decoration-line-through me-2 small">{{ number_format($mainVariant->price) }} ₫</span>
                        <span class="text-danger">{{ number_format($mainVariant->sale_price) }} ₫</span>
                    @else
                        {{ number_format($mainVariant->price) }} ₫
                    @endif
                </p>
            @endif
        </div>
        
        {{-- Nút Thêm vào giỏ hàng & Yêu thích --}}
        <div class="product-actions mt-auto pt-2 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-link text-dark text-decoration-none p-0">
                    <i class="bi bi-plus"></i> Thêm Vào Giỏ Hàng
                </a>
                <button type="button" class="btn btn-link text-dark p-0 toggle-wishlist-btn {{ $isWishlisted ? 'active' : '' }}" data-product-id="{{ $product->id }}">
                    <i class="bi {{ $isWishlisted ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                </button>
            </div>
        </div>
    </div>
</div>

