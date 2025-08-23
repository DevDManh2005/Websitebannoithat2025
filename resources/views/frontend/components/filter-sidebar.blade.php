@props(['categories', 'brands', 'max_price'])

<div class="filter-sidebar card border-0 shadow-sm rounded-4">
  <div class="card-body">

    <form id="filterForm" action="{{ route('products.index') }}" method="GET">
      {{-- Giữ lại query string khác --}}
      @foreach(request()->except(['categories', 'brands', 'price_max', 'page']) as $key => $value)
          @if(is_array($value))
              @foreach($value as $v)
                  <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
              @endforeach
          @else
              <input type="hidden" name="{{ $key }}" value="{{ $value }}">
          @endif
      @endforeach

      {{-- DANH MỤC --}}
      <div class="filter-block mb-4">
        <h6 class="filter-title d-flex align-items-center mb-3">
          <i class="bi bi-list-ul me-2 text-brand"></i> Danh mục
        </h6>
        <ul class="list-unstyled filter-category-list">
          @php $selectedCategories = (array) request('categories', []); @endphp
          @foreach($categories as $category)
              @include('frontend.components.filter-category-item', [
                  'category' => $category,
                  'selectedCategories' => $selectedCategories
              ])
          @endforeach
        </ul>
      </div>

      <hr>

      {{-- GIÁ --}}
      <div class="filter-block mb-4">
        <h6 class="filter-title d-flex align-items-center mb-3">
          <i class="bi bi-cash-stack me-2 text-brand"></i> Giá
        </h6>
        <input type="range" class="form-range" id="price-range" name="price_max"
               min="0" max="{{ $max_price ?? 50000000 }}"
               value="{{ request('price_max', $max_price ?? 50000000) }}">
        <div class="d-flex justify-content-between text-muted small mt-2">
          <span>0 ₫</span>
          <span id="price-range-value">{{ number_format(request('price_max', $max_price ?? 50000000)) }} ₫</span>
        </div>
      </div>

      <hr>

      {{-- THƯƠNG HIỆU --}}
      <div class="filter-block mb-4">
        <h6 class="filter-title d-flex align-items-center mb-3">
          <i class="bi bi-tags me-2 text-brand"></i> Thương hiệu
        </h6>
        @php $selectedBrands = (array) request('brands', []); @endphp
        <div class="brand-list">
          @foreach($brands as $brand)
            <div class="form-check mb-2">
              <input class="form-check-input js-auto-submit" type="checkbox"
                     name="brands[]" value="{{ $brand->id }}"
                     id="brand-{{ $brand->id }}"
                     {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}>
              <label class="form-check-label" for="brand-{{ $brand->id }}">
                {{ $brand->name }}
              </label>
            </div>
          @endforeach
        </div>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-brand text-white fw-semibold">
          <i class="bi bi-funnel-fill me-1"></i> Áp dụng lọc
        </button>
      </div>
    </form>

  </div>
</div>

@push('styles')
<style>
  .filter-sidebar {
    background: #fff;
  }
  .filter-title {
    font-weight: 600;
    font-size: 15px;
    text-transform: uppercase;
    letter-spacing: .5px;
  }
  .filter-block {
    padding-bottom: .25rem;
  }
  .filter-category-list li {
    margin-bottom: .35rem;
  }
  .filter-category-list .btn-toggle-sub {
    background: none;
    border: 0;
    padding: 0;
    margin-left: .25rem;
    color: var(--bs-gray-600);
    font-size: 13px;
  }
  .filter-category-list .btn-toggle-sub[aria-expanded="true"] i {
    transform: rotate(90deg);
  }
  .brand-list .form-check-label {
    cursor: pointer;
  }
  .btn-brand {
    background: var(--brand-color, #c5252d);
  }
  .btn-brand:hover {
    background: #d70b0b;
  }
</style>
@endpush

@push('scripts-page')
<script>
(function(){
  const form = document.getElementById('filterForm');
  if(!form) return;

  const price = document.getElementById('price-range');
  const priceVal = document.getElementById('price-range-value');
  if(price && priceVal){
    price.addEventListener('input', e => {
      priceVal.textContent = new Intl.NumberFormat('vi-VN').format(e.target.value) + ' ₫';
    });
    price.addEventListener('change', () => form.submit());
  }

  form.addEventListener('change', function(e){
    if(e.target.matches('input:is([name="categories[]"], [name="brands[]"])')){
      setTimeout(()=> form.submit(), 150);
    }
  });
})();
</script>
@endpush
