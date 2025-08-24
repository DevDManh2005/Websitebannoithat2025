@php
    // Logic này có thể giữ lại hoặc chuyển vào View Composer nếu muốn
    $productActive = request()->routeIs(['products.*', 'product.*', 'category.*']);

    // Biến $supportUnread sẽ tự động được cung cấp bởi View Composer
@endphp

<ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center main-navbar-nav">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} {{ $textColor ?? '' }}"
            href="{{ route('home') }}">Trang chủ</a>
    </li>

    <li class="nav-item dropdown mega">
        <a class="nav-link" href="#categories" id="open-category-modal">
            Sản phẩm
        </a>
        @include('frontend.components.category-mega', ['categories' => $sharedCategoriesTree ?? collect()])
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="#blog-categories"
            id="open-blog-modal">
            Tin tức
        </a>
        @include('frontend.components.blog-category-menu')
    </li>

    <li class="nav-item">
        <a class="nav-link position-relative {{ request()->routeIs('support.*') ? 'active' : '' }} {{ $textColor ?? '' }}"
            href="{{ route('support.index') }}">
            Hỗ trợ
            @if(auth()->check() && isset($supportOpenTickets) && $supportOpenTickets > 0)
                <span class="notification-pill">{{ $supportOpenTickets }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('about') }}">Giới thiệu</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('contact') }}">Liên hệ</a>
    </li>
</ul>

@once
    @push('styles')
        <style>
            /* --- CSS MẶC ĐỊNH CHO NAV LINKS (DESKTOP) --- */
            .main-navbar-nav .nav-link {
                font-weight: 600;
                color: var(--text, #2B2623);
                padding: 0.5rem 1rem;
                position: relative;
                transition: color .2s ease;
            }

            /* Underline effect */
            .main-navbar-nav .nav-link::after {
                content: '';
                position: absolute;
                bottom: -5px;
                left: 50%;
                transform: translateX(-50%) scaleX(0);
                width: 60%;
                height: 2px;
                background-color: var(--brand);
                transition: transform .25s ease-out;
            }
            .main-navbar-nav .nav-link.active::after,
            .main-navbar-nav .nav-link:hover::after {
                transform: translateX(-50%) scaleX(1);
            }
            .main-navbar-nav .nav-link.active {
                color: var(--brand);
            }
            .main-navbar-nav .nav-link:hover {
                color: var(--brand);
            }

            /* --- Overrides cho Header Trang chủ (trong suốt) --- */
            .header-home .main-navbar-nav .nav-link {
                color: #fff;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            }
            .header-home .main-navbar-nav .nav-link::after {
                background-color: #fff;
            }
            .header-home .main-navbar-nav .nav-link.active,
            .header-home .main-navbar-nav .nav-link:hover {
                color: #fff; /* Giữ màu trắng khi hover/active */
            }
            /* Khi cuộn, link chữ chuyển màu tối */
            .header-home.is-scrolled .main-navbar-nav .nav-link {
                color: var(--text, #2B2623);
            }
            .header-home.is-scrolled .main-navbar-nav .nav-link::after {
                background-color: var(--brand);
            }
            .header-home.is-scrolled .main-navbar-nav .nav-link.active,
            .header-home.is-scrolled .main-navbar-nav .nav-link:hover {
                color: var(--brand);
            }

            /* --- Notification Pill --- */
            .notification-pill {
                position: absolute;
                top: 0; right: 0;
                transform: translate(40%, -40%);
                background-color: var(--brand);
                color: white;
                font-size: 0.65rem;
                font-weight: 700;
                border-radius: 99px;
                min-width: 18px; height: 18px;
                display: inline-flex;
                align-items: center; justify-content: center;
                padding: 0 5px;
                border: 2px solid var(--card, #fff);
            }
            .header-home .notification-pill {
                border-color: transparent;
            }
            .header-home.is-scrolled .notification-pill {
                border-color: var(--card, #fff);
            }

            /* === CSS RESPONSIVE CHO NAV LINKS (MOBILE DROPDOWN) === */
            @media (max-width: 991.98px) {
                /* 1. Tắt hiệu ứng gạch chân không cần thiết trên mobile */
                .main-navbar-nav .nav-link::after {
                    display: none;
                }
                
                /* 2. Tùy chỉnh lại khoảng cách cho menu dọc */
                .main-navbar-nav .nav-link {
                    padding: 0.8rem 0;
                    width: 100%;
                }

                /* 3. Xử lý Mega Menu và Dropdown để hiển thị đúng trong menu dọc */
                .nav-item.dropdown.mega,
                .nav-item.dropdown {
                    position: static;
                }

                /* Biến Mega Menu thành một khối nội dung tĩnh, không còn bay lơ lửng */
                .dropdown-menu.catmega,
                .dropdown-menu.blog-category-menu {
                    position: static !important;
                    width: auto !important;
                    transform: none !important;
                    border: none !important;
                    box-shadow: none !important;
                    background-color: transparent !important;
                    padding: 0 0 0 1rem; /* Thụt vào để tạo cấp bậc */
                    display: none; /* Mặc định ẩn, sẽ được JS bật/tắt */
                }

                /* Khi được JS bật class 'show' */
                .dropdown-menu.catmega.show,
                .dropdown-menu.blog-category-menu.show {
                    display: block;
                }
                
                /* Định dạng lại lưới của Mega Menu cho mobile */
                .catmega-grid {
                    grid-template-columns: 1fr; /* Chỉ 1 cột */
                    gap: 0;
                }
                .catmega-col {
                    border-right: none;
                    padding-bottom: 0.5rem;
                }
                .catmega-link {
                    padding: 6px 0;
                    white-space: normal;
                }
            }
        </style>
    @endpush

    @push('scripts-page')
        <script>
            // Xử lý việc click để mở/đóng dropdown trên mobile
            document.addEventListener('DOMContentLoaded', function() {
                const toggles = document.querySelectorAll('.main-navbar-nav .nav-item.dropdown > .nav-link');

                toggles.forEach(toggle => {
                    toggle.addEventListener('click', function(e) {
                        // Chỉ chạy trên màn hình mobile
                        if (window.innerWidth < 992) {
                            e.preventDefault();
                            const dropdownMenu = this.nextElementSibling;
                            if (dropdownMenu) {
                                dropdownMenu.classList.toggle('show');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce