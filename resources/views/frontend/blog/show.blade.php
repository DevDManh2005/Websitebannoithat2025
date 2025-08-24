@extends('layouts.app')

@section('title', $post->title . ' - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<section class="blog-detail-section py-5">
  <div class="container">
    <div class="row justify-content-center">
      {{-- Main Content --}}
      <div class="col-lg-8">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4" data-aos="fade-down">
          <ol class="breadcrumb blog-breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-brand">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('blog.index') }}" class="text-decoration-none text-brand">Blog</a></li>
            @if($post->category)
              <li class="breadcrumb-item">
                <a href="{{ route('blog.index', ['danh-muc' => $post->category->slug]) }}" class="text-decoration-none text-brand">
                  {{ $post->category->name }}
                </a>
              </li>
            @endif
            <li class="breadcrumb-item active text-muted">{{ Str::limit($post->title, 30) }}</li>
          </ol>
        </nav>

        <article class="blog-article card card-glass border-0 rounded-4 overflow-hidden" data-aos="fade-up">
          {{-- Header --}}
          <header class="blog-header px-4 pt-4">
            <h1 class="display-5 fw-bold text-dark mb-3">{{ $post->title }}</h1>

            <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-3">
              <span class="d-flex align-items-center gap-1">
                <i class="bi bi-person-circle text-brand"></i>
                {{ $post->author->name }}
              </span>
              <span class="d-flex align-items-center gap-1">
                <i class="bi bi-calendar-event text-brand"></i>
                {{ ($post->published_at ?? $post->created_at)->format('d/m/Y') }}
              </span>
              <span class="d-flex align-items-center gap-1">
                <i class="bi bi-eye text-brand"></i>
                {{ number_format($post->view_count) }} lượt xem
              </span>
            </div>

            @if($post->category)
              <span class="badge badge-soft-brand rounded-pill px-3 py-2 mb-3">
                <i class="bi bi-tag me-1"></i>{{ $post->category->name }}
              </span>
            @endif
          </header>

          {{-- Featured Image --}}
          @if($post->thumbnail)
            <div class="blog-featured-image mb-4">
              <img src="{{ Storage::url($post->thumbnail) }}" 
                   alt="{{ $post->title }}" 
                   class="img-fluid w-100">
              <div class="image-overlay"></div>
            </div>
          @endif

          {{-- Content --}}
          <div class="blog-content px-4 pb-4">
            <div class="content-wrapper">
              {!! $post->content !!}
            </div>
          </div>

          {{-- Like Button & Social Share --}}
          <div class="blog-actions px-4 pb-4 border-top pt-4 d-flex flex-wrap justify-content-between align-items-center">
            <form action="{{ route('blog.like.toggle', $post) }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-like" 
                      data-liked="{{ auth()->check() && $post->likes->contains('user_id', auth()->id()) ? 'true' : 'false' }}">
                <span class="like-icon">
                  <i class="bi bi-heart{{ auth()->check() && $post->likes->contains('user_id', auth()->id()) ? '-fill' : '' }}"></i>
                </span>
                <span class="like-count">{{ $post->likes->count() }}</span>
                <span class="like-text">Thích bài viết</span>
              </button>
            </form>
            
            <div class="share-buttons">
              <span class="me-2 text-muted">Chia sẻ:</span>
              <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                 target="_blank" class="btn btn-sm btn-share fb">
                <i class="bi bi-facebook"></i>
              </a>
              <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" 
                 target="_blank" class="btn btn-sm btn-share tw">
                <i class="bi bi-twitter"></i>
              </a>
              <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&media={{ urlencode(Storage::url($post->thumbnail)) }}&description={{ urlencode($post->title) }}" 
                 target="_blank" class="btn btn-sm btn-share pin">
                <i class="bi bi-pinterest"></i>
              </a>
            </div>
          </div>
        </article>

        {{-- Related Posts --}}
        @include('frontend.components.blog-related', ['post' => $post])

        {{-- Comments Section --}}
        <div class="blog-comments mt-5">
          <div class="card card-glass border-0 rounded-4 overflow-hidden" data-aos="fade-up">
            <div class="card-header bg-transparent py-3">
              <h4 class="mb-0 d-flex align-items-center">
                <i class="bi bi-chat-text-fill me-2 text-brand"></i>
                Bình luận ({{ $post->comments->count() }})
              </h4>
            </div>
            
            <div class="card-body">
              {{-- Comment Form --}}
              @auth
                <form action="{{ route('blog.comments.store', $post) }}" method="POST" class="comment-form mb-5">
                  @csrf
                  <div class="mb-3">
                    <label for="comment" class="form-label fw-semibold">Thêm bình luận</label>
                    <textarea name="comment" id="comment" rows="4" class="form-control form-control-modern shadow-sm"
                              placeholder="Viết bình luận của bạn..." required></textarea>
                  </div>
                  <button type="submit" class="btn btn-brand px-4">
                    <i class="bi bi-send me-2"></i>Gửi bình luận
                  </button>
                </form>
              @else
                <div class="alert alert-light border card-glass fade-in">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-brand me-2 fs-5"></i>
                    <div>
                      <a href="{{ route('login.form') }}" class="text-brand fw-semibold">Đăng nhập</a> 
                      để bình luận bài viết
                    </div>
                  </div>
                </div>
              @endauth

              {{-- Comments List --}}
              <div class="comments-list">
                @forelse($post->comments as $comment)
                  @include('frontend.blog.partials.comment', ['comment' => $comment])
                @empty
                  <div class="text-center py-4 text-muted" data-aos="zoom-in">
                    <i class="bi bi-chat-dots display-4 opacity-25"></i>
                    <p class="mt-3">Chưa có bình luận nào</p>
                  </div>
                @endforelse
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Sidebar --}}
      <div class="col-lg-4">
        <div class="sticky-sidebar">
          @include('frontend.components.blog-categories-sidebar')
          
          {{-- Popular Posts --}}
          @php
            $popularPosts = App\Models\Blog::published()
              ->orderBy('view_count', 'desc')
              ->take(4)
              ->get();
          @endphp
          
          @if($popularPosts->count() > 0)
            <div class="card card-glass border-0 rounded-4 overflow-hidden mt-4" data-aos="fade-left">
              <div class="card-header bg-transparent py-3">
                <h5 class="mb-0 d-flex align-items-center">
                  <i class="bi bi-fire text-brand me-2"></i>
                  Bài viết nổi bật
                </h5>
              </div>
              <div class="card-body">
                <div class="list-group list-group-flush">
                  @foreach($popularPosts as $popularPost)
                    <a href="{{ route('blog.show', $popularPost->slug) }}" class="list-group-item list-group-item-action border-0 px-0 py-3 hover-lift">
                      <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                          <img src="{{ $popularPost->thumbnail ? Storage::url($popularPost->thumbnail) : 'https://placehold.co/60x40?text=Thumb' }}" 
                               class="rounded me-3" alt="{{ $popularPost->title }}" width="60" height="40" style="object-fit: cover;">
                        </div>
                        <div class="flex-grow-1">
                          <h6 class="mb-1">{{ Str::limit($popularPost->title, 40) }}</h6>
                          <small class="text-muted">{{ ($popularPost->published_at ?? $popularPost->created_at)->format('d/m/Y') }}</small>
                          <div class="d-flex align-items-center mt-1">
                            <i class="bi bi-eye me-1 text-muted small"></i>
                            <small class="text-muted">{{ number_format($popularPost->view_count) }} lượt xem</small>
                          </div>
                        </div>
                      </div>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

@push('styles')
<style>
/* =================== Blog Detail Section =================== */
.blog-detail-section {
  background: var(--bg);
  padding-top: 3rem;
  padding-bottom: 3rem;
}

/* =================== Breadcrumb =================== */
.blog-breadcrumb {
  background: rgba(var(--brand-rgb), 0.08);
  border-radius: var(--radius);
  padding: 0.75rem 1.25rem;
  box-shadow: var(--shadow);
  font-size: 0.95rem;
}
.blog-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
  color: var(--brand);
  font-weight: bold;
}
.blog-breadcrumb a {
  color: var(--brand);
  transition: color 0.2s ease;
}
.blog-breadcrumb a:hover {
  color: var(--brand-600);
}
.breadcrumb-item.active {
  color: var(--muted);
}

/* =================== Article Card =================== */
.blog-article-card {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
  box-shadow: var(--shadow);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.blog-article-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}
.blog-header h1 {
  font-size: 2.2rem;
  line-height: 1.4;
  color: var(--text);
}
.blog-header .badge-soft-brand {
  font-size: 0.85rem;
  background: rgba(var(--brand-rgb), 0.1);
  color: var(--brand);
}

/* =================== Featured Image =================== */
.blog-featured-image {
  border-radius: var(--radius);
  overflow: hidden;
  margin: 0 -1rem 2rem;
  box-shadow: var(--shadow);
}
.blog-featured-image img {
  transition: transform 0.6s ease;
}
.blog-featured-image:hover img {
  transform: scale(1.05);
}
.image-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.1), transparent);
}

/* =================== Content =================== */
.blog-content .content-wrapper {
  font-size: 1.08rem;
  line-height: 1.85;
  color: var(--muted);
}
.blog-content .content-wrapper h2,
.blog-content .content-wrapper h3 {
  margin-top: 2rem;
  font-weight: 600;
  color: var(--text);
}
.blog-content blockquote {
  border-left: 4px solid var(--brand);
  padding: 1rem 1.5rem;
  background: rgba(var(--brand-rgb), 0.05);
  border-radius: var(--radius);
  font-style: italic;
  color: var(--muted);
  margin: 1.5rem 0;
}

/* =================== Like Button =================== */
.btn-like {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.6rem 1.4rem;
  border-radius: 999px;
  background: rgba(var(--brand-rgb), 0.1);
  border: none;
  color: var(--brand);
  font-weight: 500;
  transition: all 0.25s ease;
}
.btn-like:hover {
  background: rgba(var(--brand-rgb), 0.2);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
.btn-like[data-liked="true"] {
  background: var(--brand);
  color: white;
}

/* =================== Share Buttons =================== */
.btn-share {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-left: 0.4rem;
  border: 1px solid var(--light-border);
  color: var(--brand);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.btn-share:hover {
  transform: translateY(-2px) scale(1.08);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
}
.btn-share.fb { background: #4267B2; color: white; }
.btn-share.tw { background: #1DA1F2; color: white; }
.btn-share.pin { background: #E60023; color: white; }

/* =================== Comments =================== */
.blog-comments .card-glass {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
  box-shadow: var(--shadow);
}
.comment-form textarea {
  border-radius: var(--radius);
  border: 1px solid var(--light-border);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.comment-form textarea:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 0.2rem var(--ring);
}
.comments-list .comment-card {
  border-radius: var(--radius);
}

/* =================== Sidebar =================== */
.sticky-sidebar {
  position: sticky;
  top: var(--sticky-offset, 88px);
}
.sticky-sidebar .card-glass {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
  box-shadow: var(--shadow);
}
.sticky-sidebar .card-header {
  background: rgba(var(--brand-rgb), 0.05);
  font-weight: 600;
}
.list-group-item {
  transition: background 0.2s ease, transform 0.2s ease;
}
.list-group-item:hover {
  background: rgba(var(--brand-rgb), 0.05);
  transform: translateX(3px);
}

/* =================== Buttons =================== */
.btn-brand {
  background-color: var(--brand);
  border-color: var(--brand);
  color: #fff;
  transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
}
.btn-brand:hover {
  background-color: var(--brand-600);
  border-color: var(--brand-600);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}
.btn-outline-secondary {
  transition: all 0.2s ease;
}
.btn-outline-secondary:hover {
  background-color: var(--brand);
  color: #fff;
  transform: translateY(-1px);
}

/* =================== Responsive Design =================== */
@media (max-width: 991px) {
  .blog-detail-section {
    padding-top: 2rem;
    padding-bottom: 2rem;
  }
  .col-lg-8, .col-lg-4 {
    flex: 0 0 100%;
    max-width: 100%;
  }
  .sticky-sidebar {
    position: static;
  }
  .blog-header h1 {
    font-size: 2rem;
  }
  .blog-featured-image {
    margin: 0;
    border-radius: var(--radius);
  }
}

@media (max-width: 767px) {
  .blog-header h1 {
    font-size: 1.8rem;
  }
  .blog-content .content-wrapper {
    font-size: 1rem;
  }
  .blog-featured-image {
    margin: 0;
  }
  .blog-actions {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  .share-buttons {
    width: 100%;
  }
  .blog-breadcrumb {
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
  }
  .comment-form textarea {
    font-size: 0.9rem;
  }
  .btn-brand, .btn-outline-secondary {
    font-size: 0.85rem;
    padding: 0.4rem 1rem;
  }
}

@media (max-width: 575px) {
  .blog-header h1 {
    font-size: 1.6rem;
  }
  .blog-content .content-wrapper {
    font-size: 0.95rem;
  }
  .blog-breadcrumb {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
  }
  .blog-header .badge-soft-brand {
    font-size: 0.75rem;
  }
  .btn-like {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
  }
  .btn-share {
    width: 32px;
    height: 32px;
    font-size: 0.9rem;
  }
  .comment-form textarea {
    font-size: 0.85rem;
  }
  .btn-brand, .btn-outline-secondary {
    font-size: 0.8rem;
    padding: 0.3rem 0.8rem;
  }
}
</style>
@endpush

@push('scripts-page')
<script>
// ===== Tính khoảng cách sticky theo chiều cao header =====
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

  // AOS init
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 600,
      once: true,
      offset: 80
    });
  }
})();

// Like button animation
document.querySelector('.btn-like')?.addEventListener('click', function() {
  const liked = this.dataset.liked === 'true';
  this.dataset.liked = !liked;
  const icon = this.querySelector('.like-icon i');
  icon.classList.toggle('bi-heart', liked);
  icon.classList.toggle('bi-heart-fill', !liked);
});

// Reply functionality
document.querySelectorAll('.reply-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    const commentId = this.dataset.commentId;
    const username = this.dataset.username;

    // Remove any existing reply forms
    document.querySelectorAll('.reply-form').forEach(form => form.remove());

    // Create reply form
    const replyForm = `
      <form action="{{ route('blog.comments.store', $post) }}" method="POST" class="reply-form mt-3 p-3 card-glass rounded-3" data-aos="fade-up">
        @csrf
        <input type="hidden" name="parent_id" value="${commentId}">
        <div class="mb-2">
          <textarea name="comment" class="form-control form-control-modern" rows="2" 
                    placeholder="Trả lời ${username}..." required></textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-sm btn-brand">Gửi</button>
          <button type="button" class="btn btn-sm btn-outline-secondary cancel-reply">Hủy</button>
        </div>
      </form>
    `;

    // Insert form
    this.closest('.comment-card').insertAdjacentHTML('beforeend', replyForm);

    // Focus on textarea
    const textarea = document.querySelector('.reply-form textarea');
    if (textarea) {
      textarea.focus();
    }

    // Cancel button handler
    document.querySelector('.cancel-reply').addEventListener('click', function () {
      this.closest('.reply-form').remove();
    });
  });
});
</script>
@endpush
@endsection