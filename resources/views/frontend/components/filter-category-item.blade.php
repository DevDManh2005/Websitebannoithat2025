@props(['category', 'selectedCategories' => []])

@php
    // Helper function to check if a category or any of its children are selected
    $hasSelectedDesc = function($cat, $selected) use (&$hasSelectedDesc) {
        if (in_array($cat->id, $selected)) {
            return true;
        }
        foreach ($cat->children as $c) {
            if ($hasSelectedDesc($c, $selected)) {
                return true;
            }
        }
        return false;
    };
    
    $isChecked = in_array($category->id, $selectedCategories);
    $hasChild = $category->children->isNotEmpty();
    $shouldOpen = $hasChild && $hasSelectedDesc($category, $selectedCategories);
@endphp

<li class="filter-tree-item">
    <div class="filter-tree-row">
        {{-- Custom Checkbox --}}
        <div class="form-check custom-checkbox-wrapper m-0">
            <input class="form-check-input js-auto-submit" type="checkbox" name="categories[]"
                   value="{{ $category->id }}" id="cat-{{ $category->id }}"
                   {{ $isChecked ? 'checked' : '' }}>
            <label class="form-check-label" for="cat-{{ $category->id }}">
                {{ $category->name }}
                @if($hasChild)
                    {{-- Thêm badge để đếm số lượng danh mục con --}}
                    <span class="badge badge-soft-brand ms-2">{{ $category->children->count() }}</span>
                @endif
            </label>
        </div>

        {{-- Toggle Button --}}
        @if($hasChild)
            <button type="button"
                    class="btn-ghost-toggle"
                    aria-expanded="{{ $shouldOpen ? 'true' : 'false' }}"
                    aria-controls="sub-{{ $category->id }}"
                    data-bs-toggle="collapse"
                    data-bs-target="#sub-{{ $category->id }}" title="Mở rộng">
                <i class="bi {{ $shouldOpen ? 'bi-dash-lg' : 'bi-plus-lg' }}"></i>
            </button>
        @endif
    </div>

    {{-- Submenu --}}
    @if($hasChild)
        <ul class="list-unstyled mt-2 filter-tree-submenu collapse {{ $shouldOpen ? 'show' : '' }}"
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
    /* FIX: Thêm hiệu ứng hover cho cả hàng */
    .filter-tree-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.2rem 0.5rem;
        border-radius: 8px;
        transition: background .15s ease, color .15s ease;
    }
    .filter-tree-row:hover {
        background: rgba(var(--brand-rgb, 162, 14, 56), .06);
        color: var(--brand);
    }
    .filter-tree-row:hover .form-check-label {
        color: var(--brand);
    }

    /* FIX: Căn chỉnh và thêm hiệu ứng cho checkbox */
    .custom-checkbox-wrapper {
        display: flex;
        align-items: center;
        position: relative;
    }
    .custom-checkbox-wrapper .form-check-input {
        opacity: 0;
        width: 1em;
        height: 1em;
        position: relative;
        margin: 0;
    }
    .custom-checkbox-wrapper .form-check-label {
        cursor: pointer;
        user-select: none;
        padding-left: 1.5em; /* Khoảng cách đủ để chứa checkbox giả */
        color: var(--text, #2B2623);
        transition: color .2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .custom-checkbox-wrapper .form-check-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        border: 2px solid #ced4da;
        border-radius: 5px;
        background-color: var(--card, #fff);
        transition: all .15s ease-in-out, transform .15s cubic-bezier(0.2, 0.9, 0.3, 1.2);
    }
    .custom-checkbox-wrapper .form-check-label::after {
        content: "\f26a";
        font-family: "bootstrap-icons";
        position: absolute;
        left: 2px;
        top: 50%;
        transform: translateY(-50%) scale(0.5);
        font-size: 1rem;
        color: white;
        opacity: 0;
        transition: all .15s ease-in-out;
    }
    .custom-checkbox-wrapper .form-check-input:checked + .form-check-label::before {
        background-color: var(--brand, #A20E38);
        border-color: var(--brand, #A20E38);
    }
    .custom-checkbox-wrapper .form-check-input:checked + .form-check-label::after {
        opacity: 1;
        transform: translateY(-50%) scale(1);
    }
    /* Thêm hiệu ứng phóng to khi hover */
    .custom-checkbox-wrapper .form-check-label:hover::before {
        transform: translateY(-50%) scale(1.1);
        border-color: var(--brand);
    }
    .custom-checkbox-wrapper .form-check-input:checked + .form-check-label:hover::before {
        transform: translateY(-50%) scale(1.1);
    }

    /* Toggle Button */
    .btn-ghost-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0;
        background: transparent;
        border: none;
        border-radius: 50%;
        color: var(--muted, #7D726C);
        cursor: pointer;
        transition: background .15s, color .15s, transform .15s;
    }
    .btn-ghost-toggle:hover {
        background-color: rgba(0,0,0, .05);
        color: var(--text, #2B2623);
        transform: scale(1.2);
    }
    .btn-ghost-toggle .bi {
        font-size: 0.8rem;
        font-weight: bold;
    }

    /* Submenu with Tree Line */
    .filter-tree-submenu {
        margin-left: 11px;
        padding-left: 18px;
        position: relative;
        transition: height .25s ease;
    }
    .filter-tree-submenu::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 1px;
        background-color: #e9ecef;
    }
</style>
@endpush
@endonce
