{{-- resources/views/layouts/partials/_nav_actions.blade.php (Phiên bản self-contained) --}}

<div class="d-flex align-items-center ms-lg-3 nav-actions">
    {{-- Search --}}
    <a href="#" class="nav-link search-toggle-btn" title="Tìm kiếm" aria-label="Mở tìm kiếm">
        <i class="bi bi-search fs-5"></i>
        <span class="action-label d-lg-none ">Tìm kiếm</span>
    </a>
    {{-- Auth --}}
    @guest
        {{-- Desktop buttons --}}
        <div class="d-none d-lg-flex align-items-center ms-3">
            <a href="{{ route('login.form') }}" class="btn btn-sm btn-outline-primary">Đăng nhập</a>
            <a href="{{ route('register.form') }}" class="btn btn-sm btn-primary ms-2">Đăng ký</a>
        </div>
        {{-- Mobile links --}}
        <div class="d-lg-none nav-actions-mobile-auth">
            <a href="{{ route('login.form') }}" class="nav-link">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <span class="action-label ms-2">Đăng nhập</span>
            </a>
            <a href="{{ route('register.form') }}" class="nav-link">
                <i class="bi bi-person-plus fs-5"></i>
                <span class="action-label ms-2">Đăng ký</span>
            </a>
        </div>
    @else
        {{-- Logged-in user dropdown --}}
        <div class="dropdown ms-lg-3">
            <a href="#" class="nav-link dropdown-toggle ms-3" data-bs-toggle="dropdown" aria-expanded="false"
                aria-label="Tài khoản">
                <i class="bi bi-person fs-5 "></i>
                <span class="action-label d-lg-none ms-2">Tài khoản</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                @php $roleName = Auth::user()->role->name ?? null; @endphp
                @if($roleName === 'admin')
                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quản trị</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                @elseif($roleName === 'staff')
                    <li><a class="dropdown-item" href="{{ route('staff.dashboard') }}">Trang nhân viên</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                @endif
                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Tài khoản của tôi</a></li>
                <li><a class="dropdown-item" href="{{ route('orders.index') }}">Đơn hàng</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item">Đăng xuất</button>
                    </form>
                </li>
            </ul>
        </div>
    @endguest

    {{-- Wishlist --}}
    <a href="{{ route('wishlist.index') }}" class="nav-link ms-lg-3 position-relative ms-3" aria-label="Yêu thích">
        <i class="bi bi-heart fs-5"></i>
        {{-- <span class="action-label d-lg-none ms-2">Yêu thích</span> --}}
        @auth
            <span id="wishlist-count"
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                style="font-size:.6em; {{ ($sharedWishlistItemCount ?? 0) > 0 ? '' : 'display:none;' }}">
                {{ $sharedWishlistItemCount ?? 0 }}
            </span>
        @endauth
    </a>

    {{-- Cart --}}
    <a href="{{ route('cart.index') }}" class="nav-link ms-lg-3 position-relative ms-3" aria-label="Giỏ hàng">
        <i class="bi bi-cart fs-5"></i>
        {{-- <span class="action-label d-lg-none ms-2">Giỏ hàng</span> --}}
        <span id="cart-count-badge"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-size:.6em; {{ ($sharedCartItemCount ?? 0) > 0 ? '' : 'display:none;' }}">
            {{ $sharedCartItemCount ?? 0 }}
        </span>
    </a>
</div>

{{-- Directive @once đảm bảo CSS và JS chỉ được thêm vào trang một lần duy nhất --}}
@once
    @push('styles')
        <style>
            /* --- CSS CHO NAV ACTIONS COMPONENT --- */

            /* Desktop Styles */
            .nav-actions .nav-link {
                color: var(--text);
            }

            .header-home .nav-actions .nav-link {
                color: #fff;
            }

            .header-home.is-scrolled .nav-actions .nav-link {
                color: var(--text);
            }

            .nav-actions .nav-link:hover {
                color: var(--brand);
            }

            .header-home .nav-actions .nav-link:hover {
                color: #fff;
                /* Giữ màu trắng khi hover trên header home chưa cuộn */
            }

            .header-home.is-scrolled .nav-actions .nav-link:hover {
                color: var(--brand);
                /* Đổi màu brand khi hover trên header đã cuộn */
            }

            /* Nút trên desktop */
            .btn-outline-primary:hover {
                background-color: #A20E38;
                /* Màu xanh đậm khi hover */
                color: #fff;
                /* Chữ trắng khi hover */
                border-color: #8E0D30;
                /* Viền khớp với nền */
                transition: all 0.3s ease;
                /* Hiệu ứng mượt mà */
            }

            .btn-primary:hover {
                background-color: #A20E38;
                /* Màu xanh đậm hơn cho nút chính */
                border-color: #8E0D30;
                /* Viền khớp với nền */
                color: #fff;
                /* Chữ trắng */
                transition: all 0.3s ease;
                /* Hiệu ứng mượt mà */
            }

            /* Liên kết trên mobile */
            .nav-actions-mobile-auth .nav-link:hover {
                background-color: #A20E38;
                /* Nền xám nhạt khi hover */
                color: #8E0D30;
                /* Chữ xanh đậm khi hover */
                transition: all 0.3s ease;
                /* Hiệu ứng mượt mà */
            }

            .nav-actions-mobile-auth .nav-link:hover .action-label {
                color: #A20E38;
                /* Đảm bảo chữ nhãn khớp màu khi hover */
            }

            .nav-actions-mobile-auth .nav-link:hover i {
                color: #A20E38;
                /* Màu biểu tượng khi hover */
            }

            /* Responsive Styles for Mobile Dropdown */
            @media (max-width: 991.98px) {

                /* Biến khu vực actions thành danh sách dọc */
                .navbar-collapse .nav-actions {
                    flex-direction: column;
                    align-items: flex-start !important;
                    width: 100%;
                    margin-left: 0 !important;
                    margin-top: 1rem;
                    padding-top: 1rem;
                    border-top: 1px solid rgba(0, 0, 0, 0.1);
                }

                .navbar-collapse .nav-actions .nav-link {
                    display: flex;
                    /* Căn chỉnh icon và chữ */
                    align-items: center;
                    width: 100%;
                    padding: 0.8rem 0;
                    margin-left: 0 !important;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                }

                .navbar-collapse .nav-actions .nav-link:last-child {
                    border-bottom: none;
                }

                /* Tùy chỉnh cho dropdown tài khoản trên mobile */
                .navbar-collapse .nav-actions .dropdown {
                    width: 100%;
                }

                .navbar-collapse .nav-actions .dropdown .nav-link {
                    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                }

                .navbar-collapse .nav-actions .dropdown-toggle::after {
                    margin-left: auto;
                    /* Đẩy mũi tên dropdown ra cuối */
                }

                /* Định dạng các nút Đăng nhập/Ký trên mobile */
                .navbar-collapse .nav-actions-mobile-auth {
                    width: 100%;
                }

                .navbar-collapse .nav-actions-mobile-auth .nav-link {
                    display: flex;
                    align-items: center;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                }
            }
        </style>
    @endpush
@endonce