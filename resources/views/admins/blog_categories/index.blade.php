{{-- resources/views/admins/blog_categories/index.blade.php --}}
@extends(auth()->user()->role->name === 'staff' ? 'staff.layouts.app' : 'admins.layouts.app')


@section('title','Danh mục bài viết')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $categories */
    $total = method_exists($categories,'total') ? $categories->total() : $categories->count();
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

    .table td,.table th{ vertical-align: middle; }
    .table .text-truncate{ max-width: 420px; }
    @media (max-width: 575.98px){
        .table .text-truncate{ max-width: 220px; }
    }
    .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
    .badge.bg-secondary-soft{ background:#ececec; color:#545b62 }

    .caret-toggle .bi{ transition: transform .15s ease; }
    .caret-toggle[aria-expanded="true"] .bi{ transform: rotate(90deg); }
</style>

<div class="container-fluid">
    {{-- Header + actions + filter --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Danh mục bài viết</h1>
                <span class="text-muted small">({{ number_format($total) }} mục)</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Thêm danh mục
                </a>
            </div>
        </div>

        <form class="row g-2 mt-2" method="get" action="{{ route('admin.blog-categories.index') }}">
            <div class="col-12 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm theo tên / slug…">
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>ON</option>
                    <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>OFF</option>
                </select>
            </div>
            <div class="col-6 col-lg-3 d-grid">
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
                            <th style="width:35%">Tên</th>
                            <th style="width:20%">Slug</th>
                            <th style="width:25%">Danh mục cha</th>
                            <th style="width:10%">Trạng thái</th>
                            <th class="text-end" style="width:10%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            {{-- Chỉ render danh mục GỐC; con sẽ nằm trong collapse --}}
                            @if(is_null($cat->parent_id))

                                @php
                                    $hasChildren = $cat->relationLoaded('children')
                                        ? $cat->children->isNotEmpty()
                                        : (optional($cat->children)->count() > 0);
                                    $collapseId = 'child-cats-'.$cat->id;
                                @endphp

                                {{-- Hàng CHA --}}
                                <tr>
                                    <td class="fw-medium text-truncate">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($hasChildren)
                                                <button
                                                    class="btn btn-sm btn-light border-0 px-1 caret-toggle"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#{{ $collapseId }}"
                                                    aria-expanded="false"
                                                    aria-controls="{{ $collapseId }}"
                                                    title="Xem danh mục con">
                                                    <i class="bi bi-caret-right-fill"></i>
                                                </button>
                                            @else
                                                <span class="text-muted"><i class="bi bi-dot"></i></span>
                                            @endif
                                            <span class="text-truncate" title="{{ $cat->name }}">{{ $cat->name }}</span>
                                        </div>
                                    </td>
                                    <td><code>{{ $cat->slug }}</code></td>
                                    <td class="text-truncate">{{ $cat->parent?->name ?? '—' }}</td>
                                    <td>
                                        @if($cat->is_active)
                                            <span class="badge bg-success-soft">ON</span>
                                        @else
                                            <span class="badge bg-secondary">OFF</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.blog-categories.edit', $cat) }}" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.blog-categories.destroy', $cat) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Xóa danh mục &quot;{{ $cat->name }}&quot;?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Hàng CON (ẩn/hiện khi bấm CHA) --}}
                                @if($hasChildren)
                                    <tr>
                                        <td colspan="5" class="p-0">
                                            <div class="collapse" id="{{ $collapseId }}">
                                                <div class="p-2 bg-light border-top">
                                                    <table class="table table-sm align-middle mb-0">
                                                        <thead>
                                                            <tr class="small text-muted">
                                                                <th style="width:35%">Tên</th>
                                                                <th style="width:20%">Slug</th>
                                                                <th style="width:25%">Danh mục cha</th>
                                                                <th style="width:10%">Trạng thái</th>
                                                                <th class="text-end" style="width:10%">Hành động</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($cat->children as $child)
                                                                <tr>
                                                                    <td class="text-truncate" title="{{ $child->name }}">
                                                                        └─ {{ $child->name }}
                                                                    </td>
                                                                    <td><code>{{ $child->slug }}</code></td>
                                                                    <td class="text-truncate">{{ $cat->name }}</td>
                                                                    <td>
                                                                        @if($child->is_active)
                                                                            <span class="badge bg-success-soft">ON</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">OFF</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <a href="{{ route('admin.blog-categories.edit', $child) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                                                                            <i class="bi bi-pencil-square"></i>
                                                                        </a>
                                                                        <form action="{{ route('admin.blog-categories.destroy', $child) }}"
                                                                              method="POST" class="d-inline"
                                                                              onsubmit="return confirm('Xóa danh mục &quot;{{ $child->name }}&quot;?');">
                                                                            @csrf @method('DELETE')
                                                                            <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Chưa có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($categories,'links'))
                <div class="mt-3">
                    {{ $categories->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
