  @extends('layouts.app')

  @section('title', $post->title . ' - ' . ($settings['site_name'] ?? config('app.name')))

  @section('content')
  <section class="blog-detail-section py-5">
    <div class="container">
      <div class="row justify-content-center">
        {{-- Main Content --}}
        <div class="col-lg-8">
          {{-- Breadcrumb với hiệu ứng --}}
          <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb blog-breadcrumb animate__animated animate__fadeInDown">
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

          <article class="blog-article card blog-article-card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp">
            {{-- Header --}}
            <header class="blog-header px-4 pt-4">
              <h1 class="display-5 fw-bold text-dark mb-3 slide-in-left">{{ $post->title }}</h1>

              <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-3 fade-in">
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
                <span class="badge bg-brand text-white px-3 py-2 rounded-pill mb-3 pop-in">
                  <i class="bi bi-tag me-1"></i>{{ $post->category->name }}
                </span>
              @endif
            </header>

            {{-- Featured Image với hiệu ứng --}}
            @if($post->thumbnail)
              <div class="blog-featured-image mb-4 zoom-in">
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
                <button type="submit" class="btn btn-like heartbeat" 
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
                  target="_blank" class="btn btn-sm btn-share fb bounce-in">
                  <i class="bi bi-facebook"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" 
                  target="_blank" class="btn btn-sm btn-share tw bounce-in" style="animation-delay: 0.1s;">
                  <i class="bi bi-twitter"></i>
                </a>
                <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&media={{ urlencode(Storage::url($post->thumbnail)) }}&description={{ urlencode($post->title) }}" 
                  target="_blank" class="btn btn-sm btn-share pin bounce-in" style="animation-delay: 0.2s;">
                  <i class="bi bi-pinterest"></i>
                </a>
              </div>
            </div>
          </article>

          {{-- Related Posts --}}
          @include('frontend.components.blog-related', ['post' => $post])

          {{-- Comments Section --}}
          <div class="blog-comments mt-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden fade-in-up">
              <div class="card-header bg-light py-3">
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
                      <textarea name="comment" id="comment" rows="4" class="form-control shadow-sm"
                                placeholder="Viết bình luận của bạn..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-brand px-4 pulse">
                      <i class="bi bi-send me-2"></i>Gửi bình luận
                    </button>
                  </form>
                @else
                  <div class="alert alert-light border fade-in">
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
                    <div class="text-center py-4 text-muted fade-in">
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
              <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4 slide-in-right">
                <div class="card-header bg-light py-3">
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

  <style>
 /* ====== Layout chung ====== */
.blog-detail-section {
  background: linear-gradient(180deg, #fafafa 0%, #fff 100%);
  padding-top: 4rem;
  padding-bottom: 4rem;
}

/* ====== Breadcrumb ====== */
.blog-breadcrumb {
  background: rgba(var(--brand-rgb), 0.08);
  border-radius: 12px;
  padding: 0.75rem 1.25rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  font-size: 0.95rem;
}
.blog-breadcrumb .breadcrumb-item + .breadcrumb-item::before {
  color: var(--brand);
  font-weight: bold;
}
.blog-breadcrumb a {
  transition: color 0.2s;
}
.blog-breadcrumb a:hover {
  color: var(--brand-600);
}

/* ====== Article Card ====== */
.blog-article-card {
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(8px);
  border-radius: 20px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  transition: transform 0.25s, box-shadow 0.25s;
}
.blog-article-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 28px rgba(0,0,0,0.1);
}

/* ====== Header ====== */
.blog-header h1 {
  font-size: 2.2rem;
  line-height: 1.4;
  color: var(--text);
}
.blog-header .badge {
  font-size: 0.85rem;
  letter-spacing: 0.3px;
}

/* ====== Featured Image ====== */
.blog-featured-image {
  border-radius: 16px;
  overflow: hidden;
  margin: 0 -1rem 2rem;
  box-shadow: 0 10px 24px rgba(0,0,0,0.08);
}
.blog-featured-image img {
  transition: transform 0.6s ease;
}
.blog-featured-image:hover img {
  transform: scale(1.05);
}

/* ====== Content ====== */
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
  background: rgba(var(--brand-rgb),0.05);
  border-radius: 12px;
  font-style: italic;
  color: var(--muted);
  margin: 1.5rem 0;
}

/* ====== Like button ====== */
.btn-like {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0.6rem 1.4rem;
  border-radius: 999px;
  background: rgba(var(--brand-rgb),0.1);
  border: none;
  color: var(--brand);
  font-weight: 500;
  transition: all 0.25s ease;
}
.btn-like:hover {
  background: rgba(var(--brand-rgb),0.2);
  transform: translateY(-2px);
}
.btn-like[data-liked="true"] {
  background: var(--brand);
  color: white;
}

/* ====== Share buttons ====== */
.btn-share {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-left: 0.4rem;
  transition: transform 0.25s, box-shadow 0.25s;
}
.btn-share:hover {
  transform: translateY(-2px) scale(1.08);
  box-shadow: 0 6px 18px rgba(0,0,0,0.12);
}

/* ====== Sidebar ====== */
.sticky-sidebar .card {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  background: #fff;
}
.sticky-sidebar .card-header {
  background: rgba(var(--brand-rgb),0.05);
  font-weight: 600;
}

/* ====== Popular Posts ====== */
.list-group-item {
  border: none;
  transition: background 0.2s, transform 0.2s;
}
.list-group-item:hover {
  background: rgba(var(--brand-rgb),0.05);
  transform: translateX(3px);
}

/* ====== Comments ====== */
.blog-comments .card {
  border-radius: 16px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}
.comment-form textarea {
  border-radius: 12px;
  border: 1px solid var(--light-border);
}
.comments-list .comment-card {
  border-radius: 12px;
  background: rgba(0,0,0,0.02);
  padding: 1rem;
  margin-bottom: 1rem;
}
.comments-list .comment-card:hover {
  background: rgba(0,0,0,0.04);
}

  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translate3d(0, -20px, 0);
    }
    to {
      opacity: 1;
      transform: translate3d(0, 0, 0);
    }
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translate3d(0, 20px, 0);
    }
    to {
      opacity: 1;
      transform: translate3d(0, 0, 0);
    }
  }

  .slide-in-left {
    animation: slideInLeft 0.8s ease;
  }

  @keyframes slideInLeft {
    from {
      opacity: 0;
      transform: translateX(-30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .fade-in {
    animation: fadeIn 1s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .pop-in {
    animation: popIn 0.5s ease;
  }

  @keyframes popIn {
    0% {
      opacity: 0;
      transform: scale(0.8);
    }
    50% {
      transform: scale(1.1);
    }
    100% {
      opacity: 1;
      transform: scale(1);
    }
  }

  .zoom-in {
    animation: zoomIn 0.8s ease;
  }

  @keyframes zoomIn {
    from {
      opacity: 0;
      transform: scale(0.95);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  .heartbeat {
    animation: heartbeat 1.5s ease-in-out infinite both;
  }

  @keyframes heartbeat {
    from {
      transform: scale(1);
      transform-origin: center center;
      animation-timing-function: ease-out;
    }
    10% {
      transform: scale(0.91);
      animation-timing-function: ease-in;
    }
    17% {
      transform: scale(0.98);
      animation-timing-function: ease-out;
    }
    33% {
      transform: scale(0.87);
      animation-timing-function: ease-in;
    }
    45% {
      transform: scale(1);
      animation-timing-function: ease-out;
    }
  }

  .bounce-in {
    animation: bounceIn 0.6s ease;
  }

  @keyframes bounceIn {
    0% {
      opacity: 0;
      transform: scale(0.3);
    }
    50% {
      opacity: 1;
      transform: scale(1.05);
    }
    70% {
      transform: scale(0.9);
    }
    100% {
      opacity: 1;
      transform: scale(1);
    }
  }

  .fade-in-up {
    animation: fadeInUp 0.8s ease;
  }

  .pulse {
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0% {
      transform: scale(1);
    }
    50% {
      transform: scale(1.05);
    }
    100% {
      transform: scale(1);
    }
  }

  .slide-in-right {
    animation: slideInRight 0.8s ease;
  }

  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(30px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @media (max-width: 768px) {
    .blog-header h1 {
      font-size: 1.8rem;
    }
    
    .blog-content .content-wrapper {
      font-size: 1rem;
    }
    
    .blog-featured-image {
      margin: 0;
      border-radius: 8px;
    }
    
    .blog-actions {
      flex-direction: column;
      gap: 1rem;
      align-items: flex-start;
    }
    
    .share-buttons {
      width: 100%;
    }
    
    .sticky-sidebar {
      position: static;
    }
  }
  </style>

  @endsection

  @push('scripts-page')
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    // Initialize animations on scroll
    const observerOptions = {
      root: null,
      rootMargin: '0px',
      threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.visibility = 'visible';
          if (entry.target.classList.contains('slide-in-left')) {
            entry.target.style.animation = 'slideInLeft 0.8s ease forwards';
          } else if (entry.target.classList.contains('fade-in')) {
            entry.target.style.animation = 'fadeIn 1s ease forwards';
          } else if (entry.target.classList.contains('slide-in-right')) {
            entry.target.style.animation = 'slideInRight 0.8s ease forwards';
          }
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.slide-in-left, .fade-in, .slide-in-right').forEach(el => {
      el.style.visibility = 'hidden';
      observer.observe(el);
    });

    // Like button animation
    const likeBtn = document.querySelector('.btn-like');
    if (likeBtn) {
      likeBtn.addEventListener('click', function() {
        this.classList.add('heartbeat');
        setTimeout(() => {
          this.classList.remove('heartbeat');
        }, 1500);
      });
    }

    // Reply functionality
    const replyButtons = document.querySelectorAll('.reply-btn');
    replyButtons.forEach(btn => {
      btn.addEventListener('click', function () {
        const commentId = this.dataset.commentId;
        const username = this.dataset.username;

        // Create reply form
        const replyForm = `
          <form action="{{ route('blog.comments.store', $post) }}" method="POST" class="reply-form mt-3 p-3 bg-light rounded-3">
            @csrf
            <input type="hidden" name="parent_id" value="${commentId}">
            <div class="mb-2">
              <textarea name="comment" 
                        class="form-control" 
                        rows="2" 
                        placeholder="Trả lời ${username}..."
                        required></textarea>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-sm btn-brand">Gửi</button>
              <button type="button" class="btn btn-sm btn-outline-secondary cancel-reply">Hủy</button>
            </div>
          </form>
        `;

        // Remove any existing reply forms
        document.querySelectorAll('.reply-form').forEach(form => form.remove());

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
  });
  </script>
  @endpush