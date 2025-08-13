@php
  /** Chỉ lấy DANH MỤC BLOG, không dùng biến $categories từ bên ngoài */
  use App\Models\BlogCategory;

  $blogCats = BlogCategory::query()
      ->where('is_active', 1)
      ->orderBy('sort_order')
      ->orderBy('name')
      ->get();
@endphp

<div class="dropdown-menu blogmega p-0" id="blogmega" aria-labelledby="blogDropdown">
  <div class="blogmega-inner">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <a href="{{ route('blog.index') }}"
         class="blogmega-all {{ request()->routeIs('blog.index') && !request('danh-muc') ? 'active' : '' }}">
        Tất cả bài viết
      </a>
    </div>

    @if($blogCats->isNotEmpty())
      <div class="blogmega-grid">
        @foreach($blogCats as $c)
          <a class="blogmega-item {{ request('danh-muc') === $c->slug ? 'active' : '' }}"
             href="{{ route('blog.index', ['danh-muc' => $c->slug]) }}"
             title="{{ $c->name }}">
            {{ $c->name }}
          </a>
        @endforeach
      </div>
    @endif
  </div>
</div>

{{-- Đặt style ngay tại component (độc lập, chắc chắn áp dụng) --}}
<style>
  /* Đồng bộ với mega Sản phẩm */
  .dropdown-menu.blogmega{
    position:absolute; top:calc(100% + 10px); left:0; transform:none;
    display:block; margin:0; opacity:0; visibility:hidden; pointer-events:none;
    width:min(720px, 96vw); z-index:1060;
    background:#fff; border:1px solid rgba(15,23,42,.10);
    border-radius:14px; box-shadow:0 18px 48px rgba(2,6,23,.20);
    overflow:clip;
  }
  .dropdown-menu.blogmega.show{ opacity:1; visibility:visible; pointer-events:auto; transition:opacity .15s ease, transform .18s ease; }
  .dropdown-menu.blogmega[data-bs-popper]{ left:0 !important; top:auto !important; transform:none !important; margin:0 !important; }

  .blogmega-inner{ padding:16px 18px; }

  .blogmega-all{
    display:inline-block; font-weight:700; color:#111827; text-decoration:none;
    padding:8px 10px; border-radius:8px;
  }
  .blogmega-all:hover{ color:#c1126b; background:rgba(193,18,107,.08); }
  .blogmega-all.active{ color:#c1126b; }

  .blogmega-grid{
    display:grid; gap:10px 16px;
    grid-template-columns: repeat(4, minmax(150px,1fr));
  }
  @media (max-width:1199.98px){ .blogmega-grid{ grid-template-columns:repeat(3, minmax(150px,1fr)); } }
  @media (max-width:767.98px){  .blogmega-grid{ grid-template-columns:repeat(2, minmax(140px,1fr)); } }

  .blogmega-item{
    display:block; padding:8px 10px; border-radius:8px;
    color:#374151; text-decoration:none; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .blogmega-item:hover{ background:rgba(193,18,107,.08); color:#c1126b; }
  .blogmega-item.active{ color:#c1126b; font-weight:600; }

  /* Tránh bị ancestor cắt */
  nav.navbar, nav.navbar * { overflow:visible !important; }
</style>

<script>
  // Canh GIỮA dưới nút "Tin tức" (id blogDropdown)
  (function () {
    const nav = document.querySelector('nav.navbar');
    const trigger = document.getElementById('blogDropdown');
    const menu = document.getElementById('blogmega');
    if (!nav || !trigger || !menu) return;

    function place() {
      const navRect = nav.getBoundingClientRect();
      const tRect   = trigger.getBoundingClientRect();
      const mWidth  = Math.min(720, window.innerWidth * 0.96);
      const centerX = tRect.left + tRect.width/2;
      let left      = centerX - mWidth/2;

      // Neo biên
      const minLeft = 8, maxLeft = window.innerWidth - mWidth - 8;
      left = Math.max(minLeft, Math.min(maxLeft, left));

      const leftInNav = left - navRect.left;
      menu.style.width = mWidth + 'px';
      menu.style.left  = Math.round(leftInNav) + 'px';
      menu.style.top   = Math.round(tRect.bottom - navRect.top + 10) + 'px';
    }

    trigger.addEventListener('shown.bs.dropdown', () => {
      if (!menu.classList.contains('show')) menu.classList.add('show');
      place();
    });
    trigger.addEventListener('hide.bs.dropdown', () => {
      menu.classList.remove('show');
    });
    window.addEventListener('resize', () => { if (menu.classList.contains('show')) place(); });
    window.addEventListener('scroll', () => { if (menu.classList.contains('show')) place(); }, { passive:true });
  })();
</script>
