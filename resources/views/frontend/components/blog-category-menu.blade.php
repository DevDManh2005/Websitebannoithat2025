@php
    use App\Models\BlogCategory;

    // KHÔNG dùng $categories để tránh đụng với categories của sản phẩm ở ngoài
    // Nếu file được include có truyền sẵn $blogCategories thì dùng; nếu không thì tự load.
    $blogCategories = isset($blogCategories)
        ? collect($blogCategories)
        : BlogCategory::query()
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
@endphp

<ul class="dropdown-menu p-2 blog-menu" aria-labelledby="blogDropdown" role="menu">
  <li>
    <a class="dropdown-item rounded-3 {{ request()->routeIs('blog.index') && !request('danh-muc') ? 'active' : '' }}"
       href="{{ route('blog.index') }}">
      Tất cả bài viết
    </a>
  </li>

  <li><hr class="dropdown-divider"></li>

  {{-- Lưới 2 cột, không dùng $categories để tránh xung đột --}}
  <li class="px-1 py-1">
    <div class="row g-1" style="min-width:280px; max-width:520px;">
      @forelse($blogCategories as $c)
        <div class="col-12 col-sm-6">
          <a class="dropdown-item rounded-3 {{ request('danh-muc') === $c->slug ? 'active' : '' }}"
             href="{{ route('blog.index', ['danh-muc' => $c->slug]) }}">
            {{ $c->name }}
          </a>
        </div>
      @empty
        <div class="col-12">
          <span class="dropdown-item text-muted">Chưa có danh mục.</span>
        </div>
      @endforelse
    </div>
  </li>
</ul>

@once
@push('styles')
<style>
  .blog-menu { padding: .5rem !important; }
  .blog-menu .dropdown-item { white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
  .blog-menu .dropdown-item.active{
    background: rgba(162,14,56,.12);
    color: #A20E38;
    font-weight: 600;
  }
</style>
@endpush
@endonce
