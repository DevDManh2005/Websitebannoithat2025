@php
    // $staffMenu được inject sẵn từ AppServiceProvider (View::composer('staffs.*', ...))
    $menu    = $staffMenu ?? collect();
    $appName = config('app.name', 'Staff Panel');
    $user    = \Illuminate\Support\Facades\Auth::user();
@endphp
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Bảng điều khiển') — {{ $appName }}</title>

    {{-- Bootstrap + Icons (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root { --sidebar-width: 240px; }
        body { min-height: 100vh; }
        .layout {
            display: grid;
            grid-template-columns: var(--sidebar-width) 1fr;
        }
        .sidebar {
            background: #0f172a; /* slate-900 */
            color: #cbd5e1;      /* slate-300 */
            min-height: 100vh;
        }
        .sidebar .brand {
            color: #fff;
            font-weight: 700;
            letter-spacing: .3px;
        }
        .sidebar a.nav-link {
            color: #cbd5e1;
            border-radius: .5rem;
        }
        .sidebar a.nav-link:hover { background: rgba(255,255,255,.08); color:#fff; }
        .sidebar a.nav-link.active { background: #2563eb; color:#fff; }
        .content {
            background: #f8fafc; /* slate-50 */
            min-height: 100vh;
        }
        .page-header {
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1rem;
        }
        .chip {
            display:inline-block; padding:.25rem .5rem; border-radius:999px;
            background:#fee2e2; color:#b91c1c; font-size:.775rem; margin:.125rem;
        }
        @media (max-width: 991.98px) {
            :root { --sidebar-width: 0; }
            .layout { grid-template-columns: 1fr; }
            .sidebar {
                position: fixed; inset: 0 auto 0 0; width: 280px; transform: translateX(-100%);
                z-index: 1040; transition: .2s ease;
            }
            .sidebar.show { transform: translateX(0); }
            .sidebar-backdrop {
                position: fixed; inset:0; background: rgba(15,23,42,.45); z-index:1039; display:none;
            }
            .sidebar-backdrop.show { display:block; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

<div class="layout">
    {{-- SIDEBAR --}}
    <aside id="sidebar" class="sidebar p-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a class="brand text-decoration-none" href="{{ route('staff.dashboard') }}">
                {{ $appName }} • Staff
            </a>
            <button class="btn btn-sm btn-outline-light d-lg-none" type="button" id="btnCloseSidebar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <nav class="nav flex-column gap-1">
            <a class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}"
               href="{{ route('staff.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Bảng điều khiển
            </a>

            {{-- Menu động từ $staffMenu --}}
            @foreach($menu as $item)
                @php
                    $routeVal = $item['route'] ?? null;

                    // Resolve tên route/URL an toàn (không dùng "use" trong Blade)
                    $href = '#';
                    if ($routeVal) {
                        if (\Illuminate\Support\Facades\Route::has($routeVal)) {
                            $href = route($routeVal);
                        } elseif (\Illuminate\Support\Str::startsWith($routeVal, ['http://', 'https://', '/'])) {
                            $href = $routeVal;
                        } else {
                            $href = url($routeVal);
                        }
                    }

                    $active = ($item['active'] ?? false) ? 'active' : '';
                    $icon   = $item['icon'] ?? 'bi-circle';
                    $label  = $item['label'] ?? 'Mục';
                @endphp

                <a class="nav-link {{ $active }}" href="{{ $href }}">
                    <i class="bi {{ $icon }} me-2"></i> {{ $label }}
                </a>
            @endforeach

            {{-- Tuỳ chọn: mục “Quyền” nếu đã cấp --}}
            @perm('permissions','view')
                <a class="nav-link {{ request()->routeIs('staff.permissions.*') ? 'active' : '' }}"
                   href="{{ route('staff.permissions.index') }}">
                    <i class="bi bi-shield-check me-2"></i> Quyền
                </a>
            @endperm
            @perm('route_permissions','view')
                <a class="nav-link {{ request()->routeIs('staff.route-permissions.*') ? 'active' : '' }}"
                   href="{{ route('staff.route-permissions.index') }}">
                    <i class="bi bi-diagram-3 me-2"></i> Route ↔ Permission
                </a>
            @endperm
        </nav>
    </aside>

    {{-- MAIN --}}
    <main class="content">
        {{-- Topbar --}}
        <div class="bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary d-lg-none" id="btnOpenSidebar" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="h5 mb-0">@yield('title','Bảng điều khiển')</h1>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="text-muted d-none d-sm-inline">Xin chào, {{ $user?->name }}</span>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" type="button">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}">Tài khoản</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button class="dropdown-item" type="submit">Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="container-fluid py-3">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <div class="fw-semibold mb-1">Có lỗi xảy ra:</div>
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar   = document.getElementById('sidebar');
    const backdrop  = document.getElementById('sidebarBackdrop');
    const openBtn   = document.getElementById('btnOpenSidebar');
    const closeBtn  = document.getElementById('btnCloseSidebar');

    function openSidebar(){ sidebar.classList.add('show'); backdrop.classList.add('show'); }
    function closeSidebar(){ sidebar.classList.remove('show'); backdrop.classList.remove('show'); }

    openBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    backdrop?.addEventListener('click', closeSidebar);
</script>
@stack('scripts')
</body>
</html>