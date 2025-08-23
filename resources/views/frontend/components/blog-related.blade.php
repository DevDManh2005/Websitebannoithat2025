@php
  use App\Models\Blog;

  /** @var \App\Models\Blog $post */
  $limit = $limit ?? 4;

  $related = Blog::with('category')
      ->published()
      ->where('id', '!=', $post->id)
      ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
      ->latest()
      ->take($limit)
      ->get();
@endphp

@if($related->count())
  <div class="component-card related-posts-card mt-4" data-aos="fade-up" data-aos-delay="100">
    <div class="card-body">
      <h5 class="card-header-title">Bài viết liên quan</h5>

      <div class="list-wrapper">
        @foreach($related as $r)
          <a href="{{ route('blog.show',$r->slug) }}" class="related-post-link" title="{{ $r->title }}">
            <img src="{{ $r->thumbnail ? Storage::url($r->thumbnail) : 'https://placehold.co/160x100?text=No+Image' }}"
                 class="related-post-thumbnail"
                 alt="{{ $r->title }}">

            <div class="post-content">
              @if($r->category)
                <span class="badge-soft-brand mb-1">{{ $r->category->name }}</span>
              @endif
              <p class="post-title mb-0">{{ Str::limit($r->title, 80) }}</p>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </div>
@endif

{{-- ===== STYLES ===== --}}
<style>
  .related-posts-card {
    background: var(--card, #fff);
    border: 1px solid rgba(0, 0, 0, .05);
    border-radius: var(--radius, 12px);
    box-shadow: var(--shadow, 0 10px 30px rgba(32, 25, 21, .08));
  }
  .related-posts-card .card-body {
    padding: 1rem 1.25rem;
  }

  .related-posts-card .card-header-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text, #2B2623);
    margin-bottom: 0.75rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(0, 0, 0, .06);
  }

  .related-posts-card .list-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .related-posts-card .related-post-link {
    display: flex;
    gap: 1rem;
    align-items: center;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 10px;
    transition: background .15s ease;
  }

  .related-posts-card .related-post-link:hover {
    background: rgba(var(--brand-rgb, 162, 14, 56), .05);
  }

  .related-posts-card .related-post-thumbnail {
    flex-shrink: 0;
    width: 120px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    transition: transform .2s cubic-bezier(.2, .9, .3, 1);
  }
  .related-posts-card .related-post-link:hover .related-post-thumbnail {
    transform: scale(1.05);
  }
  @media(max-width: 575.98px) {
    .related-posts-card .related-post-thumbnail {
      width: 100px;
      height: 70px;
    }
  }


  .related-posts-card .post-title {
    font-weight: 600;
    color: var(--text, #2B2623);
    line-height: 1.4;
    transition: color .15s ease;
  }
  .related-posts-card .related-post-link:hover .post-title {
    color: var(--brand, #A20E38);
  }
</style>