@props(['categories', 'brands', 'max_price'])

<div class="card shadow-sm border-0 filter-sidebar">
  <div class="card-body">
    <form id="filterForm" action="{{ route('products.index') }}" method="GET">
      {{-- Giữ lại mọi tham số khác (kể cả sort, on_sale, keyword...) --}}
      @foreach(request()->except(['categories', 'brands', 'price_max', 'page']) as $key => $value)
        @if(is_array($value))
          @foreach($value as $v)
            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
          @endforeach
        @else
          <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
      @endforeach

      {{-- Danh mục --}}
      <div class="mb-4">
        <h5 class="fw-bold mb-3">Danh mục</h5>
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

      {{-- Giá --}}
      <div class="mb-4">
        <h5 class="fw-bold mb-3">Giá</h5>
        <input type="range" class="form-range" id="price-range" name="price_max"
               min="0" max="{{ $max_price ?? 50000000 }}"
               value="{{ request('price_max', $max_price ?? 50000000) }}">
        <div class="d-flex justify-content-between text-muted small mt-2">
          <span>0 ₫</span>
          <span id="price-range-value">{{ number_format(request('price_max', $max_price ?? 50000000)) }} ₫</span>
        </div>
      </div>

      <hr>

      {{-- Thương hiệu --}}
      <div class="mb-4">
        <h5 class="fw-bold mb-3">Thương hiệu</h5>
        @php $selectedBrands = (array) request('brands', []); @endphp
        @foreach($brands as $brand)
          <div class="form-check">
            <input class="form-check-input js-auto-submit" type="checkbox"
                   name="brands[]" value="{{ $brand->id }}"
                   id="brand-{{ $brand->id }}"
                   {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}>
            <label class="form-check-label" for="brand-{{ $brand->id }}">{{ $brand->name }}</label>
          </div>
        @endforeach
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Lọc</button>
      </div>
    </form>
  </div>
</div>

@push('styles')
<style>
  .filter-category-list .form-check-label { cursor: pointer; }
  .btn-toggle-sub i{ transition: transform .25s ease; }
  .btn-toggle-sub[aria-expanded="true"] i{ transform: rotate(45deg); } /* plus -> minus */
</style>
@endpush

@push('scripts-page')
<script>
(function(){
  const form = document.getElementById('filterForm');
  if(!form) return;

  // hiển thị giá slider
  const price = document.getElementById('price-range');
  const priceVal = document.getElementById('price-range-value');
  if(price && priceVal){
    price.addEventListener('input', e => {
      priceVal.textContent = new Intl.NumberFormat('vi-VN').format(e.target.value) + ' ₫';
    });
    // auto submit khi thả chuột
    price.addEventListener('change', () => form.submit());
  }

  // auto submit khi tick checkbox (danh mục & brand)
  form.addEventListener('change', function(e){
    if(e.target.matches('input[name="categories[]"], input[name="brands[]"]')){
      setTimeout(()=> form.submit(), 120);
    }
  });

  // toggle submenu (đổi icon +/−)
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-toggle-sub');
    if(!btn) return;
    const targetSel = btn.getAttribute('data-target');
    const target = document.querySelector(targetSel);
    if(!target) return;

    const c = bootstrap.Collapse.getOrCreateInstance(target, {toggle:false});
    const expanded = btn.getAttribute('aria-expanded') === 'true';
    expanded ? c.hide() : c.show();
    btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');

    const icon = btn.querySelector('i');
    if(icon){
      icon.classList.toggle('bi-plus-lg', expanded);
      icon.classList.toggle('bi-dash-lg', !expanded);
    }
  });
})();
</script>
@endpush

