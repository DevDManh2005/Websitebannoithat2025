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
  <div class="card border-0 shadow-sm rounded-4 mt-4" data-aos="fade-right" data-aos-delay="100">
    <div class="card-body p-3 p-lg-4">
      <h5 class="fw-bold mb-3">Bài viết khác</h5>

      <div class="row g-3">
        @foreach($related as $r)
          <div class="col-12">
            <a href="{{ route('blog.show',$r->slug) }}" class="d-flex gap-3 text-decoration-none blog-related__item">
              <img src="{{ $r->thumbnail ? Storage::url($r->thumbnail) : 'https://placehold.co/160x100?text=No+Image' }}"
                   class="rounded-3 flex-shrink-0"
                   style="width:140px;height:90px;object-fit:cover" alt="{{ $r->title }}">
              <div class="flex-grow-1">
                <div class="xsmall text-muted mb-1">{{ $r->category?->name }}</div>
                <div class="fw-semibold text-dark">{{ Str::limit($r->title, 80) }}</div>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>
@endif