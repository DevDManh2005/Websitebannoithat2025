{{-- resources/views/admins/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin – @yield('title','Dashboard')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Fonts + Bootstrap + Icons --}}
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* ====== Brand palette ====== */
    :root{
      --brand:#C46F3B; --brand-600:#B46234; --brand-700:#7B3E22;
      --accent:#4E6B52; --sand:#EADFCE; --text:#2B2623; --bg:#F6F2EC;
      --card:#FFFFFF; --muted:#7d726c;
      --wood-900:#201915; --wood-800:#2a211d; --wood-700:#3a2f28;
      --ring:rgba(196,111,59,.25); --shadow:0 10px 30px rgba(32,25,21,.12); --radius:16px;
      --sidebar-w:276px; --sidebar-w-mini:92px;

      /* map sang Bootstrap */
      --bs-primary: var(--brand); --bs-primary-rgb:196,111,59;
      --bs-link-color: var(--brand); --bs-link-hover-color: var(--brand-600);
    }

    html,body{height:100%}
    body{
      font-family:Inter,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;
      background:
        radial-gradient(1200px 600px at -20% -10%, rgba(196,111,59,.08), transparent 60%),
        radial-gradient(1000px 600px at 120% 10%, rgba(78,107,82,.08), transparent 60%),
        var(--bg);
      color:var(--text);
    }
    ::selection{ background:rgba(196,111,59,.28) }
    html{ scroll-behavior:smooth }
    *{ scrollbar-width:thin; scrollbar-color: var(--brand) transparent }
    *::-webkit-scrollbar{ height:10px; width:10px }
    *::-webkit-scrollbar-thumb{ background:linear-gradient(180deg,var(--brand),var(--brand-600)); border-radius:10px }
    *:focus-visible{ outline:2px solid var(--brand); outline-offset:2px }

    #wrapper{ display:flex; min-height:100% }

    /* ====== Sidebar ====== */
    #sidebar-wrapper{
      width:var(--sidebar-w); min-height:100vh;
      background:linear-gradient(180deg, var(--wood-800) 0%, var(--wood-900) 100%);
      color:#e7e3df; position:sticky; top:0; left:0; z-index:1030;
      transition: width .25s ease, transform .25s ease;
      box-shadow: inset -1px 0 0 rgba(255,255,255,.04);
    }
    #wrapper.collapsed #sidebar-wrapper{ width:var(--sidebar-w-mini) }

    .sidebar-heading{
      padding:1rem 1.25rem; font-weight:800; letter-spacing:.3px;
      display:flex; align-items:center; gap:.75rem; white-space:nowrap;
      border-bottom:1px solid rgba(255,255,255,.06);
    }
    .sidebar-heading .logo{
      width:40px; height:40px; border-radius:12px; display:grid; place-items:center;
      background:linear-gradient(135deg, rgba(196,111,59,.2), rgba(196,111,59,.05));
      box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
    }

    .nav-section-title{
      font-size:.72rem; letter-spacing:.12em; opacity:.6;
      padding:.9rem 1.1rem .4rem; text-transform:uppercase; white-space:nowrap;
    }
    .nav-list{ padding:.35rem .4rem 1rem }

    /* chỉ áp cho link trong sidebar */
    .nav-list .nav-item{
      --item-padding:.7rem;
      display:flex; align-items:center; gap:.7rem; white-space:nowrap;
      color:#e7e3df; text-decoration:none; padding:var(--item-padding) .9rem;
      border-radius:12px; margin:.18rem .6rem; position:relative; overflow:hidden;
      transition:.18s ease transform, .25s ease background, .18s ease color, box-shadow .2s;
    }
    .nav-list .nav-item i{ width:1.4rem; text-align:center; font-size:1.08rem; opacity:.95 }
    .nav-list .nav-item .text{ flex:1 }
    .nav-list .nav-item::before{ content:''; position:absolute; inset:0; border-left:3px solid transparent }
    .nav-list .nav-item:hover{ background:rgba(255,255,255,.06); transform:translateY(-1px) }
    .nav-list .nav-item:active{ transform:translateY(0) }
    .nav-list .nav-item.active{
      background:linear-gradient(90deg, rgba(196,111,59,.16), transparent 60%);
      box-shadow:0 0 0 2px var(--ring) inset;
    }
    .nav-list .nav-item.active::before{ border-left-color: var(--brand) }

    /* thu gọn label khi mini (desktop) */
    #wrapper.collapsed #sidebar-wrapper .nav-section-title,
    #wrapper.collapsed #sidebar-wrapper .sidebar-heading .brand-name,
    #wrapper.collapsed .nav-list .nav-item .text { width:0; opacity:0; margin:0; padding:0; overflow:hidden }
    #wrapper.collapsed .sidebar-heading{ justify-content:center }
    #wrapper.collapsed .nav-list .nav-item{ justify-content:center; padding:.7rem 0 }

    /* ====== Content & Navbar ====== */
    #page-content-wrapper{ flex:1; min-width:0 }
    .app-navbar{
      position:sticky; top:0; z-index:1020;
      background: rgba(255,255,255,.75); backdrop-filter: blur(8px);
      border-bottom:1px solid rgba(32,25,21,.08);
      overflow: visible; /* không cắt dropdown */
    }
    .navbar .dropdown-menu{ z-index:2000 }                 /* nổi trên quick search */
    .navbar-nav .nav-item{ overflow:visible !important; position:relative } /* không bị cắt */

    .content{ padding:1.25rem }

    .card{
      background:linear-gradient(180deg, rgba(234,223,206,.25), transparent), var(--card);
      border:0; border-radius: var(--radius); box-shadow: var(--shadow);
    }
    .card-header{ border-bottom:1px dashed rgba(32,25,21,.1); background:transparent }

    .btn-primary{ background:var(--brand); border-color:var(--brand) }
    .btn-primary:hover{ background:var(--brand-600); border-color:var(--brand-600) }
    .btn-outline-primary{ color:var(--brand); border-color:var(--brand) }
    .btn-outline-primary:hover{ background:var(--brand); color:#fff }

    /* Ripple cho nút icon nhỏ */
    .ripple{ position:relative; overflow:hidden }
    .ripple:after{
      content:''; position:absolute; inset:auto; width:0; height:0; border-radius:50%;
      background: rgba(255,255,255,.35); transform: translate(-50%,-50%); opacity:0; pointer-events:none;
    }

    /* Search */
    .search-wrap{ position:relative; max-width:560px; width:100% }
    .search-icon{ position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:#9a887f }
    .search-input{ padding-left:2.25rem; border-radius:12px; background:#fff6ed; border:1px solid rgba(196,111,59,.25) }
    .quick-box{ position:absolute; left:0; right:0; top:100%; margin-top:.4rem; z-index:1050; display:none }
    .quick-results{ max-height:320px; overflow:auto; border-radius:12px; box-shadow:0 12px 28px rgba(0,0,0,.15) }

    /* Footer */
    footer.app-footer{ color:var(--muted); font-size:.875rem; padding:1.25rem }

    /* Soft badges */
    .badge.bg-warning-soft{ background:#fff0d6; color:#8a5b1f }
    .badge.bg-info-soft   { background:#e6f1ff; color:#0b4a8b }
    .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
    .badge.bg-danger-soft { background:#fde7e7; color:#992f2f }
    .badge.bg-primary-soft{ background:#eaf0ff; color:#2c4da0 }

    /* Back-to-top */
    #backTop{
      position:fixed; right:18px; bottom:18px; z-index:1090; opacity:0; pointer-events:none;
      transition:opacity .2s, transform .2s; transform: translateY(6px);
    }
    #backTop.show{ opacity:1; pointer-events:auto; transform: translateY(0) }

    /* Mobile (off-canvas) */
    @media (max-width: 991.98px){
      #sidebar-wrapper{
        position:fixed; inset:0 auto 0 0; width:var(--sidebar-w);
        transform: translateX(-100%); box-shadow: 8px 0 30px rgba(0,0,0,.25);
      }
      #wrapper.show-sidebar #sidebar-wrapper{ transform: translateX(0) }
      #wrapper.collapsed #sidebar-wrapper{ transform: translateX(-100%) }
      .app-navbar .container-fluid{ gap:.5rem }
      .search-wrap{ max-width:none }
    }

    /* Phím tắt kiểu keyboard */
    .kbd{
      display:inline-flex; align-items:center; justify-content:center;
      min-width:1.35rem; padding:.1rem .35rem;
      font-size:.75rem; line-height:1.2; font-weight:600;
      background:#fff; border:1px solid rgba(0,0,0,.12); border-bottom-width:2px;
      border-radius:6px; box-shadow: inset 0 -1px 0 rgba(0,0,0,.08);
    }
    /* Dropdown không quá cao */
    .navbar .dropdown-menu{ max-height: calc(100vh - 120px); overflow:auto }
  </style>

  {{-- styles từ các view con (nếu có) --}}
  @stack('styles')
</head>
<body>
<div id="wrapper" class="{{ session('sidebar_collapsed') ? 'collapsed' : '' }}">
  {{-- ============ Sidebar ============ --}}
  <aside id="sidebar-wrapper" aria-label="Sidebar">
    <div class="sidebar-heading">
      <span class="logo"><i class="bi bi-flower3 fs-5 text-warning"></i></span>
      <span class="brand-name">Eterna Admin</span>
    </div>

    <div class="nav-section-title">Tổng quan</div>
    <nav class="nav-list" role="navigation">
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-bs-toggle="tooltip" title="Dashboard">
        <i class="bi bi-speedometer2"></i><span class="text">Tổng quan</span>
      </a>
    </nav>

    <div class="nav-section-title">Bán hàng</div>
    <nav class="nav-list">
      <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><i class="bi bi-receipt"></i><span class="text">Đơn hàng</span></a>
      <a href="{{ route('admin.products.index') }}" class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="bi bi-box-seam"></i><span class="text">Sản phẩm</span></a>
      <a href="{{ route('admin.reviews.index') }}" class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"><i class="bi bi-chat-square-text"></i><span class="text">Đánh giá</span></a>
      <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-layers"></i><span class="text">Danh mục</span></a>
      <a href="{{ route('admin.brands.index') }}" class="nav-item {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"><i class="bi bi-bag"></i><span class="text">Hãng</span></a>
      <a href="{{ route('admin.suppliers.index') }}" class="nav-item {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}"><i class="bi bi-truck"></i><span class="text">Nhà cung cấp</span></a>
      <a href="{{ route('admin.inventories.index') }}" class="nav-item {{ request()->routeIs('admin.inventories.*') ? 'active' : '' }}"><i class="bi bi-archive"></i><span class="text">Kho</span></a>
      <a href="{{ route('admin.vouchers.index') }}" class="nav-item {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}"><i class="bi bi-ticket-perforated"></i><span class="text">Voucher</span></a>
    </nav>

    <div class="nav-section-title">Nội dung</div>
    <nav class="nav-list">
      <a href="{{ route('admin.blog-categories.index') }}" class="nav-item {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}"><i class="bi bi-folder2-open"></i><span class="text">Danh mục bài viết</span></a>
      <a href="{{ route('admin.blogs.index') }}" class="nav-item {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i><span class="text">Bài viết</span></a>
      <a href="{{ route('admin.slides.index') }}" class="nav-item {{ request()->routeIs('admin.slides.*') ? 'active' : '' }}"><i class="bi bi-images"></i><span class="text">Slides</span></a>
    </nav>

    <div class="nav-section-title">Hỗ trợ</div>
    <nav class="nav-list">
      @if(\Illuminate\Support\Facades\Route::has('admin.support_tickets.index'))
        <a href="{{ route('admin.support_tickets.index') }}" class="nav-item d-flex justify-content-between align-items-center {{ request()->routeIs('admin.support_tickets.*') ? 'active' : '' }}">
          <span class="d-flex align-items-center gap-2"><i class="bi bi-life-preserver"></i><span class="text">Hỗ trợ KH</span></span>
          @php
            try { $eh_supportOpenCount = \App\Models\SupportTicket::where('status','open')->count(); }
            catch (\Throwable $e) { $eh_supportOpenCount = null; }
          @endphp
          @if(!is_null($eh_supportOpenCount) && $eh_supportOpenCount > 0)
            <span class="badge rounded-pill text-bg-danger">{{ $eh_supportOpenCount }}</span>
          @endif
        </a>
      @endif
    </nav>

    <div class="nav-section-title">Người dùng & Quyền</div>
    <nav class="nav-list pb-4">
      <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="bi bi-person-lines-fill"></i><span class="text">Người dùng</span></a>
      <a href="{{ route('admin.staffs.index') }}" class="nav-item {{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}"><i class="bi bi-people"></i><span class="text">Nhân viên</span></a>
      <a href="{{ route('admin.admins.index') }}" class="nav-item {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i><span class="text">Admin</span></a>
      @if(\Illuminate\Support\Facades\Route::has('admin.permissions.index'))
        <a href="{{ route('admin.permissions.index') }}" class="nav-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}"><i class="bi bi-shield-lock"></i><span class="text">Quyền</span></a>
      @endif
      <a href="{{ route('admin.route-permissions.index') }}" class="nav-item {{ request()->routeIs('admin.route-permissions.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i><span class="text">Route → Quyền</span></a>
      <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><i class="bi bi-gear-wide-connected"></i><span class="text">Cài đặt</span></a>
    </nav>
  </aside>

  {{-- Overlay cho mobile --}}
  <div id="sidebarBackdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1025;" aria-hidden="true"></div>

  {{-- ============ Content ============ --}}
  <div id="page-content-wrapper">
    <nav class="navbar app-navbar navbar-expand-lg">
      <div class="container-fluid py-2">
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-primary border-0 ripple" id="sidebarToggle" type="button" aria-label="Toggle sidebar" title="Thu gọn / Mở rộng">
            <i class="bi bi-layout-sidebar-inset"></i>
          </button>
        </div>

        {{-- Search box --}}
        <div class="flex-grow-1 px-lg-3 d-none d-md-block">
          <div class="search-wrap mx-auto">
            <i class="bi bi-search search-icon"></i>
            <input id="quickSearch" class="form-control form-control-sm search-input" placeholder="Tìm nhanh (Ctrl+/) – gõ: Đơn hàng, Sản phẩm, Báo cáo..." autocomplete="off">
            <div class="quick-box" id="quickBox">
              <div class="quick-results bg-white border rounded-3 overflow-hidden">
                <div class="list-group list-group-flush" id="quickList"></div>
              </div>
            </div>
          </div>
        </div>

        <ul class="navbar-nav ms-auto align-items-center gap-2">
          <li class="nav-item d-none d-md-inline">
            <span class="text-muted small me-2">Phím tắt</span>
            <span class="kbd">Ctrl</span><span class="mx-1">+</span><span class="kbd">/</span>
            <span class="mx-2">•</span>
            <span class="kbd">Ctrl</span><span class="mx-1">+</span><span class="kbd">K</span>
          </li>

          {{-- User Dropdown --}}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center"
               href="#"
               id="userMenu"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false"
               data-bs-auto-close="outside">
              <span class="position-relative me-2">
                <img src="{{ optional(Auth::user()->profile)->avatar ? asset('storage/' . Auth::user()->profile->avatar) : 'https://via.placeholder.com/32' }}"
                     class="rounded-circle" width="34" height="34" alt="Avatar">
                <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-light rounded-circle"></span>
              </span>
              <span class="fw-600">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
              <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Hồ sơ</a></li>
              <li><a class="dropdown-item" href="{{ route('admin.reports.dashboard') }}"><i class="bi bi-graph-up-arrow me-2"></i>Báo cáo</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Đăng xuất khỏi hệ thống?')">@csrf
                  <button class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</button>
                </form>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>

    {{-- Flash toasts --}}
    <div class="toast-container position-fixed top-0 end-0 p-3">
      @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 show mb-2" role="alert">
          <div class="d-flex">
            <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>
      @endif
      @if ($errors->any())
        <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
          <div class="d-flex">
            <div class="toast-body">
              <i class="bi bi-exclamation-triangle me-2"></i>
              @foreach ($errors->all() as $e) {{ $e }} @break @endforeach
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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
        © {{ date('Y') }} • Eterna Admin • Laravel + Bootstrap
      </footer>
    </main>
  </div>
</div>

{{-- Command Palette (Ctrl+K) --}}
<div class="modal fade" id="cmdPalette" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:720px">
    <div class="modal-content" style="border-radius:16px; overflow:hidden; background:var(--card); box-shadow:0 30px 60px rgba(0,0,0,.25)">
      <div class="position-relative border-bottom">
        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
        <input id="cmdInput" class="w-100" style="border:0; outline:none; padding:.9rem 2.75rem .9rem 2.25rem; background:transparent" placeholder="Tìm lệnh / trang… (Đơn hàng, Sản phẩm, Báo cáo…)" autocomplete="off" />
        <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted small d-none d-md-inline">Esc</span>
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
  // Links cho Quick Search + Command Palette
  $q = [];
  $add = function(bool $ok, string $text, string $route, string $icon) use (&$q){
    if($ok){ $q[] = ['label'=>$text, 'url'=>route($route), 'icon'=>$icon]; }
  };
  $add(true, 'Bảng điều khiển', 'admin.dashboard', 'bi-speedometer2');
  $add(\Illuminate\Support\Facades\Route::has('admin.orders.index'), 'Quản lý Đơn hàng', 'admin.orders.index', 'bi-receipt');
  $add(\Illuminate\Support\Facades\Route::has('admin.products.index'), 'Sản phẩm', 'admin.products.index', 'bi-box-seam');
  $add(\Illuminate\Support\Facades\Route::has('admin.categories.index'), 'Danh mục', 'admin.categories.index', 'bi-layers');
  $add(\Illuminate\Support\Facades\Route::has('admin.brands.index'), 'Hãng', 'admin.brands.index', 'bi-bag');
  $add(\Illuminate\Support\Facades\Route::has('admin.inventories.index'), 'Kho', 'admin.inventories.index', 'bi-archive');
  $add(\Illuminate\Support\Facades\Route::has('admin.vouchers.index'), 'Voucher', 'admin.vouchers.index', 'bi-ticket-perforated');
  $add(\Illuminate\Support\Facades\Route::has('admin.users.index'), 'Người dùng', 'admin.users.index', 'bi-person-lines-fill');
  $add(\Illuminate\Support\Facades\Route::has('admin.staffs.index'), 'Nhân viên', 'admin.staffs.index', 'bi-people');
  $add(\Illuminate\Support\Facades\Route::has('admin.permissions.index'), 'Quyền', 'admin.permissions.index', 'bi-shield-lock');
  $add(\Illuminate\Support\Facades\Route::has('admin.route-permissions.index'), 'Route ↔ Quyền', 'admin.route-permissions.index', 'bi-diagram-3');
  $add(\Illuminate\Support\Facades\Route::has('admin.reports.dashboard'), 'Báo cáo', 'admin.reports.dashboard', 'bi-graph-up-arrow');
  $add(\Illuminate\Support\Facades\Route::has('admin.settings.index'), 'Cài đặt', 'admin.settings.index', 'bi-gear-wide-connected');
@endphp
<script>
(function(){
  const wrapper = document.getElementById('wrapper');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const backTop = document.getElementById('backTop');
  const backdrop = document.getElementById('sidebarBackdrop');
  const quick = @json($q);
  const isMobile = () => window.innerWidth < 992;

  // Tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el=>new bootstrap.Tooltip(el));

  // Sidebar state (desktop)
  const keySide = 'admin.sidebar.collapsed';
  if (!isMobile() && localStorage.getItem(keySide)==='1') wrapper.classList.add('collapsed');

  // Toggle sidebar
  sidebarToggle?.addEventListener('click', (e)=>{
    ripple(e);
    if (isMobile()){
      wrapper.classList.toggle('show-sidebar');
      backdrop.style.display = wrapper.classList.contains('show-sidebar') ? 'block' : 'none';
    }else{
      wrapper.classList.toggle('collapsed');
      localStorage.setItem(keySide, wrapper.classList.contains('collapsed') ? '1':'0');
    }
  });

  // Overlay close
  backdrop?.addEventListener('click', ()=>{
    wrapper.classList.remove('show-sidebar');
    backdrop.style.display = 'none';
  });

  // Close on nav click (mobile)
  document.querySelectorAll('#sidebar-wrapper a').forEach(a=>{
    a.addEventListener('click', ()=>{
      if (isMobile()){
        wrapper.classList.remove('show-sidebar');
        backdrop.style.display = 'none';
      }
    });
  });

  // Back to top
  window.addEventListener('scroll', ()=>{
    if(window.scrollY>300){ backTop.classList.add('show'); } else { backTop.classList.remove('show'); }
  });
  backTop.addEventListener('click', ()=> window.scrollTo({top:0, behavior:'smooth'}));

  // Ripple
  function ripple(e){
    const btn = e.currentTarget; if(!btn) return;
    btn.classList.add('ripple');
    const rect = btn.getBoundingClientRect();
    const circle = document.createElement('span');
    const d = Math.max(rect.width, rect.height);
    Object.assign(circle.style,{
      width:d+'px', height:d+'px', position:'absolute',
      left:(e.clientX - rect.left)+'px', top:(e.clientY - rect.top)+'px',
      transform:'translate(-50%,-50%)', background:'rgba(255,255,255,.35)',
      borderRadius:'50%', pointerEvents:'none', opacity:'0.6',
      transition:'opacity .6s, width .6s, height .6s'
    });
    btn.appendChild(circle);
    requestAnimationFrame(()=>{ circle.style.width=circle.style.height=(d*1.8)+'px'; circle.style.opacity='0'; });
    setTimeout(()=>circle.remove(),600);
  }

  // ====== Quick Search (Ctrl+/) ======
  const search = document.getElementById('quickSearch');
  const quickBox = document.getElementById('quickBox');
  const quickList = document.getElementById('quickList');

  function renderQuick(qs){
    quickList.innerHTML = '';
    qs.slice(0,8).forEach(l=>{
      const a = document.createElement('a');
      a.href = l.url; a.className = 'list-group-item list-group-item-action d-flex align-items-center';
      a.innerHTML = `<i class="bi ${l.icon} me-2 text-muted"></i> <span>${l.label}</span>`;
      quickList.appendChild(a);
    });
    quickBox.style.display = qs.length ? 'block' : 'none';
  }
  function hideQuickBox(){ quickBox.style.display='none'; }

  search?.addEventListener('input', ()=>{
    const v = search.value.trim().toLowerCase();
    if(!v){ hideQuickBox(); return; }
    renderQuick(quick.filter(x => x.label.toLowerCase().includes(v)));
  });
  search?.addEventListener('focus', ()=>{
    const v = search.value.trim().toLowerCase();
    if(v){ renderQuick(quick.filter(x => x.label.toLowerCase().includes(v))); }
  });
  search?.addEventListener('blur', ()=> setTimeout(hideQuickBox, 150));

  // Shortcuts
  window.addEventListener('keydown',(e)=>{
    if((e.ctrlKey||e.metaKey) && e.key === '/'){ e.preventDefault(); search?.focus(); }
    if((e.ctrlKey||e.metaKey) && (e.key.toLowerCase() === 'k')){ e.preventDefault(); openPalette(); }
    if(e.key === 'Escape'){ closePalette(); hideQuickBox(); }
  });

  // ===== Command Palette (Ctrl+K) =====
  const cmdModal = new bootstrap.Modal(document.getElementById('cmdPalette'));
  const cmdInput = document.getElementById('cmdInput');
  const cmdList  = document.getElementById('cmdList');
  let activeIndex = -1;

  function openPalette(){ cmdModal.show(); setTimeout(()=>{ cmdInput.value=''; renderCmd(quick); activeIndex=-1; cmdInput.focus(); }, 120); }
  function closePalette(){ try{ cmdModal.hide(); }catch{} }

  function renderCmd(items){
    cmdList.innerHTML='';
    if(!items.length){ cmdList.innerHTML = `<div class="text-center text-muted py-3">Không tìm thấy mục phù hợp</div>`; return; }
    items.slice(0,10).forEach((it,idx)=>{
      const li = document.createElement('div');
      li.className = 'd-flex align-items-center gap-2 p-2 rounded-3 cmd-item';
      li.dataset.url = it.url; li.style.cursor = 'pointer';
      li.innerHTML = `<span style="width:28px;text-align:center"><i class="bi ${it.icon}"></i></span><span>${it.label}</span>`;
      li.addEventListener('click', ()=>{ window.location.href = it.url; });
      if(idx===activeIndex) li.style.background = 'rgba(196,111,59,.12)';
      cmdList.appendChild(li);
    });
  }
  cmdInput?.addEventListener('input', ()=>{
    const v = cmdInput.value.trim().toLowerCase();
    const items = v ? quick.filter(x=>x.label.toLowerCase().includes(v)) : quick;
    activeIndex = -1; renderCmd(items);
  });
  cmdInput?.addEventListener('keydown', (e)=>{
    const items = [...cmdList.querySelectorAll('.cmd-item')];
    if(['ArrowDown','ArrowUp','Enter'].includes(e.key)){ e.preventDefault(); }
    if(e.key==='ArrowDown'){ activeIndex = Math.min(activeIndex+1, items.length-1); }
    if(e.key==='ArrowUp'){ activeIndex = Math.max(activeIndex-1, 0); }
    if(e.key==='Enter' && items[activeIndex]){ window.location.href = items[activeIndex].dataset.url; return; }
    items.forEach((it,i)=> it.style.background = i===activeIndex ? 'rgba(196,111,59,.12)' : '');
  });

  // Ngăn '#' nhảy đầu trang + ẩn quickBox khi mở dropdown
  document.querySelectorAll('[data-bs-toggle="dropdown"][href="#"]').forEach(el=>{
    el.addEventListener('click', e => e.preventDefault());
  });
  document.addEventListener('show.bs.dropdown', () => {
    const box = document.getElementById('quickBox'); if (box) box.style.display = 'none';
  });

  // Resize: rời mobile → ẩn overlay
  window.addEventListener('resize', ()=>{
    if (!isMobile()){
      wrapper.classList.remove('show-sidebar');
      backdrop.style.display = 'none';
    }
  });
})();
</script>

{{-- Tự ẩn toast sau 2.6s --}}
<script>
  document.querySelectorAll('.toast').forEach(el=>{
    try{ new bootstrap.Toast(el, { delay: 2600 }).show(); }catch(e){}
  });
</script>

{{-- ============ KILL-SWITCH PHÂN TRANG (đặt CUỐI TRANG) ============ --}}
<style id="kill-pagination-arrows">
  /* Dập mọi pseudo-element mà extension/script ngoài có thể chèn vào prev/next */
  .pagination::before, .pagination::after,
  .pagination *::before, .pagination *::after,
  .page-item::before, .page-item::after,
  .page-item .page-link::before, .page-item .page-link::after,
  a[rel="prev"]::before, a[rel="prev"]::after,
  a[rel="next"]::before, a[rel="next"]::after,
  .previous::before, .previous::after,
  .next::before, .next::after,
  .prev::before, .prev::after,
  .dataTables_paginate *::before, .dataTables_paginate *::after{
    content: none !important;
    background: none !important;
    border: 0 !important;
    box-shadow: none !important;
    position: static !important;
    display: inline !important;
    width:auto !important; height:auto !important;
  }
  .pagination { gap: .25rem }
  .page-item .page-link { position: static !important }
</style>

@stack('scripts')
</body>
</html>
