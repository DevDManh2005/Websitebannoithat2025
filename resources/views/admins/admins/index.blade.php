{{-- resources/views/admins/admins/index.blade.php --}}
@extends('admins.layouts.app')

@section('title','Quản lý Admin')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $admins */
    $total = method_exists($admins,'total') ? $admins->total() : $admins->count();
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
    .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a }
    .badge.bg-secondary-soft{ background:#ececec; color:#545b62 }
</style>

<div class="container-fluid">
    {{-- Header + actions + filter --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Quản lý Admin</h1>
                <span class="text-muted small">({{ number_format($total) }} tài khoản)</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Thêm Admin
                </a>
            </div>
        </div>

        <form class="row g-2 mt-2" method="get" action="{{ route('admin.admins.index') }}">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Tìm theo tên hoặc email…">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>Khóa</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i> Lọc</button>
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
                            <th style="width:80px">#</th>
                            <th>Tài khoản</th>
                            <th style="width:160px">Trạng thái</th>
                            <th style="width:160px">Ngày tạo</th>
                            <th class="text-end" style="width:180px">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $a)
                            <tr>
                                <td class="text-muted">#{{ $a->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $a->name }}</div>
                                    <div class="small text-muted">{{ $a->email }}</div>
                                </td>
                                <td>
                                    @if($a->is_active ?? true)
                                        <span class="badge bg-success-soft">Hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Khóa</span>
                                    @endif
                                </td>
                                <td>{{ optional($a->created_at)->format('d/m/Y') ?: '—' }}</td>
                                <td class="text-end">
                                    {{-- Desktop --}}
                                    <div class="d-none d-md-inline-flex gap-1">
                                        <a href="{{ route('admin.admins.edit',$a->id) }}" class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.admins.destroy',$a->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Xóa Admin &quot;{{ $a->name }}&quot;?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                    {{-- Mobile --}}
                                    <div class="dropdown d-inline d-md-none">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.admins.edit',$a->id) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.admins.destroy',$a->id) }}" method="POST"
                                                      onsubmit="return confirm('Xóa Admin &quot;{{ $a->name }}&quot;?');">
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
                                <td colspan="5" class="text-center text-muted py-4">Chưa có tài khoản admin nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ method_exists($admins,'appends') ? $admins->appends(request()->query())->links() : $admins->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
