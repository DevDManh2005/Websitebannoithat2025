@php
  $roots = ($sharedCategoriesTree ?? $sharedCategories ?? collect());
  $productActive = request()->routeIs('products.*')
    || request()->routeIs('product.*')
    || request()->routeIs('category.*');
@endphp

<ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} {{ $textColor ?? '' }}"
       href="{{ route('home') }}">Trang chủ</a>
  </li>

  {{-- Mega menu: KHÔNG dùng position-static để mình tự căn vị trí bằng JS --}}
  <li class="nav-item dropdown mega">
    <a class="nav-link dropdown-toggle {{ $productActive ? 'active' : '' }} {{ $textColor ?? '' }}"
       href="{{ route('products.index') }}"
       id="productsDropdown"
       data-bs-toggle="dropdown"
       data-bs-auto-close="outside"
       aria-expanded="false">
      Sản phẩm
    </a>

    {{-- Mega menu (grid 4 cột) --}}
    @include('frontend.components.category-mega', [
      'categories' => $roots
    ])
  </li>

  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('blog.*') ? 'active' : '' }} {{ $textColor ?? '' }}"
       href="{{ route('blog.index') }}" id="blogDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      Tin tức
    </a>
    @include('frontend.components.blog-category-menu')
  </li>

  <li class="nav-item"><a class="nav-link {{ $textColor ?? '' }}" href="#">Giới thiệu</a></li>
  <li class="nav-item"><a class="nav-link {{ $textColor ?? '' }}" href="#">Liên hệ</a></li>
</ul>
