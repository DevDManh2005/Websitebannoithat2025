<div class="product-actions-inner">
    @php $pid = $product->id ?? null; @endphp
    <input type="hidden" name="product_id" value="{{ $pid }}">
    <input type="hidden" name="product_variant_id" id="selected-variant-id-{{ $pid }}" value="{{ $selectedVariantId ?? '' }}">

    <div class="mb-3 d-flex align-items-center gap-2">
        <label for="product-qty-{{ $pid }}" class="mb-0 small text-muted">Số lượng</label>
        <input aria-label="Số lượng" type="number" name="quantity" id="product-qty-{{ $pid }}" value="1" min="1" max="999" step="1"
               class="form-control form-control-sm" style="width:90px">
    </div>

    <div class="d-flex gap-2">
        {{-- Khi có JS, xử lý .btn-add-cart/.btn-buy-now; nếu không có JS, server xử lý form submit với name=action --}}
        <button type="submit" name="action" value="add_to_cart" class="btn btn-brand btn-add-cart" data-product-id="{{ $pid }}">
            <i class="bi-cart-plus"></i> Thêm vào giỏ
        </button>

        <button type="submit" name="action" value="buy_now" class="btn btn-outline-brand btn-buy-now" data-product-id="{{ $pid }}">
            Mua ngay
        </button>
    </div>

    <div class="mt-3 small text-muted">
        <a href="#" class="me-3">Chia sẻ</a>
        <a href="#" class="text-decoration-none">So sánh</a>
    </div>
</div>