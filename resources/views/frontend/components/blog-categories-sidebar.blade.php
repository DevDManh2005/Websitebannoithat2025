@php
  use App\Models\BlogCategory;

  $categories = $categories
      ?? BlogCategory::where('is_active', 1)->orderBy('sort_order')->orderBy('name')->get();

  $activeSlug = $activeSlug ?? request('danh-muc');
@endphp

<div class="card border-0 shadow-sm rounded-4 blog-side" data-aos="fade-right">
  <div class="card-body p-3 p-lg-4">
    <h5 class="fw-bold mb-3">Danh mục</h5>

    <div class="list-group list-group-flush blog-side__list">
      <a href="{{ route('blog.index') }}"
         class="list-group-item list-group-item-action {{ empty($activeSlug) ? 'active' : '' }}">
        Tất cả
      </a>
      @foreach($categories as $c)
        @php
          $count = $c->published_blogs_count ?? $c->blogs_count ?? null; // hiển thị nếu controller có withCount
        @endphp
        <a href="{{ route('blog.index', ['danh-muc' => $c->slug]) }}"
           class="list-group-item list-group-item-action {{ $activeSlug === $c->slug ? 'active' : '' }}">
          <span>{{ $c->name }}</span>
          @if(!is_null($count))
            <span class="badge bg-light text-secondary rounded-pill ms-auto">{{ $count }}</span>
          @endif
        </a>
      @endforeach
    </div>
  </div>
</div>
