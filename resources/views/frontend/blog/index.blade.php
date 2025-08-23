@extends('layouts.app')

@section('title', 'Blog - ' . ($settings['site_name'] ?? config('app.name')))

@section('content')
<section class="blog-page pb-5">
  <div class="container">
    <div class="row">
      {{-- Sidebar --}}
      <div class="col-lg-3">
        @include('frontend.components.blog-categories-sidebar', [
          'categories' => $cats,
          'activeSlug' => $categorySlug
        ])
        
        {{-- Popular Posts Sidebar --}}
        @php
          $popularPosts = App\Models\Blog::published()
            ->orderBy('view_count', 'desc')
            ->take(3)
            ->get();
        @endphp
        
        @if($popularPosts->count() > 0)
          <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-header bg-transparent py-3">
              <h6 class="mb-0 fw-bold">
                <i class="bi bi-fire text-danger me-2"></i>
                Bài viết nổi bật
              </h6>
            </div>
            <div class="card-body">
              @foreach($popularPosts as $popularPost)
                <a href="{{ route('blog.show', $popularPost->slug) }}" class="d-block text-decoration-none mb-3">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <img src="{{ $popularPost->thumbnail ? Storage::url($popularPost->thumbnail) : 'https://placehold.co/60x60?text=Thumb' }}" 
                           class="rounded" alt="{{ $popularPost->title }}" width="60" height="60" style="object-fit: cover;">
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0 text-dark fw-medium">{{ Str::limit($popularPost->title, 40) }}</h6>
                      <small class="text-muted">{{ ($popularPost->published_at ?? $popularPost->created_at)->format('d/m/Y') }}</small>
                    </div>
                  </div>
                </a>
                @if(!$loop->last)
                  <hr class="my-3">
                @endif
              @endforeach
            </div>
          </div>
        @endif
      </div>

      {{-- Main Content --}}
      <div class="col-lg-9">
        {{-- Page Header --}}
        <div class="page-header mb-5">
          <div class="row align-items-center">
            <div class="col-md-6">
              @php
                $currentCategory = null;
                if ($categorySlug) {
                  $currentCategory = $cats->firstWhere('slug', $categorySlug);
                }
              @endphp
              
              <h1 class="display-5 fw-bold text-dark mb-3">
                @if($currentCategory)
                  {{ $currentCategory->name }}
                @else
                  Bài Viết Mới Nhất
                @endif
              </h1>
              <p class="text-muted">Khám phá những bài viết mới nhất và thú vị nhất từ chúng tôi</p>
            </div>
            <div class="col-md-6">
              <form action="{{ route('blog.index') }}" method="GET" class="blog-search">
                <div class="input-group shadow-sm rounded-pill">
                  <input type="text" 
                         name="q" 
                         class="form-control border-0 rounded-pill" 
                         placeholder="Tìm kiếm bài viết..."
                         value="{{ request('q') }}"
                         aria-label="Tìm kiếm bài viết">
                  <button class="btn btn-brand rounded-pill px-4" type="submit">
                    <i class="bi bi-search me-1"></i> Tìm kiếm
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        {{-- Posts Grid --}}
        @if($posts->count())
          <div class="row g-4">
            @foreach($posts as $post)
              <div class="col-md-6 col-lg-4">
                <div class="card post-card border-0 shadow-sm h-100">
                  <div class="post-image">
                    <a href="{{ route('blog.show', $post->slug) }}">
                      <img src="{{ $post->thumbnail ? Storage::url($post->thumbnail) : 'https://placehold.co/600x400?text=Blog' }}" 
                           class="card-img-top" alt="{{ $post->title }}">
                    </a>
                    @if($post->category)
                      <div class="post-category">
                        {{ $post->category->name }}
                      </div>
                    @endif
                  </div>
                  <div class="card-body">
                    <div class="post-meta d-flex align-items-center mb-2">
                      <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i>
                        {{ ($post->published_at ?? $post->created_at)->format('d/m/Y') }}
                      </small>
                      <span class="mx-2 text-muted">•</span>
                      <small class="text-muted">
                        <i class="bi bi-eye me-1"></i>
                        {{ number_format($post->view_count) }}
                      </small>
                    </div>
                    <h5 class="card-title">
                      <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">
                        {{ Str::limit($post->title, 60) }}
                      </a>
                    </h5>
                    <p class="card-text text-muted">
                      {{ Str::limit(strip_tags($post->excerpt), 100) }}
                    </p>
                  </div>
                  <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-sm btn-outline-brand rounded-pill">
                      Đọc thêm <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          {{-- Pagination --}}
          <div class="mt-5 d-flex justify-content-center">
            {{ $posts->links() }}
          </div>
        @else
          <div class="text-center py-5">
            <div class="empty-state">
              <i class="bi bi-search display-1 text-muted opacity-25"></i>
              <h4 class="mt-3 text-muted">Không tìm thấy bài viết nào</h4>
              <p class="text-muted">Hãy thử tìm kiếm với từ khóa khác hoặc duyệt danh mục khác</p>
              <a href="{{ route('blog.index') }}" class="btn btn-brand mt-3 rounded-pill">
                <i class="bi bi-arrow-left me-2"></i>Quay lại blog
              </a>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

<style>
.blog-page {
  background: linear-gradient(to bottom, #f8f9fa, #ffffff);
  padding-top: 2rem;
}

.page-header {
  padding: 2rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.post-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 12px;
  overflow: hidden;
}

.post-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.post-image {
  position: relative;
  overflow: hidden;
}

.post-image img {
  transition: transform 0.5s ease;
  height: 200px;
  object-fit: cover;
}

.post-card:hover .post-image img {
  transform: scale(1.05);
}

.post-category {
  position: absolute;
  top: 15px;
  left: 15px;
  background: var(--brand);
  color: white;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
}

.post-meta {
  font-size: 0.85rem;
}

.card-title a {
  transition: color 0.2s ease;
}

.card-title a:hover {
  color: var(--brand) !important;
}

.empty-state {
  padding: 4rem 2rem;
}

/* Pagination styling */
.pagination {
  margin-bottom: 0;
}

.page-link {
  border-radius: 8px !important;
  margin: 0 3px;
  border: 1px solid #dee2e6;
  color: var(--brand);
}

.page-item.active .page-link {
  background-color: var(--brand);
  border-color: var(--brand);
}

.page-link:hover {
  color: var(--brand);
  background-color: #f8f9fa;
  border-color: #dee2e6;
}

/* Search form styling */
.blog-search .input-group {
  background: white;
  border-radius: 50px;
  padding: 5px;
}

.blog-search .form-control {
  border: none;
  background: transparent;
  box-shadow: none !important;
}

.blog-search .form-control:focus {
  box-shadow: none !important;
}

.blog-search .btn {
  padding: 0.5rem 1.2rem;
}

@media (max-width: 992px) {
  .page-header {
    text-align: center;
    padding: 1.5rem;
  }
  
  .blog-search {
    margin-top: 1.5rem;
  }
}

@media (max-width: 768px) {
  .post-card {
    margin-bottom: 1.5rem;
  }
  
  .page-header h1 {
    font-size: 2rem;
  }
}
</style>

@endsection