@php
    // $staffMenu được inject sẵn từ AppServiceProvider (View::composer('staffs.*', ...))
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $menu = collect($staffMenu ?? []);
    $appName = config('app.name', 'Staff Panel');
    $user = \Illuminate\Support\Facades\Auth::user();

    // Helper: resolve URL từ route name / URL thô
    $resolveUrl = function ($routeVal) {
        if (!$routeVal) {
            return '#';
        }
        if (Route::has($routeVal)) {
            return route($routeVal);
        }
        if (Str::startsWith($routeVal, ['http://', 'https://', '/'])) {
            return $routeVal;
        }
        return url($routeVal);
    };
@endphp
<!DOCTYPE html>
<html lang="vi" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff • @yield('title', 'Dashboard') — {{ $appName }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fonts + Bootstrap + Icons --}}
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ====== Brand palette (đồng bộ với admin) ====== */
        :root {
            --brand: #C46F3B;
            --brand-600: #B46234;
            --brand-700: #7B3E22;
            --accent: #4E6B52;
            --sand: #EADFCE;
            --text: #2B2623;
            --bg: #F6F2EC;
            --card: #FFFFFF;
            --muted: #7d726c;
            --wood-900: #201915;
            --wood-800: #2a211d;
            --wood-700: #3a2f28;
            --ring: rgba(196, 111, 59, .25);
            --shadow: 0 10px 30px rgba(32, 25, 21, .12);
            --radius: 16px;
            --sidebar-w: 276px;
            --sidebar-w-mini: 92px;

            /* map sang Bootstrap */
            --bs-primary: var(--brand);
            --bs-primary-rgb: 196, 111, 59;
            --bs-link-color: var(--brand);
            --bs-link-hover-color: var(--brand-600);
        }

        html,
        body {
            height: 100%
        }

        body {
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            background:
                radial-gradient(1200px 600px at -20% -10%, rgba(196, 111, 59, .08), transparent 60%),
                radial-gradient(1000px 600px at 120% 10%, rgba(78, 107, 82, .08), transparent 60%),
                var(--bg);
            color: var(--text);
        }

        ::selection {
            background: rgba(196, 111, 59, .28)
        }

        html {
            scroll-behavior: smooth
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--brand) transparent
        }

        *::-webkit-scrollbar {
            height: 10px;
            width: 10px
        }

        *::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, var(--brand), var(--brand-600));
            border-radius: 10px
        }

        *:focus-visible {
            outline: 2px solid var(--brand);
            outline-offset: 2px
        }

        #wrapper {
            display: flex;
            min-height: 100%
        }

        /* ====== Sidebar ====== */
        #sidebar-wrapper {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--wood-800) 0%, var(--wood-900) 100%);
            color: #e7e3df;
            position: sticky;
            top: 0;
            left: 0;
            z-index: 1030;
            transition: width .25s ease, transform .25s ease;
            box-shadow: inset -1px 0 0 rgba(255, 255, 255, .04);
        }

        #wrapper.collapsed #sidebar-wrapper {
            width: var(--sidebar-w-mini)
        }

        .sidebar-heading {
            padding: 1rem 1.25rem;
            font-weight: 800;
            letter-spacing: .3px;
            display: flex;
            align-items: center;
            gap: .75rem;
            white-space: nowrap;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
        }

        .sidebar-heading .logo {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(196, 111, 59, .2), rgba(196, 111, 59, .05));
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .06);
        }

        .sidebar-heading .brand-name {
            color: #fff
        }

        .nav-section-title {
            font-size: .72rem;
            letter-spacing: .12em;
            opacity: .6;
            padding: .9rem 1.1rem .4rem;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .nav-list {
            padding: .35rem .4rem 1rem
        }

        .nav-list .nav-item {
            --item-padding: .7rem;
            display: flex;
            align-items: center;
            gap: .7rem;
            white-space: nowrap;
            color: #e7e3df;
            text-decoration: none;
            padding: var(--item-padding) .9rem;
            border-radius: 12px;
            margin: .18rem .6rem;
            position: relative;
            overflow: hidden;
            transition: .18s ease transform, .25s ease background, .18s ease color, box-shadow .2s;
        }

        .nav-list .nav-item i {
            width: 1.4rem;
            text-align: center;
            font-size: 1.08rem;
            opacity: .95
        }

        .nav-list .nav-item .text {
            flex: 1
        }

        .nav-list .nav-item::before {
            content: '';
            position: absolute;
            inset: 0;
            border-left: 3px solid transparent
        }

        .nav-list .nav-item:hover {
            background: rgba(255, 255, 255, .06);
            transform: translateY(-1px)
        }

        .nav-list .nav-item.active {
            background: linear-gradient(90deg, rgba(196, 111, 59, .16), transparent 60%);
            box-shadow: 0 0 0 2px var(--ring) inset;
        }

        .nav-list .nav-item.active::before {
            border-left-color: var(--brand)
        }

        /* thu gọn label khi mini (desktop) */
        #wrapper.collapsed #sidebar-wrapper .nav-section-title,
        #wrapper.collapsed #sidebar-wrapper .sidebar-heading .brand-name,
        #wrapper.collapsed .nav-list .nav-item .text {
            width: 0;
            opacity: 0;
            margin: 0;
            padding: 0;
            overflow: hidden
        }

        #wrapper.collapsed .sidebar-heading {
            justify-content: center
        }

        #wrapper.collapsed .nav-list .nav-item {
            justify-content: center;
            padding: .7rem 0
        }

        /* ====== Content & Navbar ====== */
        #page-content-wrapper {
            flex: 1;
            min-width: 0
        }

        .app-navbar {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, .75);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(32, 25, 21, .08);
            overflow: visible;
        }

        .navbar .dropdown-menu {
            z-index: 2000
        }

        .navbar-nav .nav-item {
            overflow: visible !important;
            position: relative
        }

        .content {
            padding: 1.25rem
        }

        .card {
            background: linear-gradient(180deg, rgba(234, 223, 206, .25), transparent), var(--card);
            border: 0;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-header {
            border-bottom: 1px dashed rgba(32, 25, 21, .1);
            background: transparent
        }

        .btn-primary {
            background: var(--brand);
            border-color: var(--brand)
        }

        .btn-primary:hover {
            background: var(--brand-600);
            border-color: var(--brand-600)
        }

        .btn-outline-primary {
            color: var(--brand);
            border-color: var(--brand)
        }

        .btn-outline-primary:hover {
            background: var(--brand);
            color: #fff
        }

        /* Ripple cho nút icon nhỏ */
        .ripple {
            position: relative;
            overflow: hidden
        }

        /* Search */
        .search-wrap {
            position: relative;
            max-width: 560px;
            width: 100%
        }

        .search-icon {
            position: absolute;
            left: .75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9a887f
        }

        .search-input {
            padding-left: 2.25rem;
            border-radius: 12px;
            background: #fff6ed;
            border: 1px solid rgba(196, 111, 59, .25)
        }

        .quick-box {
            position: absolute;
            left: 0;
            right: 0;
            top: 100%;
            margin-top: .4rem;
            z-index: 1050;
            display: none
        }

        .quick-results {
            max-height: 320px;
            overflow: auto;
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .15)
        }

        /* Footer */
        footer.app-footer {
            color: var(--muted);
            font-size: .875rem;
            padding: 1.25rem
        }

        /* Soft badges */
        .badge.bg-warning-soft {
            background: #fff0d6;
            color: #8a5b1f
        }

        .badge.bg-info-soft {
            background: #e6f1ff;
            color: #0b4a8b
        }

        .badge.bg-success-soft {
            background: #e5f7ed;
            color: #1e6b3a
        }

        .badge.bg-danger-soft {
            background: #fde7e7;
            color: #992f2f
        }

        .badge.bg-primary-soft {
            background: #eaf0ff;
            color: #2c4da0
        }

        /* Back-to-top */
        #backTop {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 1090;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s, transform .2s;
            transform: translateY(6px);
        }

        #backTop.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0)
        }

        /* Mobile (off-canvas) */
        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                position: fixed;
                inset: 0 auto 0 0;
                width: var(--sidebar-w);
                transform: translateX(-100%);
                box-shadow: 8px 0 30px rgba(0, 0, 0, .25);
            }

            #wrapper.show-sidebar #sidebar-wrapper {
                transform: translateX(0)
            }

            #wrapper.collapsed #sidebar-wrapper {
                transform: translateX(-100%)
            }

            .app-navbar .container-fluid {
                gap: .5rem
            }

            .search-wrap {
                max-width: none
            }
        }

        /* Dropdown không quá cao */
        .navbar .dropdown-menu {
            max-height: calc(100vh - 120px);
            overflow: auto
        }
    </style>

    @stack('styles')
</head>

<body>
    {{-- THÊM MỚI: Toast Container --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>
    <div id="wrapper" class="{{ session('sidebar_collapsed') ? 'collapsed' : '' }}">
        {{-- ============ Sidebar ============ --}}
        <aside id="sidebar-wrapper" aria-label="Sidebar">
            <div class="sidebar-heading">
                <span class="logo"><i class="bi bi-flower3 fs-5 text-warning"></i></span>
                <span class="brand-name">{{ $appName }} • Staff</span>
            </div>

            <div class="nav-section-title">Tổng quan</div>
            <nav class="nav-list" role="navigation">
                @if (Route::has('staff.dashboard'))
                    <a href="{{ route('staff.dashboard') }}"
                        class="nav-item {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}"
                        data-bs-toggle="tooltip" title="Dashboard">
                        <i class="bi bi-speedometer2"></i><span class="text">Bảng điều khiển</span>
                    </a>
                @endif
            </nav>

            @if ($menu->isNotEmpty())
                <div class="nav-section-title">Chức năng</div>
                <nav class="nav-list">
                    @foreach ($menu as $item)
                        @php
                            $routeVal = $item['route'] ?? null;
                            $href = $resolveUrl($routeVal);
                            $icon = $item['icon'] ?? 'bi-circle';
                            $label = $item['label'] ?? 'Mục';
                            $active =
                                $item['active'] ?? false ? 'active' : (url()->current() === $href ? 'active' : '');
                        @endphp
                        <a class="nav-item {{ $active }}" href="{{ $href }}"><i
                                class="bi {{ $icon }}"></i><span class="text">{{ $label }}</span></a>
                    @endforeach
                </nav>
            @endif

            <div class="nav-section-title">Quyền hạn</div>
            <nav class="nav-list pb-4">
                @perm('permissions', 'view')
                    <a class="nav-item {{ request()->routeIs('staff.permissions.*') ? 'active' : '' }}"
                        href="{{ route('staff.permissions.index') }}">
                        <i class="bi bi-shield-check"></i><span class="text">Quyền</span>
                    </a>
                @endperm
                @perm('route_permissions', 'view')
                    <a class="nav-item {{ request()->routeIs('staff.route-permissions.*') ? 'active' : '' }}"
                        href="{{ route('staff.route-permissions.index') }}">
                        <i class="bi bi-diagram-3"></i><span class="text">Route → Quyền</span>
                    </a>
                @endperm
            </nav>
        </aside>

        {{-- Overlay cho mobile --}}
        <div id="sidebarBackdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1025;"
            aria-hidden="true"></div>

        {{-- ============ Content ============ --}}
        <div id="page-content-wrapper">
            <nav class="navbar app-navbar navbar-expand-lg">
                <div class="container-fluid py-2">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-primary border-0 ripple" id="sidebarToggle" type="button"
                            aria-label="Toggle sidebar" title="Thu gọn / Mở rộng">
                            <i class="bi bi-layout-sidebar-inset"></i>
                        </button>
                    </div>

                    {{-- Quick Search --}}
                    <div class="flex-grow-1 px-lg-3 d-none d-md-block">
                        <div class="search-wrap mx-auto">
                            <i class="bi bi-search search-icon"></i>
                            <input id="quickSearch" class="form-control form-control-sm search-input"
                                placeholder="Tìm nhanh (Ctrl+/) – gõ: Bảng điều khiển, mục menu…" autocomplete="off">
                            <div class="quick-box" id="quickBox">
                                <div class="quick-results bg-white border rounded-3 overflow-hidden">
                                    <div class="list-group list-group-flush" id="quickList"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <ul class="navbar-nav ms-auto align-items-center gap-2">
                        {{-- Notification Dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="notificationDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo">
                                <span class="position-relative">
                                    <i class="bi bi-bell fs-5"></i>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        id="notification-badge" style="display: none;">0</span>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown"
                                style="width: 350px;">
                                <li class="px-3 py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Thông báo mới</h6>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <div id="notification-list" style="max-height: 400px; overflow-y: auto;">
                                    <li class="text-center text-muted p-3" id="no-notification-item">Không có thông báo
                                        mới.</li>
                                </div>
                            </ul>
                        </li>
                        <li class="nav-item d-none d-md-inline">
                            <span class="text-muted small me-2">Phím tắt</span>
                            <span class="kbd">Ctrl</span><span class="mx-1">+</span><span
                                class="kbd">/</span>
                            <span class="mx-2">•</span>
                            <span class="kbd">Ctrl</span><span class="mx-1">+</span><span
                                class="kbd">K</span>
                        </li>

                        {{-- User Dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                                id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                data-bs-auto-close="outside">
                                <span class="position-relative me-2">
                                    <img src="{{ optional($user->profile)->avatar ? asset('storage/' . $user->profile->avatar) : 'https://via.placeholder.com/32' }}"
                                        class="rounded-circle" width="34" height="34" alt="Avatar">
                                    <span
                                        class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-light rounded-circle"></span>
                                </span>
                                <span class="fw-600">{{ $user?->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i
                                            class="bi bi-person me-2"></i>Hồ sơ</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST"
                                        onsubmit="return confirm('Đăng xuất khỏi hệ thống?')">@csrf
                                        <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Đăng
                                            xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            {{-- Flash toasts --}}
            <div class="toast-container position-fixed top-0 end-0 p-3">
                @if (session('success'))
                    <div class="toast align-items-center text-bg-success border-0 show mb-2" role="alert">
                        <div class="d-flex">
                            <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
                        <div class="d-flex">
                            <div class="toast-body"><i
                                    class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                                data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Optional page bar --}}
            @hasSection('pagebar')
                <div class="container-fluid pt-3">@yield('pagebar')</div>
            @endif

            <main class="content">
                @yield('content')
                <footer class="app-footer text-center">
                    © {{ date('Y') }} • {{ $appName }} • Staff Panel
                </footer>
            </main>
        </div>
    </div>

    {{-- Command Palette (Ctrl+K) --}}
    <div class="modal fade" id="cmdPalette" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:720px">
            <div class="modal-content"
                style="border-radius:16px; overflow:hidden; background:var(--card); box-shadow:0 30px 60px rgba(0,0,0,.25)">
                <div class="position-relative border-bottom">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input id="cmdInput" class="w-100"
                        style="border:0; outline:none; padding:.9rem 2.75rem .9rem 2.25rem; background:transparent"
                        placeholder="Tìm lệnh / trang… (Dashboard, Quyền, Mục menu…)" autocomplete="off" />
                    <span
                        class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted small d-none d-md-inline">Esc</span>
                </div>
                <div class="p-2">
                    <div id="cmdList" class="list-unstyled m-0"></div>
                </div>
            </div>
        </div>
    </div>

    <button id="backTop" class="btn btn-primary rounded-circle shadow-lg" aria-label="Back to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @php
        // Links cho Quick Search + Command Palette (từ menu động)
        $q = [];
        $push = function (string $text, string $url, string $icon) use (&$q) {
            $q[] = ['label' => $text, 'url' => $url, 'icon' => $icon];
        };
        if (Route::has('staff.dashboard')) {
            $push('Bảng điều khiển', route('staff.dashboard'), 'bi-speedometer2');
        }

        foreach ($menu as $it) {
            $text = $it['label'] ?? null;
            $routeVal = $it['route'] ?? null;
            $icon = $it['icon'] ?? 'bi-circle';
            if ($text && $routeVal) {
                try {
                    $url = $resolveUrl($routeVal);
                    $push($text, $url, $icon);
                } catch (\Throwable $e) {
                    /* ignore */
                }
            }
        }
        // Thêm các mục quyền nếu có
        if (\Illuminate\Support\Facades\Gate::check('permissions.view')) {
            $push('Quyền', route('staff.permissions.index'), 'bi-shield-check');
        }
        if (\Illuminate\Support\Facades\Gate::check('route_permissions.view')) {
            $push('Route ↔ Quyền', route('staff.route-permissions.index'), 'bi-diagram-3');
        }
    @endphp
    <script>
        (function() {
            const wrapper = document.getElementById('wrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const backTop = document.getElementById('backTop');
            const backdrop = document.getElementById('sidebarBackdrop');
            const quick = @json($q);
            const isMobile = () => window.innerWidth < 992;

            // Tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                try {
                    new bootstrap.Tooltip(el);
                } catch (e) {}
            });

            // Sidebar state (desktop)
            const keySide = 'staff.sidebar.collapsed';
            if (!isMobile() && localStorage.getItem(keySide) === '1') wrapper.classList.add('collapsed');

            // Toggle sidebar
            sidebarToggle?.addEventListener('click', (e) => {
                ripple(e);
                if (isMobile()) {
                    wrapper.classList.toggle('show-sidebar');
                    backdrop.style.display = wrapper.classList.contains('show-sidebar') ? 'block' : 'none';
                } else {
                    wrapper.classList.toggle('collapsed');
                    localStorage.setItem(keySide, wrapper.classList.contains('collapsed') ? '1' : '0');
                }
            });

            // Overlay close
            backdrop?.addEventListener('click', () => {
                wrapper.classList.remove('show-sidebar');
                backdrop.style.display = 'none';
            });

            // Back to top
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backTop.classList.add('show');
                } else {
                    backTop.classList.remove('show');
                }
            });
            backTop.addEventListener('click', () => window.scrollTo({
                top: 0,
                behavior: 'smooth'
            }));

            // Ripple
            function ripple(e) {
                const btn = e.currentTarget;
                if (!btn) return;
                btn.classList.add('ripple');
                const rect = btn.getBoundingClientRect();
                const circle = document.createElement('span');
                const d = Math.max(rect.width, rect.height);
                Object.assign(circle.style, {
                    width: d + 'px',
                    height: d + 'px',
                    position: 'absolute',
                    left: (e.clientX - rect.left) + 'px',
                    top: (e.clientY - rect.top) + 'px',
                    transform: 'translate(-50%,-50%)',
                    background: 'rgba(255,255,255,.35)',
                    borderRadius: '50%',
                    pointerEvents: 'none',
                    opacity: '0.6',
                    transition: 'opacity .6s, width .6s, height .6s'
                });
                btn.appendChild(circle);
                requestAnimationFrame(() => {
                    circle.style.width = circle.style.height = (d * 1.8) + 'px';
                    circle.style.opacity = '0';
                });
                setTimeout(() => circle.remove(), 600);
            }

            // ====== Quick Search (Ctrl+/) ======
            const search = document.getElementById('quickSearch');
            const quickBox = document.getElementById('quickBox');
            const quickList = document.getElementById('quickList');

            function renderQuick(qs) {
                quickList.innerHTML = '';
                qs.slice(0, 8).forEach(l => {
                    const a = document.createElement('a');
                    a.href = l.url;
                    a.className = 'list-group-item list-group-item-action d-flex align-items-center';
                    a.innerHTML = `<i class="bi ${l.icon} me-2 text-muted"></i> <span>${l.label}</span>`;
                    quickList.appendChild(a);
                });
                quickBox.style.display = qs.length ? 'block' : 'none';
            }

            function hideQuickBox() {
                quickBox.style.display = 'none';
            }

            search?.addEventListener('input', () => {
                const v = search.value.trim().toLowerCase();
                if (!v) {
                    hideQuickBox();
                    return;
                }
                renderQuick(quick.filter(x => x.label.toLowerCase().includes(v)));
            });
            search?.addEventListener('focus', () => {
                const v = search.value.trim().toLowerCase();
                if (v) {
                    renderQuick(quick.filter(x => x.label.toLowerCase().includes(v)));
                }
            });
            search?.addEventListener('blur', () => setTimeout(hideQuickBox, 150));

            // ===== Command Palette (Ctrl+K) =====
            const cmdModal = new bootstrap.Modal(document.getElementById('cmdPalette'));
            const cmdInput = document.getElementById('cmdInput');
            const cmdList = document.getElementById('cmdList');
            let activeIndex = -1;

            function openPalette() {
                cmdModal.show();
                setTimeout(() => {
                    cmdInput.value = '';
                    renderCmd(quick);
                    activeIndex = -1;
                    cmdInput.focus();
                }, 120);
            }

            function closePalette() {
                try {
                    cmdModal.hide();
                } catch {}
            }

            function renderCmd(items) {
                cmdList.innerHTML = '';
                if (!items.length) {
                    cmdList.innerHTML = `<div class="text-center text-muted py-3">Không tìm thấy mục phù hợp</div>`;
                    return;
                }
                items.slice(0, 10).forEach((it, idx) => {
                    const li = document.createElement('div');
                    li.className = 'd-flex align-items-center gap-2 p-2 rounded-3 cmd-item';
                    li.dataset.url = it.url;
                    li.style.cursor = 'pointer';
                    li.innerHTML =
                        `<span style="width:28px;text-align:center"><i class="bi ${it.icon}"></i></span><span>${it.label}</span>`;
                    li.addEventListener('click', () => {
                        window.location.href = it.url;
                    });
                    if (idx === activeIndex) li.style.background = 'rgba(196,111,59,.12)';
                    cmdList.appendChild(li);
                });
            }
            cmdInput?.addEventListener('input', () => {
                const v = cmdInput.value.trim().toLowerCase();
                const items = v ? quick.filter(x => x.label.toLowerCase().includes(v)) : quick;
                activeIndex = -1;
                renderCmd(items);
            });
            cmdInput?.addEventListener('keydown', (e) => {
                const items = [...cmdList.querySelectorAll('.cmd-item')];
                if (['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) {
                    e.preventDefault();
                }
                if (e.key === 'ArrowDown') {
                    activeIndex = Math.min(activeIndex + 1, items.length - 1);
                }
                if (e.key === 'ArrowUp') {
                    activeIndex = Math.max(activeIndex - 1, 0);
                }
                if (e.key === 'Enter' && items[activeIndex]) {
                    window.location.href = items[activeIndex].dataset.url;
                    return;
                }
                items.forEach((it, i) => it.style.background = i === activeIndex ? 'rgba(196,111,59,.12)' : '');
            });

            // Shortcuts
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                    e.preventDefault();
                    search?.focus();
                }
                if ((e.ctrlKey || e.metaKey) && (e.key.toLowerCase() === 'k')) {
                    e.preventDefault();
                    openPalette();
                }
                if (e.key === 'Escape') {
                    closePalette();
                    hideQuickBox();
                }
            });

            // Ngăn '#' nhảy đầu trang + ẩn quickBox khi mở dropdown
            document.querySelectorAll('[data-bs-toggle="dropdown"][href="#"]').forEach(el => {
                el.addEventListener('click', e => e.preventDefault());
            });
            document.addEventListener('show.bs.dropdown', () => {
                const box = document.getElementById('quickBox');
                if (box) box.style.display = 'none';
            });

            // Resize: rời mobile → ẩn overlay
            window.addEventListener('resize', () => {
                if (!isMobile()) {
                    wrapper.classList.remove('show-sidebar');
                    backdrop.style.display = 'none';
                }
            });

            // Toast auto hide
            document.querySelectorAll('.toast').forEach(el => {
                try {
                    new bootstrap.Toast(el, {
                        delay: 2600
                    }).show();
                } catch (e) {}
            });
        })();
    </script>
    {{-- ======================================================= --}}
    {{-- THÊM MỚI: LOGIC REAL-TIME NOTIFICATION BẰNG POLLING --}}
    {{-- ======================================================= --}}
    <script>
        (function() {
            // --- Cấu hình ---
            const pollingInterval = 20000; // 20 giây
            const soundSrc = '{{ asset('sounds/notification.mp3') }}'; // Đường dẫn tới file âm báo
            const newOrderApiUrl =
                '{{ auth()->user()->role->name === 'admin' ? route('admin.notifications.new') : route('staff.notifications.new') }}';

            // --- Biến trạng thái ---
            let lastCheckTimestamp = '{{ now()->toIso8601String() }}';
            let notificationCount = 0;
            const notificationSound = new Audio(soundSrc);

            // --- DOM Elements ---
            const badge = document.getElementById('notification-badge');
            const list = document.getElementById('notification-list');
            const noItem = document.getElementById('no-notification-item');
            const toastContainer = document.querySelector('.toast-container');
            const dropdownToggle = document.getElementById('notificationDropdown');

            // --- Hàm xử lý ---

            // 1. Hàm hiển thị Toast
            function showNotificationToast(order) {
                const toastId = `toast-${order.id}`;
                const toastHTML = `
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
                <div class="toast-header">
                    <i class="bi bi-receipt-cutoff text-primary me-2"></i>
                    <strong class="me-auto">Đơn hàng mới!</strong>
                    <small>vài giây trước</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Khách hàng <strong>${order.user.name}</strong> vừa đặt đơn <strong>#${order.order_code}</strong>.
                </div>
            </div>
        `;
                toastContainer.insertAdjacentHTML('beforeend', toastHTML);
                const toastEl = document.getElementById(toastId);
                const toast = new bootstrap.Toast(toastEl, {
                    delay: 10000
                });
                toast.show();
            }

            // 2. Hàm cập nhật UI (Badge và List)
            function updateNotificationUI(order) {
                // Cập nhật Badge
                notificationCount++;
                badge.textContent = notificationCount;
                badge.style.display = 'block';

                // Xóa thông báo "Không có" nếu có
                if (noItem) noItem.style.display = 'none';

                // Thêm vào danh sách
                const orderUrl = `{{ url(auth()->user()->role->name . '/orders') }}/${order.id}`;
                const listItemHTML = `
            <li>
                <a class="dropdown-item py-2" href="${orderUrl}">
                    <div class="small text-wrap">
                        KH <strong>${order.user.name}</strong> vừa đặt đơn <strong>#${order.order_code}</strong>.
                    </div>
                    <div class="xsmall text-muted">${new Date(order.created_at).toLocaleString('vi-VN')}</div>
                </a>
            </li>
        `;
                list.insertAdjacentHTML('afterbegin', listItemHTML); // Thêm vào đầu danh sách
            }

            // 3. Hàm chính để kiểm tra đơn hàng mới
            async function checkNewOrders() {
                try {
                    const response = await fetch(`${newOrderApiUrl}?last_check=${lastCheckTimestamp}`);
                    if (!response.ok) return;

                    const data = await response.json();
                    const newOrders = data.new_orders || [];

                    // Cập nhật timestamp cho lần kiểm tra tiếp theo
                    lastCheckTimestamp = data.server_time;

                    if (newOrders.length > 0) {
                        newOrders.forEach(order => {
                            updateNotificationUI(order);
                            showNotificationToast(order);
                        });
                        notificationSound.play().catch(e => console.error("Audio play failed:", e));
                    }
                } catch (error) {
                    console.error('Error fetching new orders:', error);
                }
            }

            // 4. Reset badge khi người dùng click vào chuông
            dropdownToggle.addEventListener('show.bs.dropdown', () => {
                notificationCount = 0;
                badge.style.display = 'none';
                badge.textContent = '0';
            });


            // --- Khởi chạy ---
            setInterval(checkNewOrders, pollingInterval);
            console.log('Notification polling started.');
        })();
    </script>
    {{-- KILL-SWITCH pseudo-element lạ trên phân trang (nếu có) --}}
    <style id="kill-pagination-arrows">
        .pagination::before,
        .pagination::after,
        .pagination *::before,
        .pagination *::after,
        .page-item::before,
        .page-item::after,
        .page-item .page-link::before,
        .page-item .page-link::after,
        a[rel="prev"]::before,
        a[rel="prev"]::after,
        a[rel="next"]::before,
        a[rel="next"]::after,
        .previous::before,
        .previous::after,
        .next::before,
        .next::after,
        .prev::before,
        .prev::after,
        .dataTables_paginate *::before,
        .dataTables_paginate *::after {
            content: none !important;
            background: none !important;
            border: 0 !important;
            box-shadow: none !important;
            position: static !important;
            display: inline !important;
            width: auto !important;
            height: auto !important;
        }

        .pagination {
            gap: .25rem
        }

        .page-item .page-link {
            position: static !important
        }
    </style>

    @stack('scripts')
</body>

</html>
