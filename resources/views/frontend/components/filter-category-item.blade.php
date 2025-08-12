@props(['category', 'selectedCategories' => []])

@php
  $hasSelectedDesc = function($cat, $selected) use (&$hasSelectedDesc) {
      if (in_array($cat->id, $selected)) return true;
      foreach ($cat->children as $c) if ($hasSelectedDesc($c, $selected)) return true;
      return false;
  };
  $isChecked  = in_array($category->id, $selectedCategories);
  $hasChild   = $category->children->isNotEmpty();
  $shouldOpen = $hasChild && $hasSelectedDesc($category, $selectedCategories);
@endphp

<li class="filter-category-item">
  <div class="d-flex justify-content-between align-items-center cat-row">
    <div class="form-check m-0">
      <input class="form-check-input js-auto-submit" type="checkbox" name="categories[]"
             value="{{ $category->id }}" id="cat-{{ $category->id }}"
             {{ $isChecked ? 'checked' : '' }}>
      <label class="form-check-label cat-label" for="cat-{{ $category->id }}">{{ $category->name }}</label>
    </div>

    @if($hasChild)
      <button type="button"
              class="btn btn-sm btn-toggle-sub p-0 d-inline-flex align-items-center justify-content-center"
              aria-expanded="{{ $shouldOpen ? 'true' : 'false' }}"
              aria-controls="sub-{{ $category->id }}"
              data-target="#sub-{{ $category->id }}" title="Mở rộng">
        <i class="bi {{ $shouldOpen ? 'bi-dash-lg' : 'bi-plus-lg' }}"></i>
      </button>
    @endif
  </div>

  @if($hasChild)
    <ul class="list-unstyled ms-3 mt-2 filter-submenu collapse {{ $shouldOpen ? 'show' : '' }}"
        id="sub-{{ $category->id }}">
      @foreach($category->children as $child)
        @include('frontend.components.filter-category-item', [
          'category' => $child,
          'selectedCategories' => $selectedCategories
        ])
      @endforeach
    </ul>
  @endif
</li>

@once
@push('styles')
<style>
  .filter-category-item + .filter-category-item{ margin-top:.3rem; }
  .cat-row{ padding:.35rem .25rem; border-radius:.6rem; transition:background .2s ease; }
  .cat-row:hover{ background:#f8f9fa; }
  .cat-label{ margin-left:.35rem; user-select:none; }
  .btn-toggle-sub{
    width:26px;height:26px;border-radius:8px;border:1px solid #e9ecef;background:#fff;
    transition:all .2s ease;
  }
  .btn-toggle-sub:hover{ background:#f3f4f6; }
  .filter-submenu.collapse{ transition: height .25s ease; }
</style>
@endpush
@endonce
