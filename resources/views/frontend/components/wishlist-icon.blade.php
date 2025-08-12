<button type="button"
        class="wishlist-icon-component toggle-wishlist-btn {{ $isWishlisted ? 'active' : '' }}"
        data-product-id="{{ $productId }}">
    <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
</button>

@once
    @push('styles')
        <style>
            .wishlist-icon-component {
                border: none; background: none; cursor: pointer;
                font-size: 22px; color: #aaa; transition: all 0.3s ease;
            }
            .wishlist-icon-component.active i { color: #dc3545; }
            .wishlist-icon-component:hover i { transform: scale(1.2); }
        </style>
    @endpush
@endonce