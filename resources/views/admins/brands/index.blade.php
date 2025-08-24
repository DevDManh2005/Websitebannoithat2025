{{-- resources/views/admins/brands/index.blade.php --}}
@extends('admins::layouts.app')


@section('title', 'Quản lý Thương hiệu')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $brands */
    $total = method_exists($brands,'total') ? $brands->total() : $brands->count();

    // helper chuyển path -> url đầy đủ (hỗ trợ http/https hoặc storage path)
    $toUrl = function($p){
        if(!$p) return null;
        return \Illuminate\Support\Str::startsWith($p, ['http://','https://','//'])
            ? $p
            : asset('storage/'.$p);
    };
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

    .brand-logo{
        width:72px; height:48px; object-fit:contain; border-radius:10px;
        background:#f7f2eb; border:1px solid rgba(32,25,21,.06);
    }
    .table td,.table th{ vertical-align: middle; }
    .table .text-truncate{ max-width: 320px; }
    @media (max-width: 575.98px){
        .table .text-truncate{ max-width: 200px; }
    }

    /* soft badges */
    .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
    .badge.bg-secondary-soft{ background:#ececec; color:#545b62 }
</style>

<div class="container-fluid">
    {{-- Header + actions + filter --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Thương hiệu</h1>
                <span class="text-muted small">({{ number_format($total) }} mục)</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Thêm thương hiệu
                </a>
            </div>
        </div>

        {{-- Bộ lọc: từ khóa + trạng thái --}}
        <form class="row g-2 mt-2" method="get" action="{{ route('admin.brands.index') }}">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm theo tên…">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Không hoạt động</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-grid">
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
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px">ID</th>
                            <th style="width:100px">Logo</th>
                            <th>Tên</th>
                            <th style="width:150px">Trạng thái</th>
                            <th class="text-end" style="width:200px">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($brands as $brand)
                        @php
                            $logo = $toUrl($brand->logo_url ?? $brand->logo ?? null);
                        @endphp
                        <tr>
                            <td class="text-muted">#{{ $brand->id }}</td>
                            <td>
                                <img
                                    src="{{ $logo ?: 'https://via.placeholder.com/72x48?text=Logo' }}"
                                    class="brand-logo"
                                    alt="{{ $brand->name }}"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='https://via.placeholder.com/72x48?text=Logo';"
                                >
                            </td>
                            <td class="text-truncate">
                                <a class="fw-semibold text-decoration-none" href="{{ route('admin.brands.edit', $brand) }}" title="{{ $brand->name }}">
                                    {{ $brand->name }}
                                </a>
                            </td>
                            <td>
                                @if($brand->is_active)
                                    <span class="badge bg-success-soft">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Không hoạt động</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{-- Desktop --}}
                                <div class="d-none d-md-inline-flex gap-1">
                                    <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-sm btn-outline-secondary" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa thương hiệu &quot;{{ $brand->name }}&quot;?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                {{-- Mobile --}}
                                <div class="dropdown d-inline d-md-none">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.brands.show', $brand) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.brands.edit', $brand) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thương hiệu &quot;{{ $brand->name }}&quot;?');">
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
                            <td colspan="5" class="text-center text-muted p-4">Chưa có thương hiệu nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang giữ bộ lọc --}}
            <div class="mt-3">
                {{ $brands->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
