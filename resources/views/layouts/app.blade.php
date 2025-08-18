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


  <style>
    :root {
      --brand: #A20E38;
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

    /* ===== Mega menu (catmega) – canh giữa theo trigger ===== */
    .navbar {
      position: relative;
    }

    /* toạ độ tuyệt đối của menu bám theo navbar */
    .navbar * {
      overflow: visible !important;
    }

    /* tránh ancestor cắt menu */
    .nav-item.dropdown.mega {
      position: static !important;
    }

    /* để .dropdown-menu có thể thoát khỏi li */

    .dropdown-menu.catmega {
      position: absolute;
      top: calc(100% + 10px);
      left: 0;
      transform: none;
      display: block;
      margin: 0;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      width: min(1200px, 96vw);
      background: #fff;
      border: 1px solid rgba(15, 23, 42, .10);
      border-radius: 14px;
      box-shadow: 0 18px 48px rgba(2, 6, 23, .20);
      z-index: 1060;
      overflow: clip;
    }

    /* Vô hiệu hoá Popper của Bootstrap với menu này */
    .dropdown-menu.catmega[data-bs-popper] {
      left: 0 !important;
      top: auto !important;
      transform: none !important;
      margin: 0 !important;
    }

    .dropdown-menu.catmega.show {
      opacity: 1;
      visibility: visible;
      pointer-events: auto;
      transition: opacity .18s ease;
    }

    /* Lưới cột */
    .catmega-inner {
      padding: 16px 18px;
    }

    .catmega-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px 24px;
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
      color: #c1126b;
    }

    .catmega-list {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .catmega-link {
      display: block;
      padding: 6px 8px;
      border-radius: 8px;
      color: #374151;
      text-decoration: none;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .catmega-link:hover {
      background: rgba(193, 18, 107, .08);
      color: #c1126b;
    }

    @media (max-width:991.98px) {
      .dropdown-menu.catmega {
        width: 100vw;
        left: 50% !important;
        transform: translateX(-50%) !important;
        border-radius: 0 0 14px 14px;
      }

      .catmega-inner {
        padding: 12px 14px;
      }

      .catmega-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 16px;
      }

      .catmega-col {
        border-right: 0;
      }
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
      transition: opacity .28s ease;
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
      top: 2rem;
      right: 2rem;
      font-size: 3rem;
      color: #333;
      background: none;
      border: 0;
      line-height: 1;
    }

    .search-overlay.search-overlay-dark .btn-close-search {
      color: #fff;
    }

    .form-control-overlay {
      width: min(92vw, 600px);
      background: transparent;
      border: 0;
      border-bottom: 2px solid #ccc;
      font-size: 2.25rem;
      text-align: center;
      padding: 1rem 0;
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
    }

    .search-overlay.search-overlay-dark .form-text-overlay,
    .search-overlay.search-overlay-dark .form-text-overlay a {
      color: #fff;
    }

    /* ===== Nhỏ gọn thêm ===== */
    .product-card {
      transition: transform .2s ease, box-shadow .2s ease;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    @keyframes pop {
      0% {
        transform: scale(1)
      }

      50% {
        transform: scale(1.25)
      }

      100% {
        transform: scale(1)
      }
    }

    .no-scroll {
      overflow: hidden;
    }
  </style>

  @stack('styles')
</head>

<body class="{{ request()->routeIs('home') ? 'is-home' : 'is-internal' }}">
  {{-- Header: home = trong suốt; internal = sticky --}}
  @if(request()->routeIs('home'))
    @include('layouts.partials.header-home')
  @else
    @include('layouts.partials.header-internal')
  @endif

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
      if (window.AOS) AOS.init({ duration: 800, once: true });

      // Search overlay
      (function () {
        const overlay = document.getElementById('search-overlay');
        if (!overlay) return;
        const input = overlay.querySelector('input[name="q"]');
        const btnClose = document.getElementById('search-close-btn');
        const open = () => { overlay.classList.add('active'); document.body.classList.add('no-scroll'); setTimeout(() => input?.focus(), 120); overlay.setAttribute('aria-hidden', 'false'); };
        const close = () => { overlay.classList.remove('active'); document.body.classList.remove('no-scroll'); overlay.setAttribute('aria-hidden', 'true'); };
        document.querySelectorAll('.search-toggle-btn').forEach(b => b.addEventListener('click', e => { e.preventDefault(); open(); }));
        btnClose?.addEventListener('click', close);
        overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && overlay.classList.contains('active')) close(); });
      })();

      // Wishlist toggle (giữ API)
      document.body.addEventListener('click', async (e) => {
        const btn = e.target.closest('.toggle-wishlist-btn, .wishlist-icon-component');
        if (!btn) return;
        e.preventDefault();
        const productId = btn.dataset.productId;
        const icon = btn.querySelector('i');
        if (icon) { icon.style.animation = 'pop .4s ease'; setTimeout(() => icon.style.animation = '', 400); }

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

            // Nếu ở trang danh sách yêu thích và vừa bỏ thích thì ẩn card
            if (window.location.pathname.includes('/danh-sach-yeu-thich') && !isAdded) {
              const card = btn.closest('.product-card-wrapper, .col');
              if (card) { card.style.transition = 'opacity .3s ease'; card.style.opacity = 0; setTimeout(() => card.remove(), 300); }
            }
          } else if (data.redirect) {
            window.location.href = data.redirect;
          }
        } catch (err) { if (err?.message !== 'Failed to fetch') console.error(err); }
      });

      // GHN address selects (nếu tồn tại)
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

      // ===== CANH GIỮA MEGA MENU NGAY DƯỚI "SẢN PHẨM" =====
      (function () {
        const nav = document.querySelector('nav.navbar');      // navbar bao quanh dropdown
        const trigger = document.getElementById('productsDropdown'); // nút "Sản phẩm"
        const menu = document.getElementById('catmega');          // <div class="dropdown-menu catmega" ...>
        if (!(nav && trigger && menu)) return;

        const place = () => {
          const navRect = nav.getBoundingClientRect();
          const tRect = trigger.getBoundingClientRect();

          const mWidth = Math.min(1200, window.innerWidth * 0.96);
          const centerX = tRect.left + tRect.width / 2;
          let left = centerX - mWidth / 2;

          // Clamp trong viewport
          const minLeft = 8, maxLeft = window.innerWidth - mWidth - 8;
          left = Math.max(minLeft, Math.min(maxLeft, left));

          // Toạ độ bên trong navbar
          const leftInNav = left - navRect.left;

          menu.style.width = mWidth + 'px';
          menu.style.left = Math.round(leftInNav) + 'px';
          menu.style.top = Math.round(tRect.bottom - navRect.top + 10) + 'px';
        };

        trigger.addEventListener('shown.bs.dropdown', () => { if (!menu.classList.contains('show')) menu.classList.add('show'); place(); });
        trigger.addEventListener('hide.bs.dropdown', () => menu.classList.remove('show'));
        window.addEventListener('resize', () => { if (menu.classList.contains('show')) place(); });
        window.addEventListener('scroll', () => { if (menu.classList.contains('show')) place(); }, { passive: true });
      })();

    }); // DOMContentLoaded
  </script>

  @stack('scripts-page')
</body>

</html>