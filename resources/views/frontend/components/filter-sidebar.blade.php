@props(['categories', 'brands', 'min_price', 'max_price'])

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('products.index') }}" method="GET">
            {{-- Lọc theo Danh mục --}}
            <div class="mb-4">
                <h5 class="fw-bold mb-3">Danh mục</h5>
                <ul class="list-unstyled">
                    @foreach($categories as $category)
                        <li>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="cat-{{ $category->id }}">
                                <label class="form-check-label" for="cat-{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <hr>

            {{-- Lọc theo Giá --}}
            <div class="mb-4">
                <h5 class="fw-bold mb-3">Giá</h5>
                <div id="price-range-slider">
                    <input type="range" class="form-range" id="price-range" name="price_max" min="{{ $min_price ?? 0 }}" max="{{ $max_price ?? 50000000 }}" value="{{ request('price_max', $max_price ?? 50000000) }}">
                    <div class="d-flex justify-content-between text-muted small mt-2">
                        <span>{{ number_format($min_price ?? 0) }} ₫</span>
                        <span id="price-range-value">{{ number_format(request('price_max', $max_price ?? 50000000)) }} ₫</span>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Lọc theo Thương hiệu --}}
            <div class="mb-4">
                <h5 class="fw-bold mb-3">Thương hiệu</h5>
                @foreach($brands as $brand)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="brands[]" value="{{ $brand->id }}" id="brand-{{ $brand->id }}">
                        <label class="form-check-label" for="brand-{{ $brand->id }}">
                            {{ $brand->name }}
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Lọc</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Cập nhật hiển thị giá trị của thanh trượt giá
    const priceRange = document.getElementById('price-range');
    const priceRangeValue = document.getElementById('price-range-value');
    if (priceRange) {
        priceRange.addEventListener('input', function() {
            priceRangeValue.textContent = new Intl.NumberFormat('vi-VN').format(this.value) + ' ₫';
        });
    }
</script>
@endpush
