<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $settings['site_name'] ?? config('app.name', 'Laravel'))</title>
    @if(!empty($settings['favicon']))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $settings['favicon']) }}">
    @endif

    {{-- CSS Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- Custom CSS for Components --}}
    <style>
        /* CSS cho Giao diện Tối */
        [data-bs-theme="dark"] .bg-white {
            background-color: #212529 !important;
        }
        [data-bs-theme="dark"] .text-dark {
            color: #fff !important;
        }
        [data-bs-theme="dark"] .border-bottom {
            border-color: #495057 !important;
        }

        /* Ẩn/hiện icon sáng/tối */
        .theme-icon-light { display: none; }
        .theme-icon-dark { display: inline-block; }
        [data-bs-theme="dark"] .theme-icon-light { display: inline-block; }
        [data-bs-theme="dark"] .theme-icon-dark { display: none; }
        
        /* CSS để đổi logo theo theme */
        .logo-light { display: none; }
        .logo-dark { display: inline-block; }
        [data-bs-theme="dark"] .logo-light { display: inline-block; }
        [data-bs-theme="dark"] .logo-dark { display: none; }

        /* Search Overlay */
        .search-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(255, 255, 255, 0.98);
            z-index: 1050; display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        [data-bs-theme="dark"] .search-overlay {
            background-color: rgba(10, 10, 10, 0.98);
        }
        .search-overlay.active { opacity: 1; visibility: visible; }
        .btn-close-search {
            position: absolute; top: 2rem; right: 2rem; font-size: 3rem;
            color: #333; background: none; border: none; cursor: pointer;
        }
        [data-bs-theme="dark"] .btn-close-search { color: #fff; }
        .search-form-overlay { width: 100%; max-width: 600px; }
        .form-control-overlay {
            width: 100%; border: none; border-bottom: 2px solid #ccc;
            background-color: transparent; font-size: 2.5rem; text-align: center;
            padding: 1rem 0; outline: none; transition: border-color 0.3s ease;
        }
        [data-bs-theme="dark"] .form-control-overlay { color: #fff; }
        .form-control-overlay:focus { border-bottom-color: #0d6efd; }
        .form-text-overlay { text-align: center; color: #6c757d; }
        .form-text-overlay a { color: #333; text-decoration: none; }
        [data-bs-theme="dark"] .form-text-overlay a { color: #fff; }

        /* Product Card */
        .product-card { transition: transform .2s ease-in-out, box-shadow .2s ease-in-out; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important; }
        .card-img-top { aspect-ratio: 1 / 1; object-fit: cover; }
        .product-name {
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden; text-overflow: ellipsis; height: 2.5em;
        }
        .product-actions .btn-link { font-size: 0.9rem; }
        .toggle-wishlist-btn.active i { color: var(--bs-danger) !important; }

        /* Category Menu */
        .dropdown-submenu { position: relative; }
        .dropdown-submenu .dropdown-menu {
            top: 0; left: 100%; margin-top: -1px; display: none;
        }
        .dropdown-submenu:hover > .dropdown-menu { display: block; }
    </style>
    
    @stack('styles')
</head>
<body>

    @include('layouts.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

    {{-- JS Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    {{-- Global JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIC CHUYỂN GIAO DIỆN SÁNG/TỐI ---
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            const htmlElement = document.documentElement;

            const getPreferredTheme = () => {
                const storedTheme = localStorage.getItem('theme');
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };

            const setTheme = (theme) => {
                htmlElement.setAttribute('data-bs-theme', theme);
                localStorage.setItem('theme', theme);
            };

            setTheme(getPreferredTheme());

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    const currentTheme = htmlElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    setTheme(newTheme);
                });
            }

            // --- LOGIC TÌM KIẾM ---
            const searchToggleBtn = document.getElementById('search-toggle-btn');
            const searchCloseBtn = document.getElementById('search-close-btn');
            const searchOverlay = document.getElementById('search-overlay');
            const searchInput = searchOverlay ? searchOverlay.querySelector('input') : null;

            if (searchToggleBtn && searchOverlay && searchInput) {
                searchToggleBtn.addEventListener('click', function() {
                    searchOverlay.classList.add('active');
                    setTimeout(() => searchInput.focus(), 300);
                });
            }
            
            if (searchCloseBtn && searchOverlay) {
                searchCloseBtn.addEventListener('click', function() {
                    searchOverlay.classList.remove('active');
                });
            }

            // --- LOGIC YÊU THÍCH ---
            document.body.addEventListener('click', function(event) {
                const button = event.target.closest('.toggle-wishlist-btn');
                if (button) {
                    event.preventDefault();
                    const productId = button.dataset.productId;
                    const icon = button.querySelector('i');
                    
                    fetch("{{ route('wishlist.toggle') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ product_id: productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.status === 'added') {
                                icon.classList.remove('bi-heart');
                                icon.classList.add('bi-heart-fill', 'text-danger');
                                button.classList.add('active');
                            } else {
                                if (window.location.pathname.includes('/danh-sach-yeu-thich')) {
                                    button.closest('.col-6').remove();
                                } else {
                                    icon.classList.remove('bi-heart-fill', 'text-danger');
                                    icon.classList.add('bi-heart');
                                    button.classList.remove('active');
                                }
                            }
                            const wishlistCountSpan = document.getElementById('wishlist-count');
                            if(wishlistCountSpan) {
                                wishlistCountSpan.textContent = data.count;
                                wishlistCountSpan.style.display = data.count > 0 ? 'inline-block' : 'none';
                            }
                        } else {
                            if(data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
