@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title', 'Quản lý Danh mục')

@push('styles')
<style>
/* ================================
   Admin Categories – UI sync layout
   (Only UI; no logic changes)
================================ */
:root{
  /* sử dụng biến đã định nghĩa trong layout */
  --radius: 16px;
  --radius-sm: 12px;
  --shadow: var(--shadow);
  --brand: var(--brand);
  --brand-600: var(--brand-600);
  --card: var(--card);
  --text: var(--text);
  --muted: var(--muted);
  --wood-800: var(--wood-800);
}

.category-index .filter-bar{
  border-radius: var(--radius);
  padding: 1rem;
  background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
  border: 1px solid rgba(32,25,21,.08);
  box-shadow: var(--shadow);
  animation: fadeIn .4s ease;
}
.category-index .chip{
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.3rem .55rem; border-radius:999px;
  border:1px solid rgba(0,0,0,.08); background:#faf6f0; color:#6b5e54; font-size:.85rem;
}

.category-index .card{
  border-radius: var(--radius);
  border: 0;
  box-shadow: var(--shadow);
  background: linear-gradient(180deg, rgba(234,223,206,.22), transparent), var(--card);
  animation: fadeIn .4s ease;
}
.category-index .card-header{
  background: transparent;
  border-bottom: 1px dashed rgba(32,25,21,.10);
  color: var(--text);
  font-weight: 600;
}

.category-index .alert-success{
  background:#e5f7ed; color:#1e6b3a; border-radius:12px;
  border:1px solid rgba(24,121,78,.15); animation: fadeIn .3s ease;
}

/* Table wrapper + sticky header/columns */
.category-index .table-wrap{ position:relative; overflow:auto; }
.category-index .table-sticky thead th{
  position: sticky; top: 0; z-index: 5;
  background: var(--card);
  border-bottom-color: rgba(32,25,21,.12) !important;
}
.category-index .table td, .category-index .table th{ vertical-align: middle; }
.category-index .table .text-truncate{ max-width: 360px; }
@media (max-width: 575.98px){ .category-index .table .text-truncate{ max-width: 200px; } }

/* Sticky columns: ID (left) & Actions (right) */
.category-index .sticky-col-left{
  position: sticky; left: 0; z-index: 6; background: var(--card);
  box-shadow: 8px 0 10px -8px rgba(32,25,21,.15);
}
.category-index .sticky-col-right{
  position: sticky; right: 0; z-index: 6; background: var(--card);
  box-shadow: -8px 0 10px -8px rgba(32,25,21,.15);
}

/* Thumbnails */
.category-index .cat-thumb{
  width:56px; height:56px; object-fit:cover; border-radius:12px;
  background:#f7f2eb; border:1px solid rgba(32,25,21,.06);
  transition: transform .18s ease, box-shadow .18s ease;
}
.category-index .cat-thumb:hover{ transform: translateY(-2px); box-shadow:0 4px 12px rgba(32,25,21,.12); }

/* Caret toggle */
.category-index .caret-toggle{
  --sz: 28px;
  width:var(--sz); height:var(--sz); border-radius:8px; display:inline-grid; place-items:center;
  border:1px solid rgba(32,25,21,.12); background:#fff; transition:.18s ease;
}
.category-index .caret-toggle:hover{ background:#faf6f0; transform: translateY(-1px); }
.category-index .caret-toggle .bi{ transition: transform .15s ease; }
.category-index .caret-toggle[aria-expanded="true"] .bi{ transform: rotate(90deg); }

/* Child container */
.category-index .child-inner{
  padding:.75rem; border-left:3px solid color-mix(in srgb, var(--brand) 35%, white);
  background:#fff; border-radius:0 12px 12px 0; box-shadow: inset 0 2px 8px rgba(32,25,21,.06);
}
.category-index .subtable thead th{ background:#faf6f0 }

/* Hover rows */
.category-index .table-hover > tbody > tr:hover > *{
  background: rgba(196,111,59,.05);
  transition: background .18s ease;
}

/* Badges */
.category-index .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a; border:1px solid rgba(24,121,78,.15) }
.category-index .badge.bg-secondary-soft{ background:#f2f2f2; color:#545b62; border:1px solid rgba(0,0,0,.08) }

/* Buttons */
.category-index .btn{ border-radius: 10px; transition: .18s ease; }
.category-index .btn-primary{ background:var(--brand); border-color:var(--brand) }
.category-index .btn-primary:hover{ background:var(--brand-600); border-color:var(--brand-600); transform: translateY(-1px) }
.category-index .btn-outline-primary{ color:var(--brand); border-color:var(--brand) }
.category-index .btn-outline-primary:hover{ background:var(--brand); color:#fff; transform: translateY(-1px) }
.category-index .btn-outline-secondary{ border-color: var(--muted) }
.category-index .btn-outline-secondary:hover{ background: var(--wood-800); color:#fff; transform: translateY(-1px) }
.category-index .btn-danger:hover{ transform: translateY(-1px) }

/* Indent levels */
.category-index tr.level-1 td{ padding-left:1.5rem !important; }
.category-index tr.level-2 td{ padding-left:3rem !important; }
.category-index tr.level-3 td{ padding-left:4.5rem !important; }
.category-index tr.level-4 td{ padding-left:6rem !important; }

/* Responsive tune */
@media (max-width: 767.98px){
  .category-index .filter-bar{ padding:.75rem }
  .category-index .card{ border-radius:12px }
  .category-index .cat-thumb{ width:40px; height:40px }
  .category-index .caret-toggle{ --sz:24px }
  .category-index .btn{ font-size:.9rem; padding:.5rem 1rem }
  .category-index .alert-success{ font-size:.9rem; padding:.75rem }
}

/* Smooth appear */
@keyframes fadeIn{ from{opacity:0; transform: translateY(10px)} to{opacity:1; transform:none} }
</style>
@endpush

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Pagination\AbstractPaginator;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $categories */
    $isPaginated = $categories instanceof AbstractPaginator;
    $total = method_exists($categories, 'total') ? $categories->total() : $categories->count();
    $items = $isPaginated ? collect($categories->items()) : collect($categories);

    $hasFilter = request()->hasAny(['q','status']) && (
        filled(request('q')) || (request()->has('status') && request('status')!=='')
    );
@endphp

<div class="container-fluid category-index">
    {{-- FILTER BAR --}}
    <div class="filter-bar mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h1 class="h5 mb-0 fw-bold">Danh mục sản phẩm</h1>
                <span class="text-muted small">({{ number_format($total) }} mục)</span>
                @if($isPaginated)
                    <span class="text-muted small">• Trang {{ $categories->currentPage() }}/{{ $categories->lastPage() }}</span>
                @endif>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary ripple">
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
                <button class="btn btn-outline-secondary flex-fill ripple">
                    <i class="bi bi-funnel me-1"></i>Lọc
                </button>
                @if($hasFilter)
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary ripple" title="Xoá bộ lọc">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- filter chips --}}
        @if($hasFilter)
        <div class="mt-2 d-flex flex-wrap gap-2">
            @if(filled(request('q')))
                <span class="chip"><i class="bi bi-search"></i> Từ khoá: <strong>{{ request('q') }}</strong></span>
            @endif
            @if(request()->has('status') && request('status')!=='')
                <span class="chip"><i class="bi bi-eye{{ request('status')==='1' ? '' : '-slash' }}"></i>
                    Trạng thái: <strong>{{ request('status')==='1' ? 'Hiện' : 'Ẩn' }}</strong>
                </span>
            @endif
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- LIST CARD --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Danh sách</strong>
            <div class="d-none d-md-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary ripple" id="btnExpandAll" type="button">
                    <i class="bi bi-arrows-expand me-1"></i>Mở tất
                </button>
                <button class="btn btn-sm btn-outline-secondary ripple" id="btnCollapseAll" type="button">
                    <i class="bi bi-arrows-collapse me-1"></i>Đóng tất
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-wrap">
                <table class="table table-hover table-borderless align-middle mb-0 table-sticky">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px" class="sticky-col-left">ID</th>
                            <th style="width:90px">Ảnh</th>
                            <th>Tên</th>
                            <th>Danh mục cha</th>
                            <th>Trạng thái</th>
                            <th style="width:100px">Vị trí</th>
                            <th class="text-end sticky-col-right" style="width:160px">Hành động</th>
                            <th style="width:34px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items->filter(fn($c) => is_null($c->parent_id))->values() as $cat)
                            @php
                                $hasChildren = $cat->relationLoaded('children') ? $cat->children->isNotEmpty() : (optional($cat->children)->count() > 0);
                                $collapseId = 'child-cats-'.$cat->id;
                                $imgUrl = $cat->image
                                    ? (Str::startsWith($cat->image, ['http://','https://','//']) ? $cat->image : asset('storage/'.$cat->image))
                                    : null;
                            @endphp

                            {{-- ROW: PARENT --}}
                            <tr class="level-0">
                                <td class="text-muted sticky-col-left">#{{ $cat->id }}</td>
                                <td><img src="{{ $imgUrl ?: 'https://via.placeholder.com/56x56?text=DM' }}" class="cat-thumb" alt="{{ $cat->name }}" loading="lazy" onerror="this.onerror=null;this.src='https://via.placeholder.com/56x56?text=DM';"></td>
                                <td class="text-truncate">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($hasChildren)
                                            <button
                                                class="caret-toggle btn-icon border-0"
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
                                        <a class="fw-semibold text-decoration-none" href="{{ route('admin.categories.edit', $cat) }}" title="{{ $cat->name }}">{{ $cat->name }}</a>
                                    </div>
                                </td>
                                <td class="text-truncate">{{ $cat->parent?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $cat->is_active ? 'bg-success-soft' : 'bg-secondary-soft' }}">
                                        {{ $cat->is_active ? 'Hiện' : 'Ẩn' }}
                                    </span>
                                </td>
                                <td>{{ $cat->position }}</td>
                                <td class="text-end sticky-col-right">
                                    <div class="d-none d-md-inline-flex gap-1">
                                        <a href="{{ route('admin.categories.show', $cat) }}" class="btn btn-sm btn-outline-secondary ripple" data-bs-toggle="tooltip" title="Xem"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary ripple" data-bs-toggle="tooltip" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline js-del-form" data-confirm="Xóa “{{ $cat->name }}”?">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger ripple" type="submit" data-bs-toggle="tooltip" title="Xóa"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                    <div class="dropdown d-inline d-md-none">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle ripple" data-bs-toggle="dropdown">Hành động</button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.categories.show', $cat) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.categories.edit', $cat) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="js-del-form" data-confirm="Xóa “{{ $cat->name }}”?">
                                                    @csrf @method('DELETE')
                                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>Xóa</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td></td>
                            </tr>

                            {{-- ROW: CHILDREN (collapsible) --}}
                            @if($hasChildren)
                                <tr>
                                    <td colspan="8" class="p-0">
                                        <div class="collapse" id="{{ $collapseId }}">
                                            <div class="child-inner">
                                                <div class="table-responsive">
                                                    <table class="table table-sm subtable align-middle mb-0">
                                                        <tbody>
                                                            @foreach($cat->children as $child)
                                                                @php
                                                                    $childImgUrl = $child->image
                                                                        ? (Str::startsWith($child->image, ['http://','https://','//']) ? $child->image : asset('storage/'.$child->image))
                                                                        : null;
                                                                @endphp
                                                                <tr class="level-1">
                                                                    <td class="text-muted">#{{ $child->id }}</td>
                                                                    <td><img src="{{ $childImgUrl ?: 'https://via.placeholder.com/56x56?text=DM' }}" class="cat-thumb" alt="{{ $child->name }}" loading="lazy" onerror="this.onerror=null;this.src='https://via.placeholder.com/56x56?text=DM';"></td>
                                                                    <td class="text-truncate">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <span class="text-muted"><i class="bi bi-dot"></i></span>
                                                                            <a class="fw-semibold text-decoration-none" href="{{ route('admin.categories.edit', $child) }}" title="{{ $child->name }}">└─ {{ $child->name }}</a>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-truncate">{{ $cat->name }}</td>
                                                                    <td>
                                                                        <span class="badge {{ $child->is_active ? 'bg-success-soft' : 'bg-secondary-soft' }}">
                                                                            {{ $child->is_active ? 'Hiện' : 'Ẩn' }}
                                                                        </span>
                                                                    </td>
                                                                    <td>{{ $child->position }}</td>
                                                                    <td class="text-end">
                                                                        <div class="d-none d-md-inline-flex gap-1">
                                                                            <a href="{{ route('admin.categories.show', $child) }}" class="btn btn-sm btn-outline-secondary ripple" data-bs-toggle="tooltip" title="Xem"><i class="bi bi-eye"></i></a>
                                                                            <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-outline-primary ripple" data-bs-toggle="tooltip" title="Sửa"><i class="bi bi-pencil-square"></i></a>
                                                                            <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="d-inline js-del-form" data-confirm="Xóa “{{ $child->name }}”?">
                                                                                @csrf @method('DELETE')
                                                                                <button class="btn btn-sm btn-danger ripple" type="submit" data-bs-toggle="tooltip" title="Xóa"><i class="bi bi-trash"></i></button>
                                                                            </form>
                                                                        </div>
                                                                        <div class="dropdown d-inline d-md-none">
                                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle ripple" data-bs-toggle="dropdown">Hành động</button>
                                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                                <li><a class="dropdown-item" href="{{ route('admin.categories.show', $child) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
                                                                                <li><a class="dropdown-item" href="{{ route('admin.categories.edit', $child) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                                                                                <li><hr class="dropdown-divider"></li>
                                                                                <li>
                                                                                    <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="js-del-form" data-confirm="Xóa “{{ $child->name }}”?">
                                                                                        @csrf @method('DELETE')
                                                                                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>Xóa</button>
                                                                                    </form>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted p-4">
                                    <div class="mb-2 display-6"><i class="bi bi-archive"></i></div>
                                    Chưa có danh mục nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                @if($isPaginated) {{ $categories->appends(request()->query())->links() }} @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        try { new bootstrap.Tooltip(el); } catch(e) {}
    });
    const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

    // Expand all
    document.getElementById('btnExpandAll')?.addEventListener('click', () => {
        $$('.collapse').forEach(w => { if (!w.classList.contains('show')) bootstrap.Collapse.getOrCreateInstance(w).show(); });
        $$('.caret-toggle').forEach(btn => btn.setAttribute('aria-expanded','true'));
    });
    // Collapse all
    document.getElementById('btnCollapseAll')?.addEventListener('click', () => {
        $$('.collapse').forEach(w => { if (w.classList.contains('show')) bootstrap.Collapse.getOrCreateInstance(w).hide(); });
        $$('.caret-toggle').forEach(btn => btn.setAttribute('aria-expanded','false'));
    });

    // Ripple (nhẹ)
    function addRipple(e){
        const btn = e.currentTarget, rect = btn.getBoundingClientRect();
        const circle = document.createElement('span'); const d = Math.max(rect.width, rect.height);
        Object.assign(circle.style, {
            width:d+'px', height:d+'px', position:'absolute',
            left:(e.clientX-rect.left)+'px', top:(e.clientY-rect.top)+'px',
            transform:'translate(-50%,-50%)', background:'rgba(196,111,59,.35)',
            borderRadius:'50%', pointerEvents:'none', opacity:'0.6',
            transition:'opacity .6s, width .6s, height .6s'
        });
        btn.appendChild(circle);
        requestAnimationFrame(()=>{ circle.style.width=circle.style.height=(d*1.8)+'px'; circle.style.opacity='0'; });
        setTimeout(()=>circle.remove(),600);
    }
    $$('.ripple').forEach(btn => btn.addEventListener('click', addRipple));

    // SweetAlert confirm delete (fallback confirm nếu không có Swal)
    document.querySelectorAll('form.js-del-form').forEach(f => {
        f.addEventListener('submit', function(e){
            const msg = this.getAttribute('data-confirm') || 'Xóa mục này?';
            if (window.Swal){
                e.preventDefault();
                Swal.fire({ icon:'warning', title:'Xác nhận xóa', text:msg, showCancelButton:true, confirmButtonText:'Xóa', cancelButtonText:'Hủy', confirmButtonColor:'#dc3545' })
                    .then(r => { if (r.isConfirmed) this.submit(); });
            }else{
                if (!confirm(msg)) e.preventDefault();
            }
        });
    });
});
</script>
@endpush
