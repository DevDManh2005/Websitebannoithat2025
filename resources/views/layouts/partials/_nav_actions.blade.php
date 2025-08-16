{{-- Các nút: Tìm kiếm, Tài khoản, Wishlist, Giỏ hàng --}}
@php $iconColor = $iconColor ?? ''; @endphp

<div class="d-flex align-items-center ms-lg-3">
  {{-- Search (overlay) --}}
  <button type="button" class="btn nav-link search-toggle-btn" title="Tìm kiếm" aria-label="Mở tìm kiếm">
    <i class="bi bi-search fs-5 {{ $iconColor }}"></i>
  </button>

  {{-- Auth --}}
@guest
  <a href="{{ route('login.form') }}"
     class="btn btn-sm {{ $iconColor==='text-white' ? 'btn-outline-light' : 'btn-outline-primary' }} ms-3">Đăng nhập</a>
  <a href="{{ route('register.form') }}"
     class="btn btn-sm {{ $iconColor==='text-white' ? 'btn-light' : 'btn-primary' }} ms-2">Đăng ký</a>
@else
  @php $roleName = Auth::user()->role->name ?? null; @endphp
  <div class="dropdown ms-3">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Tài khoản">
      <i class="bi bi-person fs-5 {{ $iconColor }}"></i>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      @if($roleName === 'admin')
        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quản trị</a></li>
        <li><hr class="dropdown-divider"></li>
      @elseif($roleName === 'staff')
        <li><a class="dropdown-item" href="{{ route('staff.dashboard') }}">Trang nhân viên</a></li>
        <li><hr class="dropdown-divider"></li>
      @endif

      <li><a class="dropdown-item" href="{{ route('profile.show') }}">Tài khoản của tôi</a></li>
      <li><a class="dropdown-item" href="{{ route('orders.index') }}">Đơn hàng</a></li>
      <li><hr class="dropdown-divider"></li>
      <li>
        <form action="{{ route('logout') }}" method="POST">@csrf
          <button type="submit" class="dropdown-item">Đăng xuất</button>
        </form>
      </li>
    </ul>
  </div>
@endguest

  {{-- Wishlist --}}
  <a href="{{ route('wishlist.index') }}" class="nav-link ms-3 position-relative" aria-label="Yêu thích">
    <i class="bi bi-heart fs-5 {{ $iconColor }}"></i>
    @auth
      <span id="wishlist-count"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-size:.6em; {{ ($sharedWishlistItemCount ?? 0) > 0 ? '' : 'display:none;' }}">
        {{ $sharedWishlistItemCount ?? 0 }}
      </span>
    @endauth
  </a>

  {{-- Cart --}}
  <a href="{{ route('cart.index') }}" class="nav-link ms-3 position-relative" aria-label="Giỏ hàng">
    <i class="bi bi-cart fs-5 {{ $iconColor }}"></i>
    @auth
      @if(($sharedCartItemCount ?? 0) > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6em;">
          {{ $sharedCartItemCount }}
        </span>
      @endif
    @endauth
  </a>
</div>
