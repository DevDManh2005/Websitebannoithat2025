@php

    $categories = $categories ?? 0;
@endphp
<ul class="dropdown-menu">
    @foreach($categories as $category)
        <li class="{{ $category->children->isNotEmpty() ? 'dropdown-submenu' : '' }}">
            <a class="dropdown-item" href="{{ route('category.show', $category->slug) }}">
                {{ $category->name }}
                @if($category->children->isNotEmpty())
                    <i class="bi bi-chevron-right float-end"></i>
                @endif
            </a>
            @if($category->children->isNotEmpty())
                {{-- Đây là phần đệ quy, tự gọi lại chính nó để hiển thị các cấp con --}}
                @include('frontend.components.category-menu', ['categories' => $category->children])
            @endif
        </li>
    @endforeach
</ul>
