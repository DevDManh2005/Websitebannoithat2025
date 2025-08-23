@props(['productId', 'isWishlisted'])

<button type="button"
        class="wishlist-icon-component toggle-wishlist-btn {{ $isWishlisted ? 'active' : '' }}"
        data-product-id="{{ $productId }}"
        aria-label="Thêm vào danh sách yêu thích">
    <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
</button>

@once
    @push('styles')
        <style>
            .wishlist-icon-component {
                padding: 0;
                border: none;
                background: none;
                cursor: pointer;
                font-size: 1.5rem; /* 24px */
                color: var(--muted, #aaa);
                transition: color 0.2s ease, transform 0.2s ease;
            }
            .wishlist-icon-component:hover {
                color: var(--brand, #A20E38);
            }
            .wishlist-icon-component.active {
                color: var(--brand, #A20E38);
            }
            .wishlist-icon-component:hover i {
                transform: scale(1.15);
            }
        </style>
    @endpush
@endonce