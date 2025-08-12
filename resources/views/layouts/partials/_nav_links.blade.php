@php
  $roots = ($sharedCategoriesTree ?? $sharedCategories ?? collect());
@endphp

<ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} {{ $textColor ?? '' }}"
       href="{{ route('home') }}">Trang chủ</a>
  </li>

  <li class="nav-item dropdown position-static">
    <a class="nav-link dropdown-toggle {{ request()->routeIs('products.*') ? 'active' : '' }} {{ $textColor ?? '' }}"
       href="{{ route('products.index') }}"
       id="productsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Sản phẩm
    </a>

    {{-- DÙNG COMPONENT FLY-OUT --}}
    @include('frontend.components.category-menu', [
      'categories' => $roots,  
      'level' => 0
    ])
  </li>

  <li class="nav-item"><a class="nav-link {{ $textColor ?? '' }}" href="#">Giới Thiệu</a></li>
  <li class="nav-item"><a class="nav-link {{ $textColor ?? '' }}" href="#">Tin tức</a></li>
  <li class="nav-item"><a class="nav-link {{ $textColor ?? '' }}" href="#">Liên hệ</a></li>
</ul>

<style>
/* underline item đang active */
.navbar .nav-link.active{ position:relative; }
.navbar .nav-link.active::after{
  content:""; position:absolute; left:10px; right:10px; bottom:2px; height:3px;
  border-radius:999px; background:linear-gradient(90deg,#ff4d6d,#c1126b);
}

/* tuỳ chọn: canh dropdown chính ngay dưới trigger đẹp hơn */
.navbar .dropdown > .dropdown-menu.cmenu.level-0{
  margin-top: .6rem;
}
</style>
