@props(['categories', 'brands', 'max_price'])

@php
  // Giữ lại các query khác ngoài bộ lọc chuẩn
  $keepQuery = request()->except(['categories','brands','price_max','page']);
  $clearUrl  = route('products.index', $keepQuery);
  $selectedCategories = (array) request('categories', []);
  $selectedBrands     = (array) request('brands', []);
  $priceMax           = (int) request('price_max', $max_price ?? 50000000);
@endphp

<div class="filter-sidebar card card-glass rounded-4">
  <div class="card-body p-4">

    {{-- ĐANG LỌC (chips) --}}
    @if(!empty($selectedCategories) || !empty($selectedBrands) || request()->filled('price_max'))
      <div class="mb-3">
        <div class="small text-muted mb-2 d-flex align-items-center gap-2">
          <i class="bi bi-funnel"></i> Đang lọc
        </div>
        <div class="d-flex flex-wrap gap-2">
          {{-- Chips: categories --}}
          @foreach($selectedCategories as $cid)
            @php
              $qs = request()->except('page');
              $arr = (array)($qs['categories'] ?? []);
              $qs['categories'] = array_values(array_filter($arr, fn($v)=> (string)$v !== (string)$cid));
              if (empty($qs['categories'])) unset($qs['categories']);
              $url = route('products.index', $qs);
            @endphp
            <a href="{{ $url }}" class="chip">
              <i class="bi bi-x me-1"></i>
              DM #{{ $cid }}
            </a>
          @endforeach

          {{-- Chips: brands --}}
          @foreach($selectedBrands as $bid)
            @php
              $qs = request()->except('page');
              $arr = (array)($qs['brands'] ?? []);
              $qs['brands'] = array_values(array_filter($arr, fn($v)=> (string)$v !== (string)$bid));
              if (empty($qs['brands'])) unset($qs['brands']);
              $url = route('products.index', $qs);
            @endphp
            <a href="{{ $url }}" class="chip chip-alt">
              <i class="bi bi-x me-1"></i>
              Thương hiệu #{{ $bid }}
            </a>
          @endforeach

          {{-- Chip: price --}}
          @if(request()->filled('price_max'))
            @php
              $qs = request()->except(['page','price_max']);
              $url = route('products.index', $qs);
            @endphp
            <a href="{{ $url }}" class="chip chip-price">
              <i class="bi bi-x me-1"></i>
              Giá ≤ {{ number_format($priceMax) }} ₫
            </a>
          @endif

          <a href="{{ $clearUrl }}" class="chip-clear ms-auto">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Xóa lọc
          </a>
        </div>
        <hr class="border-dashed mt-3 mb-0">
      </div>
    @endif

    <form id="filterForm" action="{{ route('products.index') }}" method="GET">
      {{-- Giữ lại các query khác --}}
      @foreach($keepQuery as $key => $value)
        @if(is_array($value))
          @foreach($value as $v)
            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
          @endforeach
        @else
          <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
      @endforeach

      {{-- DANH MỤC --}}
      <details class="filter-sec mb-3" open>
        <summary class="filter-sum">
          <span class="d-flex align-items-center">
            <i class="bi bi-list-ul me-2 text-brand"></i> Danh mục
          </span>
          <i class="bi bi-chevron-right arrow"></i>
        </summary>

        <ul class="list-unstyled filter-category-list mt-3">
          @foreach($categories as $category)
            @include('frontend.components.filter-category-item', [
              'category' => $category,
              'selectedCategories' => $selectedCategories
            ])
          @endforeach
        </ul>
      </details>

      {{-- GIÁ --}}
      <details class="filter-sec mb-3" open>
        <summary class="filter-sum">
          <span class="d-flex align-items-center">
            <i class="bi bi-cash-stack me-2 text-brand"></i> Giá
          </span>
          <i class="bi bi-chevron-right arrow"></i>
        </summary>

        <div class="mt-3">
          <input
            type="range"
            class="form-range form-control-modern"
            id="price-range"
            name="price_max"
            min="0"
            max="{{ $max_price ?? 50000000 }}"
            value="{{ $priceMax }}"
          >
          <div class="d-flex justify-content-between text-muted small mt-2">
            <span>0 ₫</span>
            <span id="price-range-value">{{ number_format($priceMax) }} ₫</span>
          </div>
        </div>
      </details>

      {{-- THƯƠNG HIỆU --}}
      <details class="filter-sec mb-4" open>
        <summary class="filter-sum">
          <span class="d-flex align-items-center">
            <i class="bi bi-tags me-2 text-brand"></i> Thương hiệu
          </span>
          <i class="bi bi-chevron-right arrow"></i>
        </summary>

        <div class="brand-list mt-3">
          @foreach($brands as $brand)
            <div class="form-check mb-2">
              <input
                class="form-check-input js-auto-submit form-control-modern"
                type="checkbox"
                name="brands[]"
                value="{{ $brand->id }}"
                id="brand-{{ $brand->id }}"
                {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}
              >
              <label class="form-check-label" for="brand-{{ $brand->id }}">
                {{ $brand->name }}
              </label>
            </div>
          @endforeach
        </div>
      </details>

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
  /* ===== Card ===== */
  .filter-sidebar.card-glass{
    background:linear-gradient(180deg,rgba(255,255,255,.92),rgba(255,255,255,.98));
    border-radius:var(--radius);
    box-shadow:var(--shadow);
    border:1px solid rgba(15,23,42,.04);
    transition:transform .25s, box-shadow .25s;
  }
  .filter-sidebar.card-glass:hover{ transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,.1) }

  .border-dashed{ border-top:1px dashed #dee2e6 }

  /* ===== Chips ===== */
  .chip, .chip-alt, .chip-price, .chip-clear{
    display:inline-flex; align-items:center; gap:.25rem;
    padding:.25rem .6rem; border-radius:999px; font-size:.85rem;
    text-decoration:none; border:1px solid transparent;
  }
  .chip{ background:rgba(var(--brand-rgb,162,14,56),.08); color:var(--brand); border-color:rgba(var(--brand-rgb,162,14,56),.15) }
  .chip-alt{ background:#f5f7fb; color:#334155; border-color:#e5e7eb }
  .chip-price{ background:#f0fdf4; color:#166534; border-color:#bbf7d0 }
  .chip-clear{ margin-left:auto; background:#fff; color:#334155; border-color:#e5e7eb }
  .chip:hover, .chip-alt:hover, .chip-price:hover, .chip-clear:hover{ filter:brightness(.95) }

  /* ===== Disclosure (details/summary) ===== */
  .filter-sec{
    border-bottom:1px dashed #e5e7eb; padding-bottom:.5rem;
  }
  .filter-sec:last-of-type{ border-bottom:0 }
  .filter-sum{
    list-style:none; cursor:pointer; user-select:none;
    display:flex; align-items:center; justify-content:space-between;
    font-weight:600; font-size:1.05rem; color:var(--text);
  }
  .filter-sum::-webkit-details-marker{ display:none }
  .filter-sum .arrow{ transition:transform .2s ease; color:var(--muted) }
  .filter-sec[open] .filter-sum .arrow{ transform:rotate(90deg) }

  /* ===== Category list ===== */
  .filter-category-list li{ margin-bottom:.5rem }
  .filter-category-list .form-check-input{
    border-color:var(--muted); transition:border-color .2s, background-color .2s
  }
  .filter-category-list .form-check-input:checked{ background-color:var(--brand); border-color:var(--brand) }
  .filter-category-list .form-check-input:focus{ box-shadow:0 0 0 .2rem var(--ring) }
  .filter-category-list .form-check-label{ color:var(--text); transition:color .2s }
  .filter-category-list .form-check-label:hover{ color:var(--brand) }

  /* ===== Range ===== */
  .form-range.form-control-modern{ padding:0; height:8px; background:#e9ecef; border-radius:4px }
  .form-range.form-control-modern::-webkit-slider-thumb,
  .form-range.form-control-modern::-moz-range-thumb{
    background:var(--brand); border:2px solid #fff; box-shadow:0 2px 4px rgba(0,0,0,.2);
    width:20px; height:20px; cursor:pointer
  }
  .form-range.form-control-modern:focus::-webkit-slider-thumb,
  .form-range.form-control-modern:focus::-moz-range-thumb{ box-shadow:0 0 0 .2rem var(--ring) }

  /* ===== Brand list ===== */
  .brand-list .form-check-label{ cursor:pointer; color:var(--text); transition:color .2s }
  .brand-list .form-check-label:hover{ color:var(--brand) }
  .brand-list .form-check-input{ border-color:var(--muted); transition:border-color .2s, background-color .2s }
  .brand-list .form-check-input:checked{ background-color:var(--brand); border-color:var(--brand) }
  .brand-list .form-check-input:focus{ box-shadow:0 0 0 .2rem var(--ring) }

  /* ===== Button ===== */
  .btn-brand{
    background-color:var(--brand); border-color:var(--brand); color:#fff;
    padding:.5rem 1rem; transition:transform .15s, box-shadow .15s, background .15s
  }
  .btn-brand:hover{ background-color:var(--brand-600); border-color:var(--brand-600);
    transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.08) }

  /* ===== Responsive ===== */
  @media (max-width:991px){
    .filter-sidebar.card-glass{ margin-bottom:1.5rem }
    .card-body{ padding:1.5rem }
    .filter-sum{ font-size:1rem }
  }
  @media (max-width:767px){
    .card-body{ padding:1rem }
    .chip,.chip-alt,.chip-price,.chip-clear{ font-size:.8rem }
  }
  @media (max-width:575px){
    .card-body{ padding:.75rem }
    .filter-sum{ font-size:.95rem }
  }
</style>
@endpush

@push('scripts-page')
<script>
(function(){
  const form = document.getElementById('filterForm');
  if (!form) return;

  // Hiển thị giá khi kéo slider + auto submit khi thả
  const price = document.getElementById('price-range');
  const priceVal = document.getElementById('price-range-value');
  if (price && priceVal) {
    price.addEventListener('input', e => {
      priceVal.textContent = new Intl.NumberFormat('vi-VN').format(e.target.value) + ' ₫';
    });
    price.addEventListener('change', () => form.submit());
  }

  // Auto submit khi tick danh mục / thương hiệu
  form.addEventListener('change', function(e){
    if (e.target.matches('input:is([name="categories[]"],[name="brands[]"])')) {
      setTimeout(()=>form.submit(), 120);
    }
  });

  if (typeof AOS !== 'undefined') {
    AOS.init({ duration:600, once:true, offset:80 });
  }
})();
</script>
@endpush
