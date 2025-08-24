<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', $settings['site_name'] ?? config('app.name', 'Laravel'))</title>

  @if(!empty($settings['favicon']))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $settings['favicon']) }}">
  @endif

  {{-- Libs --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --brand: #A20E38;
      --brand-600: #8E0D30;
      --brand-700: #6B0A24;
      --accent: #4E6B52;
      --sand: #F6E9EC;
      --text: #2B2623;
      --bg: #FFF6F8;
      --card: #FFFFFF;
      --muted: #7D726C;
      --ring: rgba(162, 14, 56, .18);
      --shadow: 0 10px 30px rgba(32, 25, 21, .08);
      --radius: 12px;
    }

    /* ===== Utilities (brand-aware) ===== */
    .text-brand { color: var(--brand) !important; }
    .text-brand-600 { color: var(--brand-600) !important; }
    .bg-brand { background: var(--brand) !important; color: #fff !important; }
    .border-brand { border-color: var(--brand) !important; }
    
    .btn-brand {
      background: var(--brand);
      color: #fff;
      border: 1px solid var(--brand);
      border-radius: 10px;
      padding: 0.5rem 1rem;
      transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
    }
    .btn-brand:hover {
      background: var(--brand-600);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    }
    
    .btn-outline-brand {
      background: transparent;
      color: var(--brand);
      border: 1px solid var(--brand);
      border-radius: 10px;
      padding: 0.45rem 0.9rem;
      transition: background 0.15s ease, color 0.15s ease;
    }
    .btn-outline-brand:hover {
      background: var(--brand);
      color: #fff;
    }
    
    .btn-ghost {
      background: transparent;
      border: 0;
      color: var(--brand);
    }
    
    .badge-soft-brand {
      background: rgba(162, 14, 56, .08);
      color: var(--brand);
      border-radius: 999px;
      padding: 0.25rem 0.6rem;
      font-size: 0.8rem;
    }
    
    .card-glass {
      background: linear-gradient(180deg, rgba(255, 255, 255, .92), rgba(255, 255, 255, .98));
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      border: 1px solid rgba(15, 23, 42, .04);
    }
    
    .shadow-elevated {
      box-shadow: 0 12px 36px rgba(11, 13, 15, .1);
    }
    
    .table-custom th {
      background: transparent;
      color: var(--muted);
      font-weight: 600;
    }
    .table-custom tbody tr:hover {
      background: rgba(0, 0, 0, .02);
    }
    
    .icon-hover {
      transition: transform 0.15s cubic-bezier(.2, .9, .3, 1), color 0.15s ease;
    }
    .icon-hover:hover {
      transform: translateY(-2px) scale(1.05);
      color: var(--brand);
    }
    
    .focus-ring:focus {
      outline: none;
      box-shadow: 0 0 0 3px var(--ring);
      border-radius: 8px;
    }
    
    .cmd-item {
      padding: 0.5rem 0.75rem;
      border-radius: 8px;
      transition: background 0.15s ease;
    }
    .cmd-item.active,
    .cmd-item:hover {
      background: rgba(162, 14, 56, .06);
    }
    
    .kbd {
      background: #f3f4f6;
      border-radius: 4px;
      padding: 0.15rem 0.4rem;
      font-size: 0.8rem;
    }

    /* ===== Header trong suốt ở trang chủ ===== */
    .is-home .header-home,
    .is-home .header-home .navbar {
      background: transparent !important;
      box-shadow: none !important;
      border: 0 !important;
    }
    .is-home .header-home.is-scrolled {
      background: rgba(0, 0, 0, .45) !important;
      backdrop-filter: blur(6px) saturate(1.1);
    }

    /* ===== Mega menu (catmega) ===== */
    .navbar { position: relative; }
    .navbar * { overflow: visible !important; }
    .nav-item.dropdown.mega { position: static !important; }
    
    .dropdown-menu.catmega {
      position: absolute;
      top: calc(100% + 8px);
      left: 50%;
      transform: translateX(-50%);
      display: block;
      margin: 0;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      width: min(1200px, 96vw);
      background: #fff;
      border: 1px solid rgba(15, 23, 42, .1);
      border-radius: 12px;
      box-shadow: 0 12px 36px rgba(2, 6, 23, .15);
      z-index: 1060;
      overflow: clip;
      transition: opacity 0.2s ease, transform 0.2s ease;
    }
    .dropdown-menu.catmega.show {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
      transform: translateX(-50%) translateY(0);
    }
    .dropdown-menu.catmega[data-bs-popper] {
      left: 50% !important;
      top: auto !important;
      transform: translateX(-50%) !important;
      margin: 0 !important;
    }
    
    .catmega-inner {
      padding: 14px 16px;
    }
    .catmega-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px 20px;
    }
    .catmega-col {
      min-width: 0;
      border-right: 1px solid rgba(2, 6, 23, .06);
    }
    .catmega-col:last-child {
      border-right: 0;
    }
    .catmega-title {
      display: inline-block;
      margin: 0 0 6px;
      font-weight: 700;
      color: #111827;
      text-decoration: none;
    }
    .catmega-title:hover {
      color: var(--brand);
    }
    .catmega-list {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    .catmega-link {
      display: block;
      padding: 5px 8px;
      border-radius: 6px;
      color: #374151;
      text-decoration: none;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .catmega-link:hover {
      background: rgba(162, 14, 56, .08);
      color: var(--brand);
    }

    /* ===== Search overlay ===== */
    .search-overlay {
      position: fixed;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, .98);
      z-index: 1055;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.25s ease;
    }
    .search-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    .search-overlay.search-overlay-dark {
      background: rgba(0, 0, 0, .85);
      backdrop-filter: blur(5px);
    }
    .btn-close-search {
      position: absolute;
      top: 1.5rem;
      right: 1.5rem;
      font-size: 2.5rem;
      color: #333;
      background: none;
      border: 0;
      line-height: 1;
    }
    .search-overlay.search-overlay-dark .btn-close-search {
      color: #fff;
    }
    .form-control-overlay {
      width: min(90vw, 550px);
      background: transparent;
      border: 0;
      border-bottom: 2px solid #ccc;
      font-size: 2rem;
      text-align: center;
      padding: 0.8rem 0;
      outline: none;
      box-shadow: none;
      color: #333;
    }
    .search-overlay.search-overlay-dark .form-control-overlay {
      color: #fff;
      border-bottom-color: rgba(255, 255, 255, .55);
    }
    .search-overlay.search-overlay-dark .form-control-overlay:focus {
      border-bottom-color: #ffc107;
    }
    .form-text-overlay {
      text-align: center;
      color: #6c757d;
      font-size: 0.9rem;
    }
    .search-overlay.search-overlay-dark .form-text-overlay,
    .search-overlay.search-overlay-dark .form-text-overlay a {
      color: #fff;
    }

    /* ===== Product Card ===== */
    .product-card {
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, .1) !important;
    }

    @keyframes pop {
      0% { transform: scale(1); }
      50% { transform: scale(1.2); }
      100% { transform: scale(1); }
    }

    .no-scroll {
      overflow: hidden;
    }

    /* ===== Responsive Design ===== */
    @media (max-width: 991px) {
      .dropdown-menu.catmega {
        width: 100vw;
        left: 50% !important;
        transform: translateX(-50%) !important;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 8px 24px rgba(2, 6, 23, .1);
      }
      .catmega-inner {
        padding: 10px 12px;
      }
      .catmega-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px 16px;
      }
      .catmega-col {
        border-right: 0;
      }
      .form-control-overlay {
        width: min(90vw, 500px);
        font-size: 1.8rem;
        padding: 0.7rem 0;
      }
      .btn-close-search {
        top: 1.2rem;
        right: 1.2rem;
        font-size: 2rem;
      }
    }

    @media (max-width: 767px) {
      .dropdown-menu.catmega {
        top: calc(100% + 6px);
      }
      .catmega-inner {
        padding: 8px 10px;
      }
      .catmega-grid {
        grid-template-columns: 1fr;
        gap: 8px;
      }
      .form-control-overlay {
        width: min(88vw, 450px);
        font-size: 1.6rem;
        padding: 0.6rem 0;
      }
      .form-text-overlay {
        font-size: 0.85rem;
      }
      .btn-brand {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
      }
      .btn-outline-brand {
        padding: 0.35rem 0.7rem;
        font-size: 0.85rem;
      }
    }

    @media (max-width: 575px) {
      .dropdown-menu.catmega {
        top: calc(100% + 4px);
        border-radius: 0 0 10px 10px;
      }
      .catmega-inner {
        padding: 6px 8px;
      }
      .catmega-link {
        padding: 4px 6px;
        font-size: 0.9rem;
      }
      .form-control-overlay {
        width: min(85vw, 400px);
        font-size: 1.4rem;
        padding: 0.5rem 0;
      }
      .btn-close-search {
        top: 1rem;
        right: 1rem;
        font-size: 1.8rem;
      }
      .btn-brand {
        padding: 0.35rem 0.7rem;
        font-size: 0.85rem;
      }
      .btn-outline-brand {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
      }
    }
  </style>

  @stack('styles')
</head>

<body class="{{ request()->routeIs('home') ? 'is-home' : 'is-internal' }}">
  {{-- Header: home = trong suốt; internal = sticky --}}
  @include('layouts.partials.header')

  <main>@yield('content')</main>

  @include('layouts.footer')

  {{-- Search Overlay --}}
  <div id="search-overlay" class="search-overlay {{ request()->routeIs('home') ? 'search-overlay-dark' : '' }}"
    aria-hidden="true" role="dialog" aria-modal="true">
    <button type="button" class="btn-close-search" id="search-close-btn" aria-label="Đóng tìm kiếm">&times;</button>
    <form action="{{ route('search') }}" method="GET" class="search-form-overlay" role="search"
      aria-label="Tìm kiếm sản phẩm">
      <input type="text" class="form-control-overlay" name="q" placeholder="Tìm kiếm sản phẩm..." autocomplete="off"
        required>
      <div class="form-text-overlay mt-3">
        Phổ biến:
        <a href="{{ route('search', ['q' => 'Bàn ghế']) }}">Bàn ghế</a>,
        <a href="{{ route('search', ['q' => 'Giường ngủ']) }}">Giường ngủ</a>
      </div>
    </form>
  </div>

  {{-- Libs --}}
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // AOS
      if (window.AOS) AOS.init({ duration: 600, once: true });

      // Search overlay
      (function () {
        const overlay = document.getElementById('search-overlay');
        if (!overlay) return;
        const input = overlay.querySelector('input[name="q"]');
        const btnClose = document.getElementById('search-close-btn');
        const open = () => { 
          overlay.classList.add('active'); 
          document.body.classList.add('no-scroll'); 
          setTimeout(() => input?.focus(), 100); 
          overlay.setAttribute('aria-hidden', 'false'); 
        };
        const close = () => { 
          overlay.classList.remove('active'); 
          document.body.classList.remove('no-scroll'); 
          overlay.setAttribute('aria-hidden', 'true'); 
        };
        document.querySelectorAll('.search-toggle-btn').forEach(b => b.addEventListener('click', e => { e.preventDefault(); open(); }));
        btnClose?.addEventListener('click', close);
        overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && overlay.classList.contains('active')) close(); });
      })();

      // Wishlist toggle
      document.body.addEventListener('click', async (e) => {
        const btn = e.target.closest('.toggle-wishlist-btn, .wishlist-icon-component');
        if (!btn) return;
        e.preventDefault();
        const productId = btn.dataset.productId;
        const icon = btn.querySelector('i');
        if (icon) { icon.style.animation = 'pop 0.4s ease'; setTimeout(() => icon.style.animation = '', 400); }

        try {
          const res = await fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
          });

          if (res.status === 401) { window.location.href = '{{ route("login.form") }}'; return; }
          const data = await res.json(); if (!data) return;

          if (data.success) {
            const isAdded = data.status === 'added';
            btn.classList.toggle('active', isAdded);
            if (icon) { icon.classList.toggle('bi-heart', !isAdded); icon.classList.toggle('bi-heart-fill', isAdded); }

            const badge = document.getElementById('wishlist-count');
            if (badge) {
              const cur = parseInt(badge.textContent) || 0;
              const next = isAdded ? cur + 1 : Math.max(0, cur - 1);
              badge.textContent = next;
              badge.style.display = next > 0 ? 'inline-block' : 'none';
            }

            if (window.location.pathname.includes('/danh-sach-yeu-thich') && !isAdded) {
              const card = btn.closest('.product-card-wrapper, .col');
              if (card) { card.style.transition = 'opacity 0.3s ease'; card.style.opacity = 0; setTimeout(() => card.remove(), 300); }
            }
          } else if (data.redirect) {
            window.location.href = data.redirect;
          }
        } catch (err) { if (err?.message !== 'Failed to fetch') console.error(err); }
      });

      // GHN address selects
      (function () {
        const provinceSelect = document.getElementById('province_id');
        const districtSelect = document.getElementById('district_id');
        const wardSelect = document.getElementById('ward_code');
        if (!(provinceSelect && districtSelect && wardSelect)) return;

        const provinceNameInput = document.getElementById('province_name_input');
        const districtNameInput = document.getElementById('district_name_input');
        const wardNameInput = document.getElementById('ward_name_input');

        const saved = {
          province: provinceNameInput?.value || '',
          district: districtNameInput?.value || '',
          ward: wardNameInput?.value || ''
        };

        const fetchJson = async (url) => {
          try { const r = await fetch(url, { headers: { 'Accept': 'application/json' } }); return r.ok ? r.json() : []; }
          catch { return []; }
        };
        const renderOptions = (select, list, placeholder, valKey, textKey, pickedText = '') => {
          select.innerHTML = `<option value="">${placeholder}</option>`;
          if (!Array.isArray(list)) return;
          let pickedVal = null;
          for (const item of list) {
            const opt = new Option(item[textKey], item[valKey]);
            if (pickedText && item[textKey] === pickedText) { opt.selected = true; pickedVal = item[valKey]; }
            select.add(opt);
          }
          if (pickedVal) {
            select.value = pickedVal;
            setTimeout(() => select.dispatchEvent(new Event('change', { bubbles: true })), 0);
          }
        };

        const loadProvinces = async () => {
          const provinces = await fetchJson('{{ route("address.provinces") }}');
          renderOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành', 'ProvinceID', 'ProvinceName', saved.province);
        };

        provinceSelect.addEventListener('change', async function () {
          if (provinceNameInput) provinceNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
          renderOptions(districtSelect, [], 'Vui lòng chờ...', 'DistrictID', 'DistrictName');
          renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
          if (this.value) {
            const districts = await fetchJson(`{{ route("address.districts") }}?province_id=${this.value}`);
            renderOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', saved.district);
          }
        });

        districtSelect.addEventListener('change', async function () {
          if (districtNameInput) districtNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
          renderOptions(wardSelect, [], 'Vui lòng chờ...', 'WardCode', 'WardName');
          if (this.value) {
            const wards = await fetchJson(`{{ route("address.wards") }}?district_id=${this.value}`);
            renderOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', saved.ward);
          }
        });

        wardSelect.addEventListener('change', function () {
          if (wardNameInput) wardNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
          if (typeof calculateShippingFee === 'function') calculateShippingFee();
        });

        loadProvinces();
      })();
    });
  </script>

  @stack('scripts-page')
</body>

</html>