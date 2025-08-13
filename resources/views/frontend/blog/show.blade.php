@extends('layouts.app')
@section('title', $post->seo_title ?: $post->title)

@section('content')
  {{-- ========= HERO ========= --}}
  <section class="post-hero d-flex align-items-center mb-5">
    <div class="container">
      <div class="row justify-content-center text-center">
        <div class="col-lg-10" data-aos="fade-up">
          <h1 class="fw-bold text-white mb-3 post-hero-title">{{ $post->title }}</h1>
          <div class="text-white-50 small mb-4">
            <span class="me-3">
              <i class="bi bi-calendar-event me-1"></i>
              {{ optional($post->published_at ?? $post->created_at)->format('d/m/Y') }}
            </span>
            <span class="me-3">
              <i class="bi bi-eye me-1"></i>{{ number_format($post->view_count) }} lượt xem
            </span>
            @if($post->category)
              <a class="badge rounded-pill text-bg-light text-decoration-none"
                 href="{{ route('blog.index', ['danh-muc'=>$post->category->slug]) }}">
                <i class="bi bi-folder2-open me-1"></i>{{ $post->category->name }}
              </a>
            @endif
          </div>

          <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center post-breadcrumb m-0">
              <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
              <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Bài viết</a></li>
              <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
    <span class="hero-overlay"></span>
  </section>

  <div class="container my-4 my-lg-5">
    <div class="row g-4 g-xl-5">
      {{-- ========= MAIN ========= --}}
      <div class="col-lg-8">
        <article class="card border-0 shadow-sm rounded-4" data-aos="fade-up">
          {{-- Ảnh đầu bài (trong khung, không crop) --}}
          @if($post->thumbnail)
            <div class="post-cover-wrap">
              <img src="{{ asset('storage/'.$post->thumbnail) }}"
                   alt="{{ $post->title }}"
                   class="post-cover-img">
            </div>
          @endif

          <div class="card-body p-3 p-md-4 p-lg-5">
            {{-- Meta nhỏ dưới ảnh --}}
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3 text-muted xsmall">
              @if($post->author?->name)
                <span><i class="bi bi-person-circle me-1"></i>{{ $post->author->name }}</span>
                <span class="vr mx-2"></span>
              @endif
              <span><i class="bi bi-clock-history me-1"></i>Cập nhật {{ optional($post->updated_at)->diffForHumans() }}</span>
            </div>

            {{-- Nút hành động --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
              @auth
                <form action="{{ route('blog.like.toggle',$post) }}" method="POST" id="likeForm">
                  @csrf
                  <button class="btn btn-outline-danger btn-sm rounded-pill" type="submit" id="likeBtn">
                    ❤️ <span id="likeCount">{{ $post->likes()->count() }}</span>
                  </button>
                </form>
              @else
                <a href="{{ route('login.form') }}" class="btn btn-outline-danger btn-sm rounded-pill">
                  ❤️ {{ $post->likes()->count() }}
                </a>
              @endauth

              <button class="btn btn-outline-secondary btn-sm rounded-pill" id="copyBtn">
                <i class="bi bi-link-45deg me-1"></i>Copy link
              </button>
            </div>

            {{-- Nội dung --}}
            <div class="post-content">
              {!! $post->content !!}
            </div>
          </div>
        </article>

        {{-- ========= BÌNH LUẬN ========= --}}
        <section class="mt-4 mt-lg-5" data-aos="fade-up">
          <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3 p-md-4 p-lg-5">
              <h5 class="fw-bold mb-3">Bình luận</h5>

              @auth
                <form action="{{ route('blog.comments.store', $post) }}" method="POST" class="mb-4" id="commentForm">
                  @csrf
                  <input type="hidden" name="parent_id" id="parent_id">
                  <textarea class="form-control form-control-modern mb-2" name="comment" rows="3" placeholder="Viết bình luận..." required></textarea>
                  <div class="d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-3">Gửi</button>
                    <button class="btn btn-link d-none" id="cancelReply" type="button">Hủy trả lời</button>
                  </div>
                </form>
              @else
                <a href="{{ route('login.form') }}" class="btn btn-outline-primary rounded-pill px-3">Đăng nhập để bình luận</a>
              @endauth

              <div class="post-comments">
                @forelse($post->comments as $c)
                  @include('frontend.blog.partials.comment', ['c'=>$c])
                @empty
                  <div class="text-muted">Chưa có bình luận.</div>
                @endforelse
              </div>
            </div>
          </div>
        </section>
      </div>

      {{-- ========= SIDEBAR ========= --}}
      <div class="col-lg-4">
        <div class="position-sticky" style="top: var(--sticky-offset, 96px)">
          @if($post->category)
            <div class="card border-0 shadow-sm rounded-4 mb-3">
              <div class="card-body">
                <div class="small text-muted mb-1">Danh mục</div>
                <a class="text-decoration-none fw-semibold"
                   href="{{ route('blog.index', ['danh-muc'=>$post->category->slug]) }}">
                  <i class="bi bi-folder2-open me-1"></i>{{ $post->category->name }}
                </a>
              </div>
            </div>
          @endif

          {{-- Danh mục & Bài viết liên quan (components riêng) --}}
          <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-3 p-lg-4">
              @include('frontend.components.blog-categories-sidebar')
            </div>
          </div>

          @include('frontend.components.blog-related', ['post' => $post, 'limit' => 6])
        </div>
      </div>
    </div>
  </div>
@endsection

@push('styles')
<style>
/* ===== HERO ===== */
.post-hero{
  position: relative;
  min-height: 260px;
  background-image:
    linear-gradient(to bottom, rgba(0,0,0,.35), rgba(0,0,0,.35)),
    url('https://images.unsplash.com/photo-1517586979033-0b6986e3186d?q=80&w=1600&auto=format&fit=crop');
  background-size: cover;
  background-position: center;
  border-radius: 0 0 32px 32px;
  overflow: hidden;
}
.post-hero .hero-overlay{
  position:absolute; inset:0;
  background: radial-gradient(60% 60% at 50% 0, rgba(162,14,56,.25) 0%, rgba(162,14,56,0) 60%);
  pointer-events:none;
}
.post-breadcrumb{ --bs-breadcrumb-divider: '›'; }
.post-breadcrumb .breadcrumb-item a{ color:#f8f9fa; text-decoration:none; }
.post-breadcrumb .breadcrumb-item a:hover{ text-decoration:underline; }
.post-breadcrumb .breadcrumb-item.active{ color:#e9ecef; }
.post-hero-title{ line-height:1.25; }

/* ===== COVER ===== */
.post-cover-wrap{
  width: 100%;
  height: clamp(220px, 35vw, 420px); /* tùy màn hình, giữ khung đồng đều */
  background:#f8f9fa;
  display:flex; align-items:center; justify-content:center;
  border-top-left-radius: 1rem; border-top-right-radius: 1rem;
  overflow:hidden;
}
.post-cover-img{
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;      /* ẢNH KHÔNG BỊ CẮT */
  display:block;
  padding:.5rem;
}

/* ===== CONTENT ===== */
.post-content{
  font-size: 1.05rem;
  line-height: 1.85;
  color:#1c1f23;
}
.post-content img{
  max-width:100% !important; height:auto !important;
  border-radius:.75rem; display:block; margin: .75rem auto;
  background:#f8f9fa; padding:.25rem;
}
.post-content h2, .post-content h3, .post-content h4{
  margin-top:1.25rem; margin-bottom:.75rem;
}
.post-content p{ margin-bottom:1rem; }
.post-content ul{ margin-bottom:1rem; padding-left:1.25rem; }

/* ===== COMMENTS ===== */
.form-control-modern{
  border-radius:.8rem; border:1px solid #e9ecef; background:#fff;
}
.form-control-modern:focus{
  border-color:#A20E38; box-shadow:0 0 0 .2rem rgba(162,14,56,.15);
}
.post-comments .comment{ border-radius:.8rem; }
.comment .comment-actions .btn-link{ text-decoration:none; }
.comment .children{ border-left:2px dashed #e9ecef; margin-left:.75rem; padding-left:.75rem; }

/* ===== Buttons (brand color) ===== */
.btn-primary{ background-color:#A20E38; border-color:#A20E38; }
.btn-primary:hover{ background-color:#8b0c30; border-color:#8b0c30; }
.btn-outline-danger:hover{ color:#fff; }

/* Sticky offset đồng bộ */
:root{ --sticky-offset: 96px; }
</style>
@endpush

@push('scripts-page')
<script>
/* ===== Sticky offset theo chiều cao header ===== */
(function(){
  function updateStickyOffset(){
    const header = document.querySelector('header.sticky-top, header.navbar-transparent, header');
    const headerHeight = header ? header.offsetHeight : 72;
    const gap = 16;
    document.documentElement.style.setProperty('--sticky-offset', (headerHeight + gap) + 'px');
  }
  window.addEventListener('load', updateStickyOffset);
  window.addEventListener('resize', updateStickyOffset);
  setTimeout(updateStickyOffset, 300);
})();

/* ===== Copy link ===== */
document.getElementById('copyBtn')?.addEventListener('click', async () => {
  try{
    await navigator.clipboard.writeText(window.location.href);
    alert('Đã copy link bài viết!');
  }catch{
    alert('Không copy được, hãy copy thủ công.');
  }
});

/* ===== Like AJAX (nếu đã đăng nhập) ===== */
const likeForm = document.getElementById('likeForm');
if (likeForm){
  likeForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const btn = document.getElementById('likeBtn');
    if (!btn) return;
    const prev = btn.innerHTML;
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    try{
      const res = await fetch(likeForm.action, {
        method: 'POST',
        headers:{
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: new FormData(likeForm)
      });
      const data = await res.json();
      if (data?.count !== undefined){
        document.getElementById('likeCount').textContent = data.count;
      }
      // nháy nhẹ báo thành công
      btn.classList.toggle('btn-danger', !!data?.liked);
      btn.classList.toggle('btn-outline-danger', !data?.liked);
      btn.blur();
    }catch(e){
      console.error(e);
      alert('Không thực hiện được. Vui lòng thử lại.');
    }finally{
      btn.disabled = false; btn.innerHTML = prev;
    }
  });
}

/* ===== Trả lời comment ===== */
document.querySelectorAll('[data-reply]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    document.getElementById('parent_id').value = btn.dataset.reply;
    document.getElementById('cancelReply').classList.remove('d-none');
    const target = document.getElementById('commentForm') || document.querySelector('form');
    target && window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
  });
});
document.getElementById('cancelReply')?.addEventListener('click', ()=>{
  document.getElementById('parent_id').value = '';
  document.getElementById('cancelReply').classList.add('d-none');
});
</script>
@endpush
