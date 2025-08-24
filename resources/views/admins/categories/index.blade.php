@extends('admins.layouts.app')

@section('title', 'Quản lý Danh mục')

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Pagination\AbstractPaginator;
    use Illuminate\Support\Collection;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $categories */
    $isPaginated = $categories instanceof AbstractPaginator;
    $total = method_exists($categories, 'total') ? $categories->total() : $categories->count();
    $items = $isPaginated ? collect($categories->items()) : collect($categories);

    // Xây dựng cây danh mục từ items trong trang hiện tại
    $tree = new Collection();
    foreach ($items as $item) {
        $item->children = new Collection();
        $tree[$item->id] = $item;
    }
    foreach ($items as $item) {
        if ($item->parent_id && $tree->has($item->parent_id)) {
            $tree[$item->parent_id]->children->push($item);
        }
    }
    $roots = $tree->filter(function ($item) use ($tree) {
        return is_null($item->parent_id) || !$tree->has($item->parent_id);
    });
@endphp

@push('styles')
<style>
    :root {
        --card: #fff;
        --brand: #C46F3B;
        --shadow: 0 10px 30px rgba(32,25,21,.12);
    }

    .filter-bar {
        border-radius: 16px;
        padding: 12px;
        background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
        border: 1px solid rgba(32,25,21,.08);
        box-shadow: var(--shadow);
    }

    .card-soft {
        border-radius: 16px;
        border: 1px solid rgba(32,25,21,.08);
        box-shadow: var(--shadow);
        background: var(--card);
    }

    .card-soft .card-header {
        background: transparent;
        border-bottom: 1px dashed rgba(32,25,21,.12);
    }

    .cat-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 12px;
        background: #f7f2eb;
        border: 1px solid rgba(32,25,21,.06);
    }

    .table-wrap {
        position: relative;
    }

    .table-sticky thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: var(--card);
        border-bottom-color: rgba(32,25,21,.12) !important;
    }

    .table td, .table th {
        vertical-align: middle;
    }

    .table .text-truncate {
        max-width: 360px;
    }

    @media (max-width: 575.98px) {
        .table .text-truncate {
            max-width: 200px;
        }
    }

    .badge-soft-success {
        background: #e5f7ed;
        color: #1e6b3a;
        border: 1px solid rgba(24,121,78,.15);
    }

    .badge-soft-secondary {
        background: #f2f2f2;
        color: #545b62;
        border: 1px solid rgba(0,0,0,.08);
    }

    .expander {
        --sz: 28px;
        width: var(--sz);
        height: var(--sz);
        border-radius: 8px;
        display: inline-grid;
        place-items: center;
        border: 1px solid rgba(32,25,21,.12);
        background: #fff;
        transition: background 0.2s ease;
    }

    .expander:hover {
        background: #faf6f0;
    }

    .chev {
        transition: transform 0.2s ease;
    }

    .chev.rot {
        transform: rotate(180deg);
    }

    .btn-icon {
        padding: 0.3rem 0.5rem;
    }

    .mono {
        font: 500 0.9rem ui-monospace, Menlo, Consolas, "Courier New", monospace;
    }

    .table-hover>tbody>tr:hover>* {
        background: rgba(196,111,59,.05);
        transition: background 0.2s ease;
    }

    .tree-row {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .tree-row.hidden {
        opacity: 0;
        transform: translateY(-10px);
        display: none;
    }

    @media (prefers-reduced-motion: reduce) {
        .chev, .tree-row {
            transition: none !important;
        }
    }
</style>
@endpush

<div class="container-fluid">
    {{-- Thanh bộ lọc --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Danh mục sản phẩm</h1>
                <span class="text-muted small">({{ number_format($total) }} mục)</span>
                @if($isPaginated)
                    <span class="text-muted small">• Trang {{ $categories->currentPage() }}/{{ $categories->lastPage() }}</span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tạo mới
                </a>
            </div>
        </div>

        <form class="row g-2 mt-2" method="get" action="{{ route('admin.categories.index') }}">
            <div class="col-12 col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Tìm theo tên…">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Trạng thái --</option>
                    <option value="1" @selected(request('status')==='1')>Hiện</option>
                    <option value="0" @selected(request('status')==='0')>Ẩn</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button class="btn btn-outline-secondary flex-fill">
                    <i class="bi bi-funnel me-1"></i>Lọc
                </button>
                @if(request()->hasAny(['q','status']) && (request('q')!==null || (request('status')!==null && request('status')!=='')))
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary" title="Xoá bộ lọc"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    {{-- Bảng danh sách --}}
    <div class="card card-soft">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Danh sách</strong>
            <div class="d-none d-md-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary" id="btnExpandAll" type="button" title="Mở tất cả danh mục con">
                    <i class="bi bi-arrows-expand me-1"></i>Mở tất
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="btnCollapseAll" type="button" title="Đóng tất cả danh mục con">
                    <i class="bi bi-arrows-collapse me-1"></i>Đóng tất
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-wrap table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0 table-sticky">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px">ID</th>
                            <th style="width:90px">Ảnh</th>
                            <th>Tên</th>
                            <th>Danh mục cha</th>
                            <th>Trạng thái</th>
                            <th style="width:100px">Vị trí</th>
                            <th class="text-end" style="width:160px">Hành động</th>
                            <th style="width:34px"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if($roots->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-muted p-4">
                                <div class="mb-2 display-6"><i class="bi bi-archive"></i></div>
                                Chưa có danh mục nào.
                            </td>
                        </tr>
                    @else
                        @php
                            function renderCategoryRows($categories, $level = 0) {
                                foreach ($categories as $cat) {
                                    $children = $cat->children;
                                    $hasChildren = $children->count() > 0;
                                    $display = $level > 0 ? ' style="display: none;"' : '';
                                    $img = $cat->image ?? null;
                                    $imgUrl = $img ? (Str::startsWith($img, ['http://','https://','//']) ? $img : asset('storage/'.$img)) : 'https://via.placeholder.com/56x56?text=DM';
                                    $parentName = $cat->parent->name ?? '—';
                                    $padding = $level * 24;

                                    echo '<tr data-id="' . $cat->id . '" data-parent="' . ($cat->parent_id ?? 0) . '" data-level="' . $level . '" class="tree-row"' . $display . '>';
                                    echo '<td class="text-muted mono">#' . $cat->id . '</td>';
                                    echo '<td><img src="' . $imgUrl . '" alt="' . e($cat->name) . '" class="cat-thumb" loading="lazy" onerror="this.src=\'https://via.placeholder.com/56x56?text=DM\';"></td>';
                                    echo '<td class="text-truncate">';
                                    echo '<div class="d-flex align-items-center gap-2" style="padding-left: ' . $padding . 'px;">';
                                    if ($hasChildren) {
                                        echo '<button class="expander btn-icon border-0" type="button" data-toggle-id="' . $cat->id . '">';
                                        echo '<i class="bi bi-chevron-down chev"></i>';
                                        echo '</button>';
                                    } else {
                                        echo '<span class="text-muted"><i class="bi bi-dot"></i></span>';
                                    }
                                    echo '<a class="fw-semibold text-decoration-none" href="' . route('admin.categories.edit', $cat) . '" title="' . e($cat->name) . '">';
                                    echo e($cat->name);
                                    echo '</a>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '<td class="text-truncate">' . e($parentName) . '</td>';
                                    echo '<td>';
                                    if ($cat->is_active) {
                                        echo '<span class="badge badge-soft-success">Hiện</span>';
                                    } else {
                                        echo '<span class="badge badge-soft-secondary">Ẩn</span>';
                                    }
                                    echo '</td>';
                                    echo '<td>' . $cat->position . '</td>';
                                    echo '<td class="text-end">';
                                    echo '<div class="d-none d-md-inline-flex gap-1">';
                                    echo '<a href="' . route('admin.categories.show', $cat) . '" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Xem"><i class="bi bi-eye"></i></a>';
                                    echo '<a href="' . route('admin.categories.edit', $cat) . '" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Sửa"><i class="bi bi-pencil-square"></i></a>';
                                    echo '<form action="' . route('admin.categories.destroy', $cat) . '" method="POST" class="d-inline js-del-form" data-confirm="Xóa “' . e($cat->name) . '”?">@csrf @method("DELETE")<button class="btn btn-sm btn-danger" type="submit" data-bs-toggle="tooltip" title="Xóa"><i class="bi bi-trash"></i></button></form>';
                                    echo '</div>';
                                    echo '<div class="dropdown d-inline d-md-none">';
                                    echo '<button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>';
                                    echo '<ul class="dropdown-menu dropdown-menu-end">';
                                    echo '<li><a class="dropdown-item" href="' . route('admin.categories.show', $cat) . '"><i class="bi bi-eye me-2"></i>Xem</a></li>';
                                    echo '<li><a class="dropdown-item" href="' . route('admin.categories.edit', $cat) . '"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>';
                                    echo '<li><hr class="dropdown-divider"></li>';
                                    echo '<li><form action="' . route('admin.categories.destroy', $cat) . '" method="POST" class="js-del-form" data-confirm="Xóa “' . e($cat->name) . '”?">@csrf @method("DELETE")<button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>Xóa</button></form></li>';
                                    echo '</ul>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '<td></td>';
                                    echo '</tr>';

                                    // Đệ quy render các danh mục con
                                    renderCategoryRows($children, $level + 1);
                                }
                            }

                            renderCategoryRows($roots);
                        @endphp
                    @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                @if($isPaginated) {{ $categories->appends(request()->query())->links() }} @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        try { new bootstrap.Tooltip(el); } catch(e) {}
    });

    // Helper lấy danh sách phần tử
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    // Hàm hiển thị subtree với animation
    function showSubtree(id) {
        const children = $$(`tr[data-parent="${id}"]`);
        children.forEach(row => {
            row.classList.remove('hidden');
            requestAnimationFrame(() => {
                row.style.display = 'table-row';
                row.style.opacity = 1;
                row.style.transform = 'translateY(0)';
            });
            const chev = row.querySelector('.chev');
            if (chev && chev.classList.contains('rot')) {
                showSubtree(row.getAttribute('data-id'));
            }
        });
    }

    // Hàm ẩn subtree với animation
    function hideSubtree(id) {
        const children = $$(`tr[data-parent="${id}"]`);
        children.forEach(row => {
            row.style.opacity = 0;
            row.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                row.classList.add('hidden');
                row.style.display = 'none';
            }, 300);
            hideSubtree(row.getAttribute('data-id'));
        });
    }

    // Toggle row
    $$('[data-toggle-id]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-toggle-id');
            const chev = btn.querySelector('.chev');
            const isExpanded = chev.classList.contains('rot');
            if (isExpanded) {
                hideSubtree(id);
                chev.classList.remove('rot');
            } else {
                showSubtree(id);
                chev.classList.add('rot');
            }
        });
    });

    // Expand all
    document.getElementById('btnExpandAll')?.addEventListener('click', () => {
        $$('.expander').forEach(btn => {
            btn.querySelector('.chev').classList.add('rot');
        });
        $$('tr.tree-row[data-level][data-level!="0"]').forEach(row => {
            row.classList.remove('hidden');
            requestAnimationFrame(() => {
                row.style.display = 'table-row';
                row.style.opacity = 1;
                row.style.transform = 'translateY(0)';
            });
        });
    });

    // Collapse all
    document.getElementById('btnCollapseAll')?.addEventListener('click', () => {
        $$('.expander').forEach(btn => {
            btn.querySelector('.chev').classList.remove('rot');
        });
        $$('tr.tree-row[data-level][data-level!="0"]').forEach(row => {
            row.style.opacity = 0;
            row.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                row.classList.add('hidden');
                row.style.display = 'none';
            }, 300);
        });
    });

    // Xác nhận xóa
    $$('form.js-del-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const msg = this.getAttribute('data-confirm') || 'Xóa mục này?';
            if (window.Swal) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Xác nhận xóa',
                    text: msg,
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#dc3545'
                }).then(result => {
                    if (result.isConfirmed) this.submit();
                });
            } else {
                if (!confirm(msg)) e.preventDefault();
            }
        });
    });
});
</script>
@endpush
@endsection