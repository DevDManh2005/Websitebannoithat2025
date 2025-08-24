
@php
  /** @var \App\Models\Blog $post */
  $post   = $post ?? null;
  $img    = $post?->thumbnail ? Storage::url($post->thumbnail) : 'https://placehold.co/600x400?text=Blog';
  $href   = route('blog.show', $post->slug);
  $cat    = $post?->category?->name;
  $date   = optional($post->published_at ?? $post->created_at)->format('d/m/Y');
@endphp

<article class="blog-card card-glass card border-0 shadow-elevated rounded-4 h-100" data-aos="fade-up" role="article" aria-labelledby="post-{{ $post->id }}-title">
  <a href="{{ $href }}" class="position-relative d-block overflow-hidden rounded-top-4 blog-card__media" aria-label="{{ $post->title }}">
    <img src="{{ $img }}"
         alt="{{ $post->title }}"
         loading="lazy"
         decoding="async"
         role="img"
         class="w-100 blog-card__img"
         style="aspect-ratio: 16/10; object-fit: cover;">
    <span class="blog-card__overlay position-absolute inset-0"></span>

    @if($cat)
      <span class="badge-soft-brand blog-card__chip position-absolute top-3 start-3">
        {{ $cat }}
      </span>
    @endif
  </a>

  <div class="card-body p-3 p-lg-4 d-flex flex-column">
    <h3 class="h6 fw-bold mb-2" id="post-{{ $post->id }}-title">
      <a class="stretched-link text-decoration-none blog-card__title"
         href="{{ $href }}">
        {{ Str::limit($post->title, 90) }}
      </a>
    </h3>

    @if($post->excerpt)
      <p class="text-muted small mb-3 text-truncate-2">
        {{ Str::limit(strip_tags($post->excerpt), 140) }}
      </p>
    @endif

    <div class="mt-auto d-flex align-items-center text-muted small gap-2">
      <span class="d-flex align-items-center gap-1">
        <i class="bi bi-calendar-event icon-hover" aria-hidden="true"></i>
        <span>{{ $date }}</span>
      </span>

      <span class="mx-1">•</span>

      <span class="d-flex align-items-center gap-1">
        <i class="bi bi-eye icon-hover" aria-hidden="true"></i>
        <span>{{ number_format($post->view_count) }}</span>
      </span>
    </div>
  </div>

  <style>
    /* Media container and image */
    .blog-card__media { position: relative; display: block; overflow: hidden; }
    .blog-card__img { transition: transform .6s cubic-bezier(.2,.9,.3,1); will-change: transform; display: block; }
    .blog-card__media:hover .blog-card__img,
    .blog-card__media:focus .blog-card__img { transform: scale(1.06) translateZ(0); }

    /* Gradient overlay for better text contrast */
    .blog-card__overlay {
      inset: 0; position: absolute;
      background: linear-gradient(180deg, rgba(0,0,0,0.00) 10%, rgba(0,0,0,0.12) 60%, rgba(0,0,0,0.18) 100%);
      pointer-events: none;
    }

    /* Category chip */
    .blog-card__chip {
      z-index: 6;
      padding: .28rem .6rem;
      border-radius: 999px;
      font-size: .8rem;
      box-shadow: 0 6px 18px rgba(0,0,0,.06);
    }

    /* Title and link */
    .blog-card__title { color: var(--text); transition: color .12s ease, transform .12s ease; }
    .blog-card__title:hover,
    .blog-card__title:focus { color: var(--brand); text-decoration: none; transform: translateY(-1px); }

    /* Truncate excerpt to two lines */
    .text-truncate-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    /* Responsive tweaks for each breakpoint */
    @media (max-width: 991px) {
      /* Tablet (768-991px) */
      .blog-card__chip {
        padding: .25rem .55rem;
        font-size: .78rem;
      }
      .card-body {
        padding: 1rem !important;
      }
      .blog-card__title {
        font-size: 1rem;
      }
      .text-muted.small {
        font-size: .85rem;
      }
      .blog-card__img {
        aspect-ratio: 4/3;
      }
    }

    @media (max-width: 767px) {
      /* Mobile (576-767px) */
      .blog-card__chip {
        padding: .22rem .5rem;
        font-size: .75rem;
      }
      .card-body {
        padding: .9rem !important;
      }
      .blog-card__title {
        font-size: .95rem;
      }
      .text-muted.small {
        font-size: .8rem;
        -webkit-line-clamp: 1; /* Limit excerpt to one line */
      }
      .blog-card__img {
        aspect-ratio: 3/2;
      }
      .blog-card__img {
        transition: transform .8s cubic-bezier(.2,.9,.3,1); /* Slower transition for mobile */
      }
    }

    @media (max-width: 575px) {
      /* Small Mobile (<576px) */
      .blog-card__chip {
        padding: .2rem .45rem;
        font-size: .7rem;
      }
      .card-body {
        padding: .75rem !important;
      }
      .blog-card__title {
        font-size: .9rem;
      }
      .text-muted.small {
        font-size: .75rem;
      }
      .blog-card__img {
        aspect-ratio: 1/1; /* Square image for small screens */
      }
    }
  </style>
</article>
