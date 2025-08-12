@props(['product'])

@php
    $mainVariant = $product->variants->where('is_main_variant', true)->first() ?? $product->variants->first();
    $primaryImage = $product->images->where('is_primary', true)->first();
    $imageUrl = $primaryImage ? $primaryImage->image_url_path : 'https://placehold.co/300x300?text=No+Image';
    $hasSale = $mainVariant && $mainVariant->sale_price > 0 && $mainVariant->sale_price < $mainVariant->price;
    $isWishlisted = auth()->check() && auth()->user()->wishlist->contains($product->id);
    $averageRating = $product->average_rating;
@endphp

<div class="product-card-wrapper" data-product-id="{{ $product->id }}">
    <div class="product-card position-relative border rounded shadow-sm p-2 h-100 d-flex flex-column fixed-card-height">

        <a href="{{ route('product.show', $product->slug) }}" class="product-image d-block mb-3">
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="img-fluid w-100 product-image-fixed">

            @if($hasSale)
                <div class="badge bg-danger position-absolute top-0 start-0 m-2">SALE</div>
            @endif
        </a>

        {{-- Thêm class "toggle-wishlist-btn" để script toàn cục hoạt động --}}
<button type="button" class="wishlist-btn toggle-wishlist-btn position-absolute top-0 end-0 m-2 {{ $isWishlisted ? 'active' : '' }}"
        data-product-id="{{ $product->id }}">
    <i class="bi {{ $isWishlisted ? 'bi-heart-fill' : 'bi-heart' }}"></i>
</button>

        <div class="product-info flex-grow-1 d-flex flex-column">
            <h6 class="product-title fw-semibold mb-2">
                <a href="{{ route('product.show', $product->slug) }}"
                    class="text-decoration-none text-dark">{{ $product->name }}</a>
            </h6>

            @if($averageRating > 0)
                <div class="mb-2">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= $averageRating ? '-fill text-warning' : '' }}"></i>
                    @endfor
                    <small class="text-muted">({{ number_format($averageRating, 1) }})</small>
                </div>
            @endif

            <div class="product-price mb-2">
                @if($hasSale)
                    <span class="text-muted text-decoration-line-through">{{ number_format($mainVariant->price) }}₫</span>
                    <span class="fw-bold text-danger ms-2">{{ number_format($mainVariant->sale_price) }}₫</span>
                @else
                    <span class="fw-bold text-dark">{{ number_format($mainVariant->price) }}₫</span>
                @endif
            </div>

            <div class="mt-auto pt-2 border-top">
                <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-eye me-1"></i> Xem Chi Tiết
                </a>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
        <style>
            .wishlist-btn {
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                z-index: 10;
            }

            .wishlist-btn i {
                color: #aaa;
                font-size: 18px;
                transition: transform 0.3s ease;
            }

            .wishlist-btn:hover i {
                transform: scale(1.2);
            }

            .wishlist-btn.active i {
                color: #dc3545;
            }

            .product-image-fixed {
                height: 250px;
                object-fit: cover;
                border-radius: 8px;
            }

            .fixed-card-height {
                min-height: 480px;
                /* bạn có thể thử 450px, 500px... để hợp layout */
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: relative;
            }

            .product-title {
                min-height: 48px;
                /* giữ tiêu đề không làm giãn card nếu dài hơn 1 dòng */
                overflow: hidden;
                text-overflow: ellipsis;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                /* hiển thị tối đa 2 dòng */
                -webkit-box-orient: vertical;
            }

            @keyframes pop {
                0% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.4);
                }

                100% {
                    transform: scale(1);
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.wishlist-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const productId = this.dataset.productId;
                        const icon = this.querySelector('i');
                        const wrapper = this.closest('.product-card-wrapper');
                        const self = this;

                        fetch('{{ route('wishlist.toggle') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ product_id: productId })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    self.classList.toggle('active');
                                    icon.classList.toggle('bi-heart');
                                    icon.classList.toggle('bi-heart-fill');
                                    icon.style.animation = 'pop 0.4s ease';
                                    setTimeout(() => icon.style.animation = '', 400);

                                    // Cập nhật localStorage
                                    localStorage.setItem('wishlist_updated', Date.now());

                                    // ✅ Cập nhật lại badge đếm số yêu thích ở header
                                    const badge = document.querySelector('#wishlist-count');
                                    if (badge) {
                                        const currentCount = parseInt(badge.textContent) || 0;
                                        if (data.status === 'added') {
                                            badge.textContent = currentCount + 1;
                                            badge.style.display = 'inline-block';
                                        } else if (data.status === 'removed') {
                                            const newCount = currentCount - 1;
                                            badge.textContent = newCount;
                                            badge.style.display = newCount > 0 ? 'inline-block' : 'none';
                                        }
                                    }

                                    // Nếu ở trang wishlist thì xóa khỏi DOM nếu bị gỡ
                                    if (window.location.pathname === '/danh-sach-yeu-thich' && data.status === 'removed') {
                                        wrapper.style.transition = 'opacity 0.3s ease';
                                        wrapper.style.opacity = 0;
                                        setTimeout(() => wrapper.remove(), 300);
                                    }
                                } else if (data.redirect) {
                                    window.location.href = data.redirect;
                                }
                            })
                            .catch(err => console.error('Lỗi xử lý wishlist:', err));
                    });
                });
            });
        </script>
    @endpush
@endonce