@php
    // Logic này có thể giữ lại hoặc chuyển vào View Composer nếu muốn
    $productActive = request()->routeIs(['products.*', 'product.*', 'category.*']);
@endphp

<ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center main-navbar-nav">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} {{ $textColor ?? '' }}"
            href="{{ route('home') }}">Trang chủ</a>
    </li>

    <li class="nav-item dropdown mega">
        <a class="nav-link {{ $productActive ? 'active' : '' }}" href="#categories" id="open-category-modal">
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
        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Giới thiệu</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Liên hệ</a>
    </li>
</ul>

@once
    @push('styles')
        <style>
            /* --- CSS CHO NAV LINKS --- */
            :root {
                --text: #2B2623;
                --brand: #A20E38;
                --card: #fff;
            }

            /* Desktop Styles */
            .main-navbar-nav {
                gap: 0.5rem;
            }
            .main-navbar-nav .nav-link {
                font-weight: 600;
                color: var(--text);
                font-size: clamp(0.9rem, 2vw, 1rem);
                padding: 0.5rem 0.75rem;
                position: relative;
                transition: color 0.2s ease;
            }
            .main-navbar-nav .nav-link::after {
                content: '';
                position: absolute;
                bottom: -5px;
                left: 50%;
                transform: translateX(-50%) scaleX(0);
                width: 60%;
                height: 2px;
                background-color: var(--brand);
                transition: transform 0.25s ease-out;
            }
            .main-navbar-nav .nav-link.active::after,
            .main-navbar-nav .nav-link:hover::after,
            .main-navbar-nav .nav-link:focus::after {
                transform: translateX(-50%) scaleX(1);
            }
            .main-navbar-nav .nav-link.active {
                color: var(--brand);
            }
            .main-navbar-nav .nav-link:hover,
            .main-navbar-nav .nav-link:focus {
                color: var(--brand);
            }

            /* Header Home Overrides */
            .header-home .main-navbar-nav .nav-link {
                color: #fff;
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            }
            .header-home .main-navbar-nav .nav-link::after {
                background-color: #fff;
            }
            .header-home .main-navbar-nav .nav-link.active,
            .header-home .main-navbar-nav .nav-link:hover,
            .header-home .main-navbar-nav .nav-link:focus {
                color: #fff;
            }
            .header-home.is-scrolled .main-navbar-nav .nav-link {
                color: var(--text);
                text-shadow: none;
            }
            .header-home.is-scrolled .main-navbar-nav .nav-link::after {
                background-color: var(--brand);
            }
            .header-home.is-scrolled .main-navbar-nav .nav-link.active,
            .header-home.is-scrolled .main-navbar-nav .nav-link:hover,
            .header-home.is-scrolled .main-navbar-nav .nav-link:focus {
                color: var(--brand);
            }

            /* Notification Pill */
            .notification-pill {
                position: absolute;
                top: 0;
                right: 0;
                transform: translate(40%, -40%);
                background-color: var(--brand);
                color: white;
                font-size: clamp(0.55rem, 1.5vw, 0.65rem);
                font-weight: 700;
                border-radius: 99px;
                min-width: 18px;
                height: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0 5px;
                border: 2px solid var(--card);
            }
            .header-home .notification-pill {
                border-color: transparent;
            }
            .header-home.is-scrolled .notification-pill {
                border-color: var(--card);
            }

            /* Responsive Styles */
            @media (max-width: 991.98px) {
                .main-navbar-nav {
                    flex-direction: column;
                    align-items: flex-start !important;
                    margin-top: 0.5rem;
                    padding-top: 0.5rem;
                    border-top: 1px solid rgba(0,0,0,0.1);
                }
                .main-navbar-nav .nav-link {
                    font-size: clamp(0.85rem, 2vw, 0.9rem);
                    padding: 0.6rem 0;
                    width: 100%;
                }
                .main-navbar-nav .nav-link::after {
                    display: none;
                }
                .nav-item.dropdown.mega,
                .nav-item.dropdown {
                    position: static;
                }
                .dropdown-menu.catmega,
                .dropdown-menu.blog-category-menu {
                    position: static !important;
                    width: 100% !important;
                    transform: none !important;
                    border: none !important;
                    box-shadow: none !important;
                    background-color: transparent !important;
                    padding: 0 0 0 1rem;
                    display: none;
                }
                .dropdown-menu.catmega.show,
                .dropdown-menu.blog-category-menu.show {
                    display: block;
                }
                .catmega-grid {
                    grid-template-columns: 1fr;
                    gap: 0.25rem;
                }
                .catmega-col {
                    border-right: none;
                    padding-bottom: 0.5rem;
                }
                .catmega-link {
                    font-size: clamp(0.8rem, 2vw, 0.85rem);
                    padding: 0.4rem 0;
                    white-space: normal;
                }
                .notification-pill {
                    font-size: clamp(0.5rem, 1.5vw, 0.6rem);
                    min-width: 16px;
                    height: 16px;
                    padding: 0 4px;
                }
            }

            @media (max-width: 767.98px) {
                .main-navbar-nav {
                    margin-top: 0.4rem;
                    padding-top: 0.4rem;
                }
                .main-navbar-nav .nav-link {
                    font-size: clamp(0.8rem, 2vw, 0.85rem);
                    padding: 0.5rem 0;
                }
                .catmega-link {
                    font-size: clamp(0.75rem, 2vw, 0.8rem);
                    padding: 0.3rem 0;
                }
                .notification-pill {
                    font-size: clamp(0.45rem, 1.5vw, 0.55rem);
                    min-width: 14px;
                    height: 14px;
                    padding: 0 3px;
                }
            }

            @media (max-width: 575.98px) {
                .main-navbar-nav {
                    margin-top: 0.3rem;
                    padding-top: 0.3rem;
                }
                .main-navbar-nav .nav-link {
                    font-size: clamp(0.75rem, 2vw, 0.8rem);
                    padding: 0.4rem 0;
                }
                .catmega-link {
                    font-size: clamp(0.7rem, 2vw, 0.75rem);
                    padding: 0.25rem 0;
                }
                .notification-pill {
                    font-size: clamp(0.4rem, 1.5vw, 0.5rem);
                    min-width: 12px;
                    height: 12px;
                    padding: 0 2px;
                }
            }
        </style>
    @endpush

    @push('scripts-page')
        <script>
            // Xử lý toggle dropdown trên mobile
            document.addEventListener('DOMContentLoaded', function() {
                const toggles = document.querySelectorAll('.main-navbar-nav .nav-item.dropdown > .nav-link');

                toggles.forEach(toggle => {
                    toggle.addEventListener('click', function(e) {
                        if (window.innerWidth < 992) {
                            e.preventDefault();
                            const dropdownMenu = this.nextElementSibling;
                            if (dropdownMenu) {
                                const isShown = dropdownMenu.classList.contains('show');
                                // Đóng tất cả dropdown khác
                                document.querySelectorAll('.main-navbar-nav .dropdown-menu').forEach(menu => {
                                    if (menu !== dropdownMenu) {
                                        menu.classList.remove('show');
                                    }
                                });
                                // Toggle dropdown hiện tại
                                dropdownMenu.classList.toggle('show');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce