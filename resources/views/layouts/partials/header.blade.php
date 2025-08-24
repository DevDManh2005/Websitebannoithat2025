
@php
    $isHomePage = request()->routeIs('home');
@endphp

<header class="@if($isHomePage) header-home navbar-transparent @else header-internal sticky-top shadow-sm @endif"
        @if($isHomePage) style="position: absolute; top: 0; left: 0; width: 100%; z-index: 1030;" @endif
        role="banner">

    {{-- Top bar: Chỉ hiển thị ở các trang trong trên desktop, tùy chọn hiển thị trên tablet/mobile --}}
    @if(!$isHomePage)
        <div class="top-bar py-2 d-none d-md-block bg-light border-bottom">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center text-secondary small gap-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt-fill me-2 text-brand" aria-hidden="true"></i>
                        <span>{{ $settings['contact_address'] ?? 'Địa chỉ liên hệ' }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-telephone-fill me-2 text-brand" aria-hidden="true"></i>
                        <span>{{ $settings['contact_phone'] ?? '1900 1234' }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-secondary">Theo dõi:</span>
                    <a href="{{ $settings['social_facebook'] ?? '#' }}" class="text-secondary" aria-label="Facebook" rel="noopener noreferrer" target="_blank">
                        <i class="bi bi-facebook" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Main navbar --}}
    <nav class="navbar navbar-expand-lg py-2 @if($isHomePage) navbar-dark @else navbar-light bg-white @endif" aria-label="Main navigation">
        <div class="container">
            {{-- Brand / Logo --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}" aria-label="{{ $settings['site_name'] ?? 'Home' }}">
                @if($isHomePage)
                    @if(!empty($settings['logo_light']))
                        <img src="{{ asset('storage/' . $settings['logo_light']) }}" alt="Logo" class="logo-light" style="height: clamp(80px, 10vw, 100px); max-height: 100px; width: auto;">
                    @endif
                    @if(!empty($settings['logo_dark']))
                        <img src="{{ asset('storage/' . $settings['logo_dark']) }}" alt="Logo" class="logo-dark" style="height: clamp(80px, 10vw, 100px); max-height: 100px; width: auto; display: none;">
                    @else
                        <span class="fs-4 fw-bold text-white logo-text">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                    @endif
                @else
                    @if(!empty($settings['logo_dark']))
                        <img src="{{ asset('storage/' . $settings['logo_dark']) }}" alt="Logo" style="height: clamp(40px, 6vw, 50px); max-height: 50px; width: auto;">
                    @else
                        <span class="fs-4 fw-bold text-dark">{{ $settings['site_name'] ?? 'EternaHome' }}</span>
                    @endif
                @endif
            </a>

            {{-- Toggler (mobile) --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Nav links + actions --}}
            <div class="collapse navbar-collapse" id="main-navbar">
                @include('layouts.partials._nav_links', ['textColor' => $isHomePage ? 'text-white' : ''])
                @include('layouts.partials._nav_actions', ['iconColor' => $isHomePage ? 'text-white' : ''])
            </div>
        </div>
    </nav>
</header>

@once
    @push('styles')
        <style>
            /* === CSS CHUNG CHO CẢ HAI HEADER === */
            :root { 
                --brand: #A20E38;
                --text: #2B2623;
                --card: #FFFFFF;
                --muted: #7D726C;
                --radius: 12px;
            }

            /* === CSS RIÊNG CHO HEADER-INTERNAL === */
            .header-internal .top-bar a:hover,
            .header-internal .top-bar a:focus { color: var(--brand) !important; }
            .header-internal .navbar .nav-link.active { color: var(--brand) !important; }

            /* === CSS RIÊNG CHO HEADER-HOME (TRONG SUỐT) === */
            .header-home {
                transition: background-color 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
            }
            .header-home .logo-dark { display: none; }
            .header-home .logo-light { display: block; }
            .header-home .nav-link, .header-home .logo-text { color: rgba(255, 255, 255, 0.96) !important; }
            .header-home .nav-link .bi, .header-home .nav-link i { color: rgba(255, 255, 255, 0.95); }
            .header-home .navbar-toggler { border-color: rgba(255, 255, 255, 0.2); }
            .header-home .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            }
            .header-home .nav-link:hover, .header-home .nav-link:focus,
            .header-home .nav-link:hover .bi, .header-home .nav-link:focus .bi,
            .header-home .nav-link:hover i, .header-home .nav-link:focus i {
                color: var(--brand) !important;
            }

            /* === CSS CHO HEADER-HOME KHI CUỘN (TRẠNG THÁI STICKY) === */
            .header-home.is-scrolled {
                position: fixed;
                background-color: var(--card);
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
                animation: slideDown 0.5s ease-out;
            }
            @keyframes slideDown {
                from { transform: translateY(-100%); }
                to { transform: translateY(0); }
            }
            .header-home.is-scrolled .logo-light { display: none; }
            .header-home.is-scrolled .logo-dark { display: block; }
            .header-home.is-scrolled .nav-link, .header-home.is-scrolled .logo-text,
            .header-home.is-scrolled .nav-link .bi, .header-home.is-scrolled .nav-link i {
                color: var(--text) !important;
            }
            .header-home.is-scrolled .nav-link:hover, .header-home.is-scrolled .nav-link:focus,
            .header-home.is-scrolled .nav-link:hover .bi, .header-home.is-scrolled .nav-link:focus .bi,
            .header-home.is-scrolled .nav-link:hover i, .header-home.is-scrolled .nav-link:focus i {
                color: var(--brand) !important;
            }
            .header-home.is-scrolled .navbar-toggler { border-color: rgba(0, 0, 0, 0.1); }
            .header-home.is-scrolled .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2843, 38, 35, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
            }

            /* === CSS CHO TOP BAR === */
            .top-bar {
                font-size: clamp(0.8rem, 2vw, 0.9rem);
            }
            .top-bar .bi {
                font-size: clamp(0.9rem, 2vw, 1rem);
            }
            .top-bar a {
                transition: color 0.2s ease;
            }

            /* === CSS RESPONSIVE === */
            @media (max-width: 991.98px) {
                .header-home .navbar-brand img, .header-home.is-scrolled .navbar-brand img {
                    height: clamp(60px, 8vw, 70px) !important;
                    max-height: 70px !important;
                }
                .header-internal .navbar-brand img {
                    height: clamp(40px, 6vw, 45px) !important;
                    max-height: 45px !important;
                }
                .navbar-collapse {
                    background-color: var(--card);
                    padding: 1rem;
                    margin-top: 0.5rem;
                    border-radius: var(--radius);
                    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                }
                .header-home .navbar-collapse .nav-link,
                .header-home .navbar-collapse .nav-link .bi,
                .header-home .navbar-collapse .nav-link i {
                    color: var(--text) !important;
                }
                .top-bar {
                    font-size: clamp(0.75rem, 2vw, 0.85rem);
                    padding: 1rem 0;
                }
                .top-bar .bi {
                    font-size: clamp(0.85rem, 2vw, 0.9rem);
                }
                .top-bar .gap-3 {
                    gap: 1.5rem !important;
                }
            }

            @media (max-width: 767.98px) {
                .header-home .navbar-brand img, .header-home.is-scrolled .navbar-brand img {
                    height: clamp(50px, 8vw, 60px) !important;
                    max-height: 60px !important;
                }
                .header-internal .navbar-brand img {
                    height: clamp(35px, 6vw, 40px) !important;
                    max-height: 40px !important;
                }
                .navbar-collapse {
                    padding: 0.75rem;
                    margin-top: 0.4rem;
                }
                .top-bar {
                    font-size: clamp(0.7rem, 2vw, 0.8rem);
                    padding: 0.75rem 0;
                }
                .top-bar .bi {
                    font-size: clamp(0.8rem, 2vw, 0.85rem);
                }
                .top-bar .gap-3 {
                    gap: 1rem !important;
                }
            }

            @media (max-width: 575.98px) {
                .header-home .navbar-brand img, .header-home.is-scrolled .navbar-brand img {
                    height: clamp(45px, 8vw, 50px) !important;
                    max-height: 50px !important;
                }
                .header-internal .navbar-brand img {
                    height: clamp(30px, 6vw, 35px) !important;
                    max-height: 35px !important;
                }
                .navbar-collapse {
                    padding: 0.5rem;
                    margin-top: 0.3rem;
                }
                .top-bar {
                    font-size: clamp(0.65rem, 2vw, 0.75rem);
                    padding: 0.5rem 0;
                }
                .top-bar .bi {
                    font-size: clamp(0.75rem, 2vw, 0.8rem);
                }
                .top-bar .gap-3 {
                    gap: 0.75rem !important;
                }
            }
        </style>
    @endpush

    @push('scripts-page')
        <script>
            // Xử lý trạng thái cuộn cho header-home
            document.addEventListener('DOMContentLoaded', function() {
                const header = document.querySelector('.header-home');
                if (header) {
                    const toggleHeaderClass = () => {
                        if (window.scrollY > 100) {
                            header.classList.add('is-scrolled');
                        } else {
                            header.classList.remove('is-scrolled');
                        }
                    };
                    window.addEventListener('scroll', toggleHeaderClass);
                    toggleHeaderClass(); // Gọi ngay khi tải trang
                }

                // Đóng navbar-collapse khi click link trên mobile
                const navbarLinks = document.querySelectorAll('.navbar-collapse .nav-link');
                const navbarCollapse = document.querySelector('.navbar-collapse');
                const navbarToggler = document.querySelector('.navbar-toggler');
                
                navbarLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (window.innerWidth < 992 && navbarCollapse.classList.contains('show')) {
                            navbarToggler.click(); // Tự động đóng navbar
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
