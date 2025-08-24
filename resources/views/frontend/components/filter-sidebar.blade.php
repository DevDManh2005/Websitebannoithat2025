@props(['categories', 'brands', 'max_price'])

<div class="filter-sidebar card card-glass rounded-4">
  <div class="card-body p-4">
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

      <hr class="border-dashed">

      {{-- GIÁ --}}
      <div class="filter-block mb-4">
        <h6 class="filter-title d-flex align-items-center mb-3">
          <i class="bi bi-cash-stack me-2 text-brand"></i> Giá
        </h6>
        <input type="range" class="form-range form-control-modern" id="price-range" name="price_max"
               min="0" max="{{ $max_price ?? 50000000 }}"
               value="{{ request('price_max', $max_price ?? 50000000) }}">
        <div class="d-flex justify-content-between text-muted small mt-2">
          <span>0 ₫</span>
          <span id="price-range-value">{{ number_format(request('price_max', $max_price ?? 50000000)) }} ₫</span>
        </div>
      </div>

      <hr class="border-dashed">

      {{-- THƯƠNG HIỆU --}}
      <div class="filter-block mb-4">
        <h6 class="filter-title d-flex align-items-center mb-3">
          <i class="bi bi-tags me-2 text-brand"></i> Thương hiệu
        </h6>
        @php $selectedBrands = (array) request('brands', []); @endphp
        <div class="brand-list">
          @foreach($brands as $brand)
            <div class="form-check mb-2">
              <input class="form-check-input js-auto-submit form-control-modern" type="checkbox"
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
        <button type="submit" class="btn btn-brand text-white fw-semibold rounded-pill">
          <i class="bi bi-funnel-fill me-1"></i> Áp dụng lọc
        </button>
      </div>
    </form>
  </div>
</div>

@push('styles')
<style>
  /* =================== Filter Sidebar =================== */
  .filter-sidebar.card-glass {
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid rgba(15, 23, 42, 0.04);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .filter-sidebar.card-glass:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  }
  .filter-title {
    font-weight: 600;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text);
  }
  .filter-block {
    padding-bottom: 0.5rem;
  }
  .border-dashed {
    border-top: 1px dashed #dee2e6;
  }

  /* =================== Category List =================== */
  .filter-category-list li {
    margin-bottom: 0.5rem;
  }
  .filter-category-list .btn-toggle-sub {
    background: none;
    border: 0;
    padding: 0;
    margin-left: 0.25rem;
    color: var(--muted);
    font-size: 0.85rem;
    transition: color 0.2s ease, transform 0.2s ease;
  }
  .filter-category-list .btn-toggle-sub:hover {
    color: var(--brand);
  }
  .filter-category-list .btn-toggle-sub[aria-expanded="true"] i {
    transform: rotate(90deg);
  }
  .filter-category-list .form-check-input {
    border-color: var(--muted);
    transition: border-color 0.2s ease, background-color 0.2s ease;
  }
  .filter-category-list .form-check-input:checked {
    background-color: var(--brand);
    border-color: var(--brand);
  }
  .filter-category-list .form-check-input:focus {
    box-shadow: 0 0 0 0.2rem var(--ring);
  }
  .filter-category-list .form-check-label {
    color: var(--text);
    transition: color 0.2s ease;
  }
  .filter-category-list .form-check-label:hover {
    color: var(--brand);
  }

  /* =================== Price Range =================== */
  .form-range.form-control-modern {
    padding: 0;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
  }
  .form-range.form-control-modern::-webkit-slider-thumb {
    background: var(--brand);
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    width: 20px;
    height: 20px;
    cursor: pointer;
  }
  .form-range.form-control-modern::-moz-range-thumb {
    background: var(--brand);
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    width: 20px;
    height: 20px;
    cursor: pointer;
  }
  .form-range.form-control-modern:focus::-webkit-slider-thumb {
    box-shadow: 0 0 0 0.2rem var(--ring);
  }
  .form-range.form-control-modern:focus::-moz-range-thumb {
    box-shadow: 0 0 0 0.2rem var(--ring);
  }

  /* =================== Brand List =================== */
  .brand-list .form-check-label {
    cursor: pointer;
    color: var(--text);
    transition: color 0.2s ease;
  }
  .brand-list .form-check-label:hover {
    color: var(--brand);
  }
  .brand-list .form-check-input {
    border-color: var(--muted);
    transition: border-color 0.2s ease, background-color 0.2s ease;
  }
  .brand-list .form-check-input:checked {
    background-color: var(--brand);
    border-color: var(--brand);
  }
  .brand-list .form-check-input:focus {
    box-shadow: 0 0 0 0.2rem var(--ring);
  }

  /* =================== Buttons =================== */
  .btn-brand {
    background-color: var(--brand);
    border-color: var(--brand);
    color: #fff;
    padding: 0.5rem 1rem;
    transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
  }
  .btn-brand:hover {
    background-color: var(--brand-600);
    border-color: var(--brand-600);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  }

  /* =================== Responsive Design =================== */
  @media (max-width: 991px) {
    .filter-sidebar.card-glass {
      margin-bottom: 1.5rem;
    }
    .card-body {
      padding: 1.5rem;
    }
    .filter-title {
      font-size: 1rem;
    }
  }

  @media (max-width: 767px) {
    .card-body {
      padding: 1rem;
    }
    .filter-block {
      padding-bottom: 0.25rem;
    }
    .filter-title {
      font-size: 0.95rem;
    }
    .filter-category-list .btn-toggle-sub {
      font-size: 0.8rem;
    }
    .brand-list .form-check-label {
      font-size: 0.9rem;
    }
    .btn-brand {
      padding: 0.4rem 0.8rem;
      font-size: 0.9rem;
    }
    .form-range.form-control-modern::-webkit-slider-thumb {
      width: 16px;
      height: 16px;
    }
    .form-range.form-control-modern::-moz-range-thumb {
      width: 16px;
      height: 16px;
    }
  }

  @media (max-width: 575px) {
    .card-body {
      padding: 0.75rem;
    }
    .filter-title {
      font-size: 0.9rem;
    }
    .filter-category-list li {
      margin-bottom: 0.3rem;
    }
    .filter-category-list .btn-toggle-sub {
      font-size: 0.75rem;
    }
    .filter-category-list .form-check-label,
    .brand-list .form-check-label {
      font-size: 0.85rem;
    }
    .btn-brand {
      padding: 0.35rem 0.7rem;
      font-size: 0.85rem;
    }
    .form-range.form-control-modern::-webkit-slider-thumb {
      width: 14px;
      height: 14px;
    }
    .form-range.form-control-modern::-moz-range-thumb {
      width: 14px;
      height: 14px;
    }
    .d-flex.justify-content-between.text-muted.small {
      font-size: 0.8rem;
    }
  }
</style>
@endpush

@push('scripts-page')
<script>
(function(){
  const form = document.getElementById('filterForm');
  if (!form) return;

  const price = document.getElementById('price-range');
  const priceVal = document.getElementById('price-range-value');
  if (price && priceVal) {
    price.addEventListener('input', e => {
      priceVal.textContent = new Intl.NumberFormat('vi-VN').format(e.target.value) + ' ₫';
    });
    price.addEventListener('change', () => form.submit());
  }

  form.addEventListener('change', function(e) {
    if (e.target.matches('input:is([name="categories[]"], [name="brands[]"])')) {
      setTimeout(() => form.submit(), 150);
    }
  });

  // AOS init
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 600,
      once: true,
      offset: 80
    });
  }
})();
</script>
@endpush