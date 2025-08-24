@extends('admins.layouts.app')

@section('title', 'Quản lý Kho hàng')

@section('content')
@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $products */
    $total = $products->total();
@endphp

{{-- Your CSS can remain exactly the same --}}
<style>
    .filter-bar { border-radius: 16px; padding: 12px; background: linear-gradient(140deg, rgba(196, 111, 59, .08), rgba(78, 107, 82, .06) 70%), var(--card); border: 1px solid rgba(32, 25, 21, .08); box-shadow: var(--shadow); }
    .card-soft { border-radius: 16px; border: 1px solid rgba(32, 25, 21, .08) }
    .card-soft .card-header { background: transparent; border-bottom: 1px dashed rgba(32, 25, 21, .12) }
    .prod-thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 10px; background: #f7f2eb; border: 1px solid rgba(32, 25, 21, .06); }
    .table td, .table th { vertical-align: middle; }
    .table .text-truncate { max-width: 320px; }
    @media (max-width: 575.98px) { .table .text-truncate { max-width: 200px; } }
    .badge.bg-success-soft { background: #e5f7ed; color: #1e6b3a }
    .badge.bg-danger-soft { background: #fde7e7; color: #992f2f }
    .badge.bg-info-soft { background: #e6f1ff; color: #0b4a8b }
    .toggle-row { cursor: pointer; }
    .chev { transition: transform .18s ease; }
    .chev.rot { transform: rotate(180deg); }
    .subtable thead th { background: #faf6f0; }
</style>

<div class="container-fluid">
    {{-- Filter Bar --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Kho hàng</h1>
                <span class="text-muted small">({{ number_format($total) }} sản phẩm)</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.inventories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Thêm mới</a>
            </div>
        </div>
        <form class="row g-2 mt-2" method="get" action="{{ route('admin.inventories.index') }}">
            <div class="col-12 col-lg-6">
                <div class="input-group"><span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span><input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm theo tên sản phẩm / SKU…"></div>
            </div>
            <div class="col-6 col-lg-3">
                <select name="low" class="form-select">
                    <option value="">-- Lọc tồn --</option>
                    <option value="zero" {{ request('low') === 'zero' ? 'selected' : '' }}>Hết hàng (0)</option>
                    <option value="low5" {{ request('low') === 'low5' ? 'selected' : '' }}>≤ 5</option>
                </select>
            </div>
            <div class="col-6 col-lg-3 d-grid"><button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i>Lọc</button></div>
        </form>
    </div>

    @if (session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
    @if (session('error'))<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>@endif

    <div class="card card-soft">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Danh sách theo sản phẩm</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px">SP #</th>
                            <th style="width:70px">Ảnh</th>
                            <th>Sản phẩm</th>
                            <th class="text-center" style="width:130px">Biến thể</th>
                            <th class="text-end" style="width:120px">Tổng tồn</th>
                            <th class="text-end" style="width:60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $inventories = $product->inventories;
                                $thumb = $product->thumbnail_url;
                                $variantCount = $inventories->count();
                                $totalQty = (int) $inventories->sum('quantity');
                                $collapseId = 'pv-' . $product->id;
                            @endphp
                            {{-- Product Summary Row --}}
                            <tr class="toggle-row" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                                <td class="text-muted">#{{ $product->id }}</td>
                                <td>
                                    <img src="{{ $thumb ?? 'https://via.placeholder.com/48x48?text=SP' }}" alt="{{ $product->name }}" class="prod-thumb" loading="lazy" onerror="this.onerror=null;this.src='https://via.placeholder.com/48x48?text=SP';">
                                </td>
                                <td class="text-truncate">
                                    <a class="fw-semibold text-decoration-none" href="{{ route('admin.products.edit', $product->id) }}" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </a>
                                    <div class="small text-muted">SKU: {{ $product->sku ?? '—' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-soft">{{ $variantCount }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="badge {{ $totalQty > 0 ? 'bg-success-soft' : 'bg-danger-soft' }}">{{ number_format($totalQty) }}</span>
                                </td>
                                <td class="text-end">
                                    <i class="bi bi-chevron-down chev"></i>
                                </td>
                            </tr>
                            {{-- Collapsible Row with Variants --}}
                            <tr class="collapse inv-collapse" id="{{ $collapseId }}">
                                <td colspan="6" class="p-0" style="background-color: #f8f9fa;">
                                    <div class="table-responsive">
                                        <table class="table table-sm subtable align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width:70px">Inv #</th>
                                                    <th style="width:170px">SKU</th>
                                                    <th>Thuộc tính</th>
                                                    <th class="text-end" style="width:120px">Số lượng</th>
                                                    <th>Vị trí</th>
                                                    <th class="text-end" style="width:220px">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($inventories as $inventory)
                                                    @php
                                                        $attrsText = $inventory->variant?->attributes_text ?? '—';
                                                        $qty = (int) ($inventory->quantity ?? 0);
                                                        $loc = $inventory->location;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-muted">#{{ $inventory->id }}</td>
                                                        <td><strong>{{ $inventory->variant?->sku ?? '—' }}</strong></td>
                                                        <td class="small text-truncate">{{ $attrsText }}</td>
                                                        <td class="text-end">
                                                            <span class="badge {{ $qty > 0 ? 'bg-success-soft' : 'bg-danger-soft' }}">{{ $qty }}</span>
                                                        </td>
                                                        <td class="small">
                                                            @if($loc)
                                                                <div class="fw-semibold">{{ $loc->name }}</div>
                                                                @if($loc->address)<div class="text-muted">{{ $loc->address }}</div>@endif
                                                            @else
                                                                <span class="text-muted">N/A</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            {{-- Action Buttons --}}
                                                            <div class="d-none d-md-inline-flex gap-1">
                                                                <a href="{{ route('admin.inventories.show', $inventory->id) }}" class="btn btn-sm btn-outline-secondary" title="Xem"><i class="bi bi-eye"></i></a>
                                                                <a href="{{ route('admin.inventories.edit', $inventory->id) }}" class="btn btn-sm btn-warning" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                                                <form action="{{ route('admin.inventories.destroy', $inventory->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa bản ghi kho #{{ $inventory->id }}?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                                                </form>
                                                            </div>
                                                            <div class="dropdown d-inline d-md-none">
                                                                {{-- Mobile Actions Dropdown --}}
                                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="{{ route('admin.inventories.show', $inventory->id) }}">Xem</a></li>
                                                                    <li><a class="dropdown-item" href="{{ route('admin.inventories.edit', $inventory->id) }}">Sửa</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form action="{{ route('admin.inventories.destroy', $inventory->id) }}" method="POST" onsubmit="return confirm('Xóa bản ghi kho #{{ $inventory->id }}?');">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="dropdown-item text-danger">Xóa</button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted p-4">Không tìm thấy sản phẩm nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $products->appends(request()->query())->links() }}</div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.inv-collapse').forEach(function (el) {
                const row = el.previousElementSibling;
                const icon = row.querySelector('.chev');
                if (icon) {
                    el.addEventListener('show.bs.collapse', () => icon.classList.add('rot'));
                    el.addEventListener('hide.bs.collapse', () => icon.classList.remove('rot'));
                }
            });
        });
    </script>
@endpush
@endsection