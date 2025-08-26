@props(['category', 'selectedCategories' => []])

@php
    // Kiểm tra nhánh có chứa mục đang chọn
    $hasSelectedDesc = function($cat, $selected) use (&$hasSelectedDesc) {
        if (in_array($cat->id, $selected, true)) return true;
        foreach ($cat->children as $c) {
            if ($hasSelectedDesc($c, $selected)) return true;
        }
        return false;
    };

    $isChecked  = in_array($category->id, $selectedCategories, true);
    $hasChild   = $category->children->isNotEmpty();
    $shouldOpen = $hasChild && $hasSelectedDesc($category, $selectedCategories);
@endphp

<li class="filter-tree-item">
  @if($hasChild)
    <details class="tree-node" {{ $shouldOpen ? 'open' : '' }}>
      <summary class="filter-tree-row">
        <div class="form-check custom-checkbox-wrapper m-0">
          <input
            class="form-check-input js-auto-submit"
            type="checkbox"
            name="categories[]"
            value="{{ $category->id }}"
            id="cat-{{ $category->id }}"
            {{ $isChecked ? 'checked' : '' }}
          >
          <label class="form-check-label" for="cat-{{ $category->id }}">
            {{ $category->name }}
            <span class="badge badge-soft-brand ms-2">{{ $category->children->count() }}</span>
          </label>
        </div>
        <i class="bi bi-chevron-right chevron"></i>
      </summary>

      <ul class="list-unstyled mt-2 filter-tree-submenu">
        @foreach($category->children as $child)
          @include('frontend.components.filter-category-item', [
            'category' => $child,
            'selectedCategories' => $selectedCategories
          ])
        @endforeach
      </ul>
    </details>
  @else
    <div class="filter-tree-row no-child">
      <div class="form-check custom-checkbox-wrapper m-0">
        <input
          class="form-check-input js-auto-submit"
          type="checkbox"
          name="categories[]"
          value="{{ $category->id }}"
          id="cat-{{ $category->id }}"
          {{ $isChecked ? 'checked' : '' }}
        >
        <label class="form-check-label" for="cat-{{ $category->id }}">
          {{ $category->name }}
        </label>
      </div>
    </div>
  @endif
</li>

@once
  @push('styles')
  <style>
    /* ===== Node row ===== */
    .filter-tree-row{
      display:flex; align-items:center; justify-content:space-between;
      padding:.2rem .5rem; border-radius:8px;
      transition:background .15s ease, color .15s ease;
      cursor:default; user-select:none;
    }
    .filter-tree-row:hover{ background:rgba(var(--brand-rgb,162,14,56),.06); color:var(--brand) }
    .filter-tree-row:hover .form-check-label{ color:var(--brand) }

    /* ===== details/summary chevron ===== */
    .tree-node{ position:relative; padding-left:.25rem }
    .tree-node > summary{ list-style:none; cursor:pointer }
    .tree-node > summary::-webkit-details-marker{ display:none }
    .tree-node .chevron{ color:var(--muted,#7D726C); transition:transform .2s ease }
    .tree-node[open] .chevron{ transform:rotate(90deg) }

    /* ===== Checkbox custom ===== */
    .custom-checkbox-wrapper{ display:flex; align-items:center; position:relative }
    .custom-checkbox-wrapper .form-check-input{
      opacity:0; width:1em; height:1em; position:relative; margin:0;
    }
    .custom-checkbox-wrapper .form-check-label{
      cursor:pointer; user-select:none; padding-left:1.5em;
      color:var(--text,#2B2623); transition:color .2s; display:inline-flex; align-items:center; gap:.25rem;
    }
    .custom-checkbox-wrapper .form-check-label::before{
      content:''; position:absolute; left:0; top:50%; transform:translateY(-50%);
      width:18px; height:18px; border:2px solid #ced4da; border-radius:5px; background:var(--card,#fff);
      transition:all .15s ease-in-out, transform .15s cubic-bezier(.2,.9,.3,1.2);
    }
    .custom-checkbox-wrapper .form-check-label::after{
      content:"\f26a"; font-family:"bootstrap-icons"; position:absolute; left:2px; top:50%;
      transform:translateY(-50%) scale(.5); font-size:1rem; color:#fff; opacity:0; transition:all .15s;
    }
    .custom-checkbox-wrapper .form-check-input:checked + .form-check-label::before{
      background:var(--brand,#A20E38); border-color:var(--brand,#A20E38);
    }
    .custom-checkbox-wrapper .form-check-input:checked + .form-check-label::after{
      opacity:1; transform:translateY(-50%) scale(1);
    }
    .custom-checkbox-wrapper .form-check-label:hover::before{
      transform:translateY(-50%) scale(1.1); border-color:var(--brand);
    }

    /* ===== Sub tree (indent + guideline) ===== */
    .filter-tree-submenu{ margin-left:11px; padding-left:18px; position:relative }
    .filter-tree-submenu::before{
      content:''; position:absolute; inset:0 auto 0 0; width:1px; background:#e9ecef;
    }
  </style>
  @endpush
@endonce
