@props(['item'])

<div class="cart-item-row">
    <div class="row align-items-center">
        {{-- Image --}}
        <div class="col-2 col-md-1">
            <a href="{{ route('product.show', $item->variant->product->slug) }}">
                <img src="{{ optional($item->variant->product->images->where('is_primary', true)->first())->image_url_path ?? 'https://placehold.co/80x80' }}"
                     alt="{{ $item->variant->product->name }}"
                     class="img-fluid cart-item-thumbnail">
            </a>
        </div>

        {{-- Product Info --}}
        <div class="col-10 col-md-5">
            <a href="{{ route('product.show', $item->variant->product->slug) }}" class="cart-item-name">
                {{ $item->variant->product->name }}
            </a>
            <div class="cart-item-attributes">
                @foreach((array)$item->variant->attributes as $key => $value)
                    {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                @endforeach
            </div>
        </div>

        {{-- Price --}}
        <div class="col-5 col-md-2 mt-2 mt-md-0">
            @php
                $price = $item->variant->sale_price > 0 && $item->variant->sale_price < $item->variant->price
                    ? $item->variant->sale_price
                    : $item->variant->price;
            @endphp
            <div class="cart-item-price">
                <span class="price-current">{{ number_format($price) }} ₫</span>
                @if($price < $item->variant->price)
                    <small class="price-old">{{ number_format($item->variant->price) }} ₫</small>
                @endif
            </div>
        </div>

        {{-- Quantity --}}
        <div class="col-7 col-md-3 mt-2 mt-md-0 d-flex justify-content-start justify-content-md-center">
            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="cart-update-form">
                @csrf
                @method('PATCH')
                <div class="quantity-stepper">
                    <button type="button" class="btn-step" data-change="-1" aria-label="Giảm số lượng">-</button>
                    <input type="number" name="quantity" class="quantity-input" value="{{ $item->quantity }}" min="1" readonly>
                    <button type="button" class="btn-step" data-change="1" aria-label="Tăng số lượng">+</button>
                </div>
            </form>
        </div>

        {{-- Actions --}}
        <div class="col-12 col-md-1 text-md-end mt-2 mt-md-0 cart-item-actions">
            <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-icon-remove" aria-label="Xóa sản phẩm">
                    <i class="bi bi-trash3"></i>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ===== STYLES ===== --}}
@once
    @push('styles')
    <style>
        .cart-item-row {
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(0,0,0, .08);
        }
        .cart-item-row:last-child {
            border-bottom: 0;
        }
        .cart-item-thumbnail {
            border-radius: var(--radius, 12px);
            border: 1px solid rgba(0,0,0, .06);
        }
        .cart-item-name {
            font-weight: 600;
            color: var(--text, #2B2623);
            text-decoration: none;
            transition: color .15s ease;
        }
        .cart-item-name:hover {
            color: var(--brand, #A20E38);
        }
        .cart-item-attributes {
            font-size: 0.85rem;
            color: var(--muted, #7D726C);
        }
        .cart-item-price .price-current {
            font-weight: 700;
            color: var(--brand, #A20E38);
        }
        .cart-item-price .price-old {
            color: var(--muted, #7D726C);
            text-decoration: line-through;
        }

        /* Quantity Stepper */
        .quantity-stepper {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 99px;
            overflow: hidden;
        }
        .quantity-stepper .btn-step {
            background: transparent;
            border: none;
            color: var(--muted);
            font-weight: 600;
            width: 36px;
            height: 36px;
            line-height: 36px;
            cursor: pointer;
            transition: background .12s, color .12s;
        }
        .quantity-stepper .btn-step:hover {
            background: rgba(0,0,0, .04);
            color: var(--text);
        }
        .quantity-stepper .quantity-input {
            width: 40px;
            text-align: center;
            border: none;
            background: transparent;
            font-weight: 600;
            color: var(--text);
            -moz-appearance: textfield; /* Firefox */
            pointer-events: none;
        }
        .quantity-stepper .quantity-input::-webkit-outer-spin-button,
        .quantity-stepper .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Remove Button */
        .cart-item-actions {
            display: flex;
            justify-content: flex-end;
        }
        .btn-icon-remove {
            background: transparent;
            border: 0;
            color: var(--muted);
            width: 38px; height: 38px;
            border-radius: 50%;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background .15s, color .15s;
        }
        .btn-icon-remove:hover {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
    </style>
    @endpush
@endonce

{{-- ===== SCRIPT ===== --}}
@once
    @push('scripts-page')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Debounce function to limit how often a function can run.
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        const debouncedSubmit = debounce(form => form.submit(), 500);

        document.body.addEventListener('click', function(e) {
            const stepButton = e.target.closest('.btn-step');
            if (stepButton) {
                const stepper = stepButton.closest('.quantity-stepper');
                const input = stepper.querySelector('.quantity-input');
                const form = stepper.closest('.cart-update-form');
                const change = parseInt(stepButton.dataset.change, 10);
                let currentValue = parseInt(input.value, 10);

                let newValue = currentValue + change;
                if (newValue < 1) {
                    newValue = 1;
                }
                
                if (newValue !== currentValue) {
                    input.value = newValue;
                    if (form) {
                        debouncedSubmit(form);
                    }
                }
            }
        });
    });
    </script>
    @endpush
@endonce