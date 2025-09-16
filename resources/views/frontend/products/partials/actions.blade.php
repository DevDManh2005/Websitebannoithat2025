
<div class="product-actions-inner">
    <div class="mb-3 d-flex align-items-center gap-2">
        <label class="mb-0 small text-muted">Số lượng</label>
        <input type="number" name="quantity" id="product-qty-{{ $product->id }}" value="1" min="1" class="form-control form-control-sm" style="width:90px">
    </div>

    <div class="d-flex gap-2">
        <button type="button" class="btn btn-brand btn-add-cart" data-product-id="{{ $product->id }}">
            <i class="bi-cart-plus"></i> Thêm vào giỏ
        </button>

        <button type="button" class="btn btn-outline-brand btn-buy-now" data-product-id="{{ $product->id }}">
            Mua ngay
        </button>
    </div>

    {{-- Optional: wishlist / compare --}}
    <div class="mt-3 small text-muted">
        <a href="#" class="me-3">Chia sẻ</a>
        <a href="#" class="text-decoration-none">So sánh</a>
    </div>
</div>