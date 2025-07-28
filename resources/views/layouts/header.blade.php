<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">Nội Thất Laravel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Các mục menu bên trái --}}
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Trang chủ</a>
                </li>

                {{-- Dropdown Danh mục sản phẩm --}}
                @if(isset($sharedCategories) && $sharedCategories->isNotEmpty())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarCategoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sản phẩm
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarCategoryDropdown">
                        @foreach($sharedCategories as $category)
                            <li><h6 class="dropdown-header">{{ $category->name }}</h6></li>
                            @if($category->children->isNotEmpty())
                                @foreach($category->children as $child)
                                    <li><a class="dropdown-item ps-4" href="{{ route('category.show', $child->slug) }}">{{ $child->name }}</a></li>
                                @endforeach
                            @endif
                            @if(!$loop->last)
                                <li><hr class="dropdown-divider"></li>
                            @endif
                        @endforeach
                    </ul>
                </li>
                @endif
            </ul>

            {{-- Các mục menu bên phải (Đăng nhập/Tài khoản/Giỏ hàng) --}}
            <ul class="navbar-nav ms-auto align-items-center">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login.form') }}">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register.form') }}">Đăng ký</a>
                    </li>
                @else
                    {{-- Icon Yêu thích --}}
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('wishlist.index') }}" title="Danh sách yêu thích">
                            <i class="fas fa-heart"></i>
                            @if(isset($sharedWishlistItemCount) && $sharedWishlistItemCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $sharedWishlistItemCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    {{-- Icon Giỏ hàng --}}
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}" title="Giỏ hàng">
                            <i class="fas fa-shopping-cart"></i>
                            @if(isset($sharedCartItemCount) && $sharedCartItemCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $sharedCartItemCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                      <li><a class="dropdown-item" href="{{ route('orders.index') }}">Lịch sử mua hàng</a></li>

                    {{-- Dropdown Tài khoản người dùng --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            👋 {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}">Tài khoản của tôi</a></li>
                            @php $role = Auth::user()->role->name; @endphp
                            @if($role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                            @elseif($role === 'staff')
                                <li><a class="dropdown-item" href="{{ route('staff.dashboard') }}">Staff Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
