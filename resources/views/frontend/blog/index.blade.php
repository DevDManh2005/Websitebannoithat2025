@extends('layouts.app')

@section('title','Bài viết')

@section('content')
  {{-- ========= HERO ========= --}}
  <section class="blog-hero d-flex align-items-center mb-5">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-10" data-aos="fade-up">
          <h1 class="display-6 fw-bold text-white mb-3">Tin tức &amp; Cảm hứng</h1>
          <p class="lead text-white-50 mb-4">Những câu chuyện, mẹo hay và xu hướng mới cho không gian sống của bạn.</p>

          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center blog-breadcrumb m-0">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
              <li class="breadcrumb-item active" aria-current="page">Bài viết</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
    <span class="hero-overlay"></span>
  </section>

  <div class="container blog-index mb-5">
    <div class="row g-4">
      {{-- ========= SIDEBAR (CATEGORIES) ========= --}}
      <aside class="col-lg-3">
        <div class="position-sticky top-sticky">
          <div class="card border-0 shadow-sm rounded-4" data-aos="fade-right">
            <div class="card-body p-3 p-lg-4">
              @include('frontend.components.blog-categories-sidebar', [
                'categories' => $cats ?? null,
                'activeSlug' => $categorySlug ?? null
              ])
            </div>
          </div>
        </div>
      </aside>

      {{-- ========= MAIN LIST ========= --}}
      <section class="col-lg-9">
        {{-- Toolbar: search + info --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4" data-aos="fade-up">
          <div class="card-body d-flex flex-wrap gap-3 justify-content-between align-items-center">
            <div class="text-secondary small">
              @php
                $from = $posts->total() ? $posts->firstItem() : 0;
                $to   = $posts->total() ? $posts->lastItem()  : 0;
              @endphp
              Hiển thị <strong>{{ $from }}</strong>–<strong>{{ $to }}</strong>
              trong <strong>{{ $posts->total() }}</strong> bài viết
              @if(request('danh-muc'))
                <span class="badge rounded-pill text-bg-light ms-1">Danh mục: {{ request('danh-muc') }}</span>
              @endif
              @if(request('q'))
                <span class="badge rounded-pill text-bg-light ms-1">Từ khóa: “{{ request('q') }}”</span>
              @endif
            </div>

            <form class="ms-auto" method="GET" action="{{ url()->current() }}" style="min-width:320px">
              {{-- giữ lại danh-mục khi tìm kiếm --}}
              @if(request('danh-muc'))
                <input type="hidden" name="danh-muc" value="{{ request('danh-muc') }}">
              @endif
              <div class="input-group">
                <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm bài viết...">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Tìm</button>
              </div>
            </form>
          </div>
        </div>

        {{-- Grid --}}
        @if($posts->isNotEmpty())
          <div class="row g-3 g-md-4 blog-grid" data-aos="fade-up" data-aos-delay="75">
            @foreach($posts as $p)
              <div class="col-6 col-md-6 col-xl-4">
                <a href="{{ route('blog.show', $p->slug) }}" class="text-decoration-none d-block h-100">
                  <div class="card blog-card h-100 shadow-sm">
                    @php
                      $thumb = $p->thumbnail ? asset('storage/'.$p->thumbnail) : 'https://placehold.co/640x420?text=No+Image';
                      $date  = $p->published_at ?? $p->created_at;
                    @endphp
                    <img class="card-img-top blog-thumb" src="{{ $thumb }}" alt="{{ $p->title }}">

                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center xsmall text-muted mb-2">
                        <span class="text-truncate-1">
                          <i class="bi bi-folder2-open me-1"></i>{{ $p->category?->name ?? 'Khác' }}
                        </span>
                        <time datetime="{{ optional($date)->toDateString() }}">
                          <i class="bi bi-calendar-event me-1"></i>{{ optional($date)->format('d/m/Y') }}
                        </time>
                      </div>

                      <h6 class="mb-2 blog-title">{{ $p->title }}</h6>

                      @if($p->excerpt)
                        <p class="mb-0 blog-excerpt text-secondary">{{ $p->excerpt }}</p>
                      @endif
                    </div>
                  </div>
                </a>
              </div>
            @endforeach
          </div>

          {{-- Pagination --}}
          <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
            {{ $posts->withQueryString()->links() }}
          </div>
        @else
          <div class="card border-0 shadow-sm rounded-4 text-center py-5" data-aos="fade-up">
            <div class="card-body">
              <i class="bi bi-journal-x" style="font-size: 3rem; color: #ced4da;"></i>
              <h5 class="mt-3 mb-1">Chưa có bài viết phù hợp</h5>
              <p class="text-secondary mb-3">Hãy thử từ khóa khác, hoặc xóa bộ lọc hiện tại.</p>
              <a href="{{ route('blog.index') }}" class="btn btn-primary rounded-pill px-3">Xóa bộ lọc</a>
            </div>
          </div>
        @endif
      </section>
    </div>
  </div>
@endsection

@push('styles')
<style>
/* ====== HERO ====== */
.blog-hero{
  position: relative;
  min-height: 300px;
  background-image:
    linear-gradient(to bottom, rgba(0,0,0,.35), rgba(0,0,0,.35)),
    url('https://images.unsplash.com/photo-1493809842364-78817add7ffb?q=80&w=1600&auto=format&fit=crop');
  background-size: cover;
  background-position: center;
  border-radius: 0 0 32px 32px;
  overflow: hidden;
}
.blog-hero .hero-overlay{
  position:absolute; inset:0;
  background: radial-gradient(60% 60% at 50% 0, rgba(162,14,56,.25) 0%, rgba(162,14,56,0) 60%);
  pointer-events:none;
}
.blog-breadcrumb{ --bs-breadcrumb-divider: '›'; }
.blog-breadcrumb .breadcrumb-item a{ color:#f8f9fa; text-decoration:none; }
.blog-breadcrumb .breadcrumb-item a:hover{ text-decoration:underline; }
.blog-breadcrumb .breadcrumb-item.active{ color:#e9ecef; }

/* ====== LAYOUT ====== */
.top-sticky{ top: var(--sticky-offset, 96px); }
.card.rounded-4{ border-radius: 1rem; }

/* ====== SEARCH in toolbar ====== */
.blog-index .input-group .form-control{ border-radius: .8rem 0 0 .8rem; }
.blog-index .input-group .btn{
  border-radius: 0 .8rem .8rem 0;
  background-color:#A20E38; border-color:#A20E38;
}
.blog-index .input-group .btn:hover{ background-color:#8b0c30; border-color:#8b0c30; }

/* ====== GRID + CARD ====== */
.blog-grid .card{
  border: 0;
  border-radius: 1rem;
  overflow: hidden;
  transition: transform .18s ease, box-shadow .18s ease;
  background:#fff;
}
.blog-grid .card:hover{
  transform: translateY(-2px);
  box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.06);
}

/* Ảnh: VỪA KHUNG, KHÔNG BỊ CẮT */
.blog-grid .card-img-top.blog-thumb{
  width: 100%;
  height: var(--blog-thumb-h, 220px); /* đồng bộ chiều cao lưới */
  object-fit: contain;                /* hiển thị đủ ảnh, không crop */
  background:#f8f9fa;                 /* nền xám nhạt dạng “letterbox” */
  padding:.5rem;
  border-top-left-radius: 1rem;
  border-top-right-radius: 1rem;
}
@media (max-width: 575.98px){
  .blog-grid .card-img-top.blog-thumb{ --blog-thumb-h: 180px; }
}
@media (min-width: 1200px){
  .blog-grid .card-img-top.blog-thumb{ --blog-thumb-h: 240px; }
}

/* Tiêu đề & trích đoạn */
.blog-title{
  font-weight: 800;
  line-height: 1.35;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
  overflow: hidden;
}
.blog-excerpt{
  font-size: .9375rem;
  display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;
  overflow: hidden;
  color:#6c757d;
}

/* Utilities */
.text-truncate-1{
  display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow:hidden;
}
.xsmall{ font-size:.8125rem; }
.btn-primary{ background-color:#A20E38; border-color:#A20E38; }
.btn-primary:hover{ background-color:#8b0c30; border-color:#8b0c30; }
</style>
@endpush

@push('scripts-page')
<script>
/* Tính khoảng cách sticky theo chiều cao header (đồng bộ với các trang khác) */
(function(){
  function updateStickyOffset(){
    const header = document.querySelector('header.sticky-top, header.navbar-transparent, header');
    const headerHeight = header ? header.offsetHeight : 72;
    const gap = 16; // space thoáng
    document.documentElement.style.setProperty('--sticky-offset', (headerHeight + gap) + 'px');
  }
  window.addEventListener('load', updateStickyOffset);
  window.addEventListener('resize', updateStickyOffset);
  setTimeout(updateStickyOffset, 300);
})();
</script>
@endpush
