@php
  /** @var \App\Models\Blog $post */
  $post   = $post ?? null;
  $img    = $post?->thumbnail ? Storage::url($post->thumbnail) : 'https://placehold.co/600x400?text=Blog';
  $href   = route('blog.show', $post->slug);
  $cat    = $post?->category?->name;
  $date   = optional($post->published_at ?? $post->created_at)->format('d/m/Y');
@endphp

<article class="blog-card card border-0 shadow-sm rounded-4 h-100" data-aos="fade-up">
  <a href="{{ $href }}" class="position-relative d-block overflow-hidden rounded-top-4">
    <img src="{{ $img }}" alt="{{ $post->title }}"
         class="w-100" style="aspect-ratio: 16/10; object-fit: cover; transition: transform .5s ease;">
    <span class="blog-card__overlay"></span>
    @if($cat)
      <span class="badge bg-primary-subtle text-primary-emphasis blog-card__chip">
        {{ $cat }}
      </span>
    @endif
  </a>

  <div class="card-body p-3 p-lg-4">
    <h3 class="h6 fw-bold mb-2">
      <a class="stretched-link text-decoration-none text-dark blog-card__title"
         href="{{ $href }}">{{ Str::limit($post->title, 90) }}</a>
    </h3>
    @if($post->excerpt)
      <p class="text-secondary small mb-3 text-truncate-2">{{ Str::limit(strip_tags($post->excerpt), 140) }}</p>
    @endif

    <div class="d-flex align-items-center text-muted xsmall">
      <i class="bi bi-calendar-event me-1"></i>{{ $date }}
      <span class="mx-2">•</span>
      <i class="bi bi-eye me-1"></i>{{ number_format($post->view_count) }}
    </div>
  </div>
</article>
