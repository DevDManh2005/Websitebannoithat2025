<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', $settings['site_name'] ?? config('app.name', 'Laravel'))</title>

  @if(!empty($settings['favicon']))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/'.$settings['favicon']) }}">
  @endif

  <!-- CSS libs -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- Global styles (kept compact) -->
  <style>
    /* ===== NAVBAR / MEGA MENU FIX ===== */
    /* Treat the mega menu (class .catmega) as a centered overlay below the navbar item */
    .navbar .nav-item.dropdown.position-static { position: static !important; }
    .navbar .nav-item.dropdown.position-static > .dropdown-menu.catmega{
      position:absolute !important; display:none !important;
      top:calc(100% + 12px) !important; left:50% !important; transform:translateX(-50%) !important;
      width:min(1180px,96vw) !important; z-index:1056 !important; margin:0 !important; border:0 !important;
    }
    /* Neutralize Popper offsets when Bootstrap adds data-bs-popper */
    .navbar .nav-item.dropdown.position-static > .dropdown-menu.catmega[data-bs-popper]{
      top:calc(100% + 12px) !important; left:50% !important; margin-top:0 !important; transform:translateX(-50%) !important;
    }
    /* Open on hover (desktop) */
    @media (min-width: 992px){
      .navbar .nav-item.dropdown.position-static:hover > .dropdown-menu.catmega{ display:block !important; }
    }
    /* Open on click (mobile/desktop) */
    .navbar .nav-item.dropdown.position-static.show > .dropdown-menu.catmega,
    .navbar .nav-item.dropdown.position-static > a[aria-expanded="true"] + .dropdown-menu.catmega{
      display:block !important;
    }
    /* Default “glass” shell in case the component didn’t push its own style */
    .dropdown-menu.catmega{
      background:var(--cm-bg,#fff) !important;
      border:1px solid var(--cm-line,rgba(15,23,42,.10)) !important;
      border-radius:14px !important;
      box-shadow:0 24px 40px rgba(16,24,40,.10) !important;
      opacity:0; translate:0 8px; transition:opacity .18s ease, translate .22s ease;
    }
    .dropdown-menu.catmega.is-open{ opacity:1; translate:0 0; }
    .dropdown-menu.catmega ul{ list-style:none; margin:0; padding:0; }

    /* Active underline for current nav item */
    .navbar .nav-link.active{ position:relative; }
    .navbar .nav-link.active::after{
      content:""; position:absolute; left:10px; right:10px; bottom:2px; height:3px;
      border-radius:999px; background:linear-gradient(90deg,#ff4d6d,#c1126b);
    }

    /* ===== SEARCH OVERLAY ===== */
    .search-overlay{
      position:fixed; inset:0; background-color:rgba(255,255,255,.98);
      z-index:1055; display:flex; align-items:center; justify-content:center;
      opacity:0; visibility:hidden; transition:opacity .3s ease;
    }
    .search-overlay.active{ opacity:1; visibility:visible; }
    .search-overlay.search-overlay-dark{ background-color:rgba(0,0,0,.85); backdrop-filter:blur(5px); }
    .btn-close-search{
      position:absolute; top:2rem; right:2rem; font-size:3rem; color:#333; background:none; border:0; cursor:pointer; line-height:1;
    }
    .search-overlay.search-overlay-dark .btn-close-search{ color:#fff; }
    .form-control-overlay{
      width:100%; max-width:600px; border:0; border-bottom:2px solid #ccc; background:transparent;
      font-size:2.5rem; text-align:center; padding:1rem 0; outline:none; box-shadow:none; color:#333;
    }
    .search-overlay.search-overlay-dark .form-control-overlay{ color:#fff; border-bottom-color:rgba(255,255,255,.5); }
    .search-overlay.search-overlay-dark .form-control-overlay:focus{ border-bottom-color:#ffc107; }
    .form-text-overlay{ text-align:center; color:#6c757d; }
    .search-overlay.search-overlay-dark .form-text-overlay,
    .search-overlay.search-overlay-dark .form-text-overlay a{ color:#fff; }

    /* ===== PRODUCT CARD HOVER ===== */
    .product-card{ transition:transform .2s ease, box-shadow .2s ease; }
    .product-card:hover{ transform:translateY(-5px); box-shadow:0 .5rem 1rem rgba(0,0,0,.15) !important; }

    /* Tiny “pop” animation helper (e.g., wishlist) */
    @keyframes pop { 0%{transform:scale(1)} 50%{transform:scale(1.25)} 100%{transform:scale(1)} }
    .no-scroll{ overflow:hidden; }
  </style>

  @stack('styles')
</head>
<body>
  {{-- Header (transparent on home, solid on inner pages) --}}
  @if(request()->routeIs('home'))
    @include('layouts.partials.header-home')
  @else
    @include('layouts.partials.header-internal')
  @endif

  <main>@yield('content')</main>

  @include('layouts.footer')

  {{-- Search Overlay --}}
  <div id="search-overlay"
       class="search-overlay {{ request()->routeIs('home') ? 'search-overlay-dark' : '' }}"
       aria-hidden="true" role="dialog" aria-modal="true">
    <button type="button" class="btn-close-search" id="search-close-btn" aria-label="Đóng tìm kiếm">&times;</button>
    <form action="{{ route('search') }}" method="GET" class="search-form-overlay" role="search" aria-label="Tìm kiếm sản phẩm">
      <input type="text" class="form-control-overlay" name="q" placeholder="Tìm kiếm sản phẩm..." autocomplete="off" required>
      <div class="form-text-overlay mt-3">
        Phổ biến:
        <a href="{{ route('search', ['q' => 'Bàn ghế']) }}">Bàn ghế</a>,
        <a href="{{ route('search', ['q' => 'Giường ngủ']) }}">Giường ngủ</a>
      </div>
    </form>
  </div>

  <!-- JS libs -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // AOS
      if (window.AOS) AOS.init({ duration: 800, once: true });

      // Search overlay
      const overlay = document.getElementById('search-overlay');
      if (overlay) {
        const input = overlay.querySelector('input[name="q"]');
        const closeBtn = document.getElementById('search-close-btn');

        function openSearch() {
          overlay.classList.add('active');
          document.body.classList.add('no-scroll');
          if (input) setTimeout(() => input.focus(), 200);
          overlay.setAttribute('aria-hidden','false');
        }
        function closeSearch() {
          overlay.classList.remove('active');
          document.body.classList.remove('no-scroll');
          overlay.setAttribute('aria-hidden','true');
        }

        document.querySelectorAll('.search-toggle-btn').forEach(btn => {
          btn.addEventListener('click', e => { e.preventDefault(); openSearch(); });
        });
        if (closeBtn) closeBtn.addEventListener('click', closeSearch);
        overlay.addEventListener('click', e => { if (e.target === overlay) closeSearch(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && overlay.classList.contains('active')) closeSearch(); });
      }

      // Wishlist toggle (kept minimal; expects route & CSRF)
      document.body.addEventListener('click', async (e) => {
        const btn = e.target.closest('.toggle-wishlist-btn, .wishlist-icon-component');
        if (!btn) return;
        e.preventDefault();

        const productId = btn.dataset.productId;
        const icon = btn.querySelector('i');
        if (icon){ icon.style.animation='pop .4s ease'; setTimeout(() => icon.style.animation='', 400); }

        try{
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
          const data = await res.json();
          if (!data) return;

          if (data.success){
            const isAdded = data.status === 'added';
            btn.classList.toggle('active', isAdded);
            if (icon){
              icon.classList.toggle('bi-heart', !isAdded);
              icon.classList.toggle('bi-heart-fill', isAdded);
            }
            const badge = document.getElementById('wishlist-count');
            if (badge){
              const cur = parseInt(badge.textContent) || 0;
              const next = isAdded ? cur + 1 : Math.max(0, cur - 1);
              badge.textContent = next;
              badge.style.display = next > 0 ? 'inline-block' : 'none';
            }

            // If on wishlist page and removed, fade out card
            if (window.location.pathname.includes('/danh-sach-yeu-thich') && !isAdded){
              const card = btn.closest('.product-card-wrapper, .col');
              if (card){ card.style.transition='opacity .3s ease'; card.style.opacity=0; setTimeout(() => card.remove(), 300); }
            }
          } else if (data.redirect){
            window.location.href = data.redirect;
          }
        }catch(err){
          if (err?.message !== 'Failed to fetch') console.error('Wishlist Error:', err);
        }
      });

      // GHN address selects (optional if present)
      const provinceSelect = document.getElementById('province_id');
      const districtSelect = document.getElementById('district_id');
      const wardSelect     = document.getElementById('ward_code');

      if (provinceSelect && districtSelect && wardSelect){
        const provinceNameInput = document.getElementById('province_name_input');
        const districtNameInput = document.getElementById('district_name_input');
        const wardNameInput     = document.getElementById('ward_name_input');

        const saved = {
          province: provinceNameInput?.value || '',
          district: districtNameInput?.value || '',
          ward:     wardNameInput?.value || ''
        };

        const fetchJson = async (url) => {
          try{ const r = await fetch(url, { headers:{'Accept':'application/json'} }); return r.ok ? r.json() : []; }
          catch{ return []; }
        };

        const renderOptions = (select, list, placeholder, valKey, textKey, pickedText = '') => {
          select.innerHTML = `<option value="">${placeholder}</option>`;
          if (!Array.isArray(list)) return;
          let pickedVal = null;
          for (const item of list){
            const opt = new Option(item[textKey], item[valKey]);
            if (pickedText && item[textKey] === pickedText){ opt.selected = true; pickedVal = item[valKey]; }
            select.add(opt);
          }
          if (pickedVal){
            select.value = pickedVal;
            setTimeout(() => select.dispatchEvent(new Event('change', { bubbles:true })), 0);
          }
        };

        const loadProvinces = async () => {
          const provinces = await fetchJson('{{ route("address.provinces") }}');
          renderOptions(provinceSelect, provinces, 'Chọn Tỉnh/Thành', 'ProvinceID', 'ProvinceName', saved.province);
        };

        provinceSelect.addEventListener('change', async function(){
          if (provinceNameInput) provinceNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
          renderOptions(districtSelect, [], 'Vui lòng chờ...', 'DistrictID', 'DistrictName');
          renderOptions(wardSelect, [], 'Chọn Phường/Xã', 'WardCode', 'WardName');
          if (this.value){
            const districts = await fetchJson(`{{ route("address.districts") }}?province_id=${this.value}`);
            renderOptions(districtSelect, districts, 'Chọn Quận/Huyện', 'DistrictID', 'DistrictName', saved.district);
          }
        });

        districtSelect.addEventListener('change', async function(){
          if (districtNameInput) districtNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
          renderOptions(wardSelect, [], 'Vui lòng chờ...', 'WardCode', 'WardName');
          if (this.value){
            const wards = await fetchJson(`{{ route("address.wards") }}?district_id=${this.value}`);
            renderOptions(wardSelect, wards, 'Chọn Phường/Xã', 'WardCode', 'WardName', saved.ward);
          }
        });

        wardSelect.addEventListener('change', function(){
          if (wardNameInput) wardNameInput.value = this.selectedIndex > 0 ? this.options[this.selectedIndex].text : '';
          if (typeof calculateShippingFee === 'function') calculateShippingFee();
        });

        loadProvinces();
      }
    });
  </script>

  @stack('scripts-page')
</body>
</html>
