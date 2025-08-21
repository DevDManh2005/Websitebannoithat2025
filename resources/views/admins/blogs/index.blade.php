{{-- resources/views/admins/blogs/index.blade.php --}}
@extends('admins.layouts.app')

@section('title','Bài viết')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $blogs */
    $total   = method_exists($blogs,'total') ? $blogs->total() : $blogs->count();
    // danh mục cho filter nếu controller có truyền (tùy chọn)
    $catList = $categories ?? $allCategories ?? collect();
@endphp

<style>
    .filter-bar{
        border-radius:16px; padding:12px;
        background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
        border:1px solid rgba(32,25,21,.08);
        box-shadow: var(--shadow);
    }
    .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
    .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }

    .post-thumb{
        width:72px; height:48px; object-fit:cover; border-radius:10px;
        background:#f7f2eb; border:1px solid rgba(32,25,21,.06);
    }
    .table td,.table th{ vertical-align: middle; }
    .table .text-truncate{ max-width: 420px; }
    @media (max-width: 575.98px){
        .table .text-truncate{ max-width: 220px; }
    }

    /* Soft badges */
    .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
    .badge.bg-secondary-soft{ background:#ececec; color:#545b62 }
</style>

<div class="container-fluid">
    {{-- Header + actions + filter --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Bài viết</h1>
                <span class="text-muted small">({{ number_format($total) }} mục)</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Thêm bài viết
                </a>
            </div>
        </div>

        {{-- Bộ lọc: từ khóa + trạng thái + danh mục (tùy chọn) --}}
        <form class="row g-2 mt-2" method="get" action="{{ route('admin.blogs.index') }}">
            <div class="col-12 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm theo tiêu đề / nội dung…">
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="published" {{ request('status')==='published' ? 'selected' : '' }}>Đã xuất bản</option>
                    <option value="draft"     {{ request('status')==='draft' ? 'selected' : '' }}>Bản nháp</option>
                </select>
            </div>
            @if($catList && count($catList))
            <div class="col-6 col-lg-2">
                <select name="category_id" class="form-select">
                    <option value="">-- Danh mục --</option>
                    @foreach($catList as $c)
                        <option value="{{ $c->id }}" {{ (string)request('category_id')===(string)$c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-12 col-lg-1 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i>Lọc</button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="card card-soft">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Danh sách</strong>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width:100px">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th style="width:220px">Danh mục</th>
                            <th style="width:150px">Trạng thái</th>
                            <th style="width:160px">Xuất bản</th>
                            <th class="text-end" style="width:170px">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($blogs as $p)
                        @php
                            // chọn ảnh: thumbnail -> image -> cover/cover_image -> image_url
                            $img = $p->thumbnail ?? $p->image ?? $p->cover ?? $p->cover_image ?? $p->image_url ?? null;
                            $imgUrl = $img
                                ? (\Illuminate\Support\Str::startsWith($img, ['http://','https://','//']) ? $img : asset('storage/'.$img))
                                : null;

                            // Fallback nút "Xem": ưu tiên admin.blogs.show; nếu không có thì dùng blog.show (public)
                            $hasAdminShow = \Illuminate\Support\Facades\Route::has('admin.blogs.show');
                            $showUrl = $hasAdminShow
                                ? route('admin.blogs.show', $p)
                                : ($p->slug ? route('blog.show', $p->slug) : null);
                        @endphp
                        <tr>
                            <td>
                                <img
                                  src="{{ $imgUrl ?: 'https://via.placeholder.com/96x64?text=IMG' }}"
                                  class="post-thumb"
                                  alt="{{ $p->title }}"
                                  loading="lazy"
                                  onerror="this.onerror=null;this.src='https://via.placeholder.com/96x64?text=IMG';">
                            </td>
                            <td class="fw-semibold text-truncate">
                                <a class="text-decoration-none" href="{{ route('admin.blogs.edit',$p) }}" title="{{ $p->title }}">
                                    {{ $p->title }}
                                </a>
                                @if(!empty($p->slug))
                                    <div class="small text-muted text-truncate">/{{ $p->slug }}</div>
                                @endif
                            </td>
                            <td class="text-truncate">
                                {{ $p->category?->name ?? '—' }}
                            </td>
                            <td>
                                @if($p->is_published)
                                    <span class="badge bg-success-soft">Published</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>{{ $p->published_at ? $p->published_at->format('d/m/Y H:i') : '—' }}</td>
                            <td class="text-end">
                                {{-- Desktop --}}
                                <div class="d-none d-md-inline-flex gap-1">
                                    @if($showUrl)
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="{{ $showUrl }}"
                                           title="Xem"
                                           @unless($hasAdminShow) target="_blank" rel="noopener" @endunless>
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.blogs.edit',$p) }}" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form class="d-inline" action="{{ route('admin.blogs.destroy',$p) }}" method="POST"
                                          onsubmit="return confirm('Xóa bài viết &quot;{{ $p->title }}&quot;?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                                {{-- Mobile --}}
                                <div class="dropdown d-inline d-md-none">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if($showUrl)
                                            <li>
                                                <a class="dropdown-item"
                                                   href="{{ $showUrl }}"
                                                   @unless($hasAdminShow) target="_blank" rel="noopener" @endunless>
                                                    <i class="bi bi-eye me-2"></i>Xem
                                                </a>
                                            </li>
                                        @endif
                                        <li><a class="dropdown-item" href="{{ route('admin.blogs.edit',$p) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.blogs.destroy',$p) }}" method="POST"
                                                  onsubmit="return confirm('Xóa bài viết &quot;{{ $p->title }}&quot;?');">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Xóa</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có bài viết nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($blogs,'links'))
                <div class="mt-3">
                    {{ $blogs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
