@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title', 'Quản lý Danh mục')

@push('styles')
<style>
/* ===== KHUNG BUBBLE CHO NHÓM CON ===== */
.category-index .child-inner{
  margin:.35rem 0;
  background:#fff;
  border-radius:12px;
  border-left:4px solid color-mix(in srgb, var(--brand) 40%, white);
  box-shadow: 0 4px 14px rgba(32,25,21,.06), inset 0 2px 8px rgba(32,25,21,.05);
  padding:.75rem;
}
.category-index .subtable>tbody>tr:not(:last-child)>td{ border-bottom:1px dashed rgba(32,25,21,.08) }

/* ===== ĐỒNG BỘ PALETTE ===== */
.category-index .filter-bar{
  border-radius: var(--radius);
  padding: 1rem;
  background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
  border: 1px solid rgba(32,25,21,.08);
  box-shadow: var(--shadow);
  animation: fadeIn .4s ease;
}
.category-index .card{
  border-radius: var(--radius);
  border:0; box-shadow: var(--shadow);
  background: linear-gradient(180deg, rgba(234,223,206,.25), transparent), var(--card);
  animation: fadeIn .4s ease;
}
.category-index .card-header{
  background:transparent;
  border-bottom:1px dashed rgba(32,25,21,.1);
  font-weight:600; color:var(--text);
}

/* ===== STICKY HEADER + CỘT TRÁI/PHẢI ===== */
.category-index .table-sticky thead th{
  position: sticky; top:0; z-index:5; background:var(--card);
  border-bottom-color:rgba(32,25,21,.12)!important;
}
.category-index th.sticky-col-left,
.category-index td.sticky-col-left{
  position:sticky; left:0; z-index:6; background:var(--card);
  box-shadow:8px 0 10px -8px rgba(32,25,21,.12);
}
.category-index th.sticky-col-right,
.category-index td.sticky-col-right{
  position:sticky; right:0; z-index:6; background:var(--card);
  box-shadow:-8px 0 10px -8px rgba(32,25,21,.12);
}

/* ===== BẢNG & ẢNH ===== */
.category-index .table td, .category-index .table th{ vertical-align: middle }
.category-index .table .text-truncate{ max-width:360px }
@media (max-width:575.98px){ .category-index .table .text-truncate{ max-width:200px } }

.category-index .cat-thumb{
  width:56px; height:56px; object-fit:cover; border-radius:12px;
  background:#f7f2eb; border:1px solid rgba(32,25,21,.06);
  transition: transform .18s ease, box-shadow .18s ease;
}
.category-index .cat-thumb:hover{ transform:translateY(-2px); box-shadow:0 4px 12px rgba(32,25,21,.12) }

/* ===== HÀNG CHA NHƯ CARD ===== */
.category-index tr.level-0 > *{
  background: linear-gradient(0deg, rgba(32,25,21,.05), transparent 60%);
  border-top: 1px solid rgba(32,25,21,.08);
  border-bottom: 1px solid rgba(32,25,21,.08);
}
.category-index tr.level-0 td:first-child{ border-top-left-radius:12px; border-bottom-left-radius:12px }
.category-index tr.level-0 td:last-child{ border-top-right-radius:12px; border-bottom-right-radius:12px }

/* ===== CONNECTOR CÂY (NHÌN RÕ HẲN) ===== */
.category-index td.tree-cell{ position:relative }
.category-index td.tree-cell .node-wrap{ position:relative; z-index:1 }
.category-index tr.level-1 td.tree-cell,
.category-index tr.level-2 td.tree-cell,
.category-index tr.level-3 td.tree-cell,
.category-index tr.level-4 td.tree-cell{ --tree-x: 28px } /* vị trí trục dọc */
.category-index tr.level-1 td.tree-cell::before,
.category-index tr.level-2 td.tree-cell::before,
.category-index tr.level-3 td.tree-cell::before,
.category-index tr.level-4 td.tree-cell::before{
  content:''; position:absolute; left:var(--tree-x); top:-14px; bottom:-14px; width:2px;
  background-image: linear-gradient(rgba(32,25,21,.16) 55%, rgba(0,0,0,0) 0);
  background-size: 2px 8px; background-repeat: repeat-y;
}
.category-index tr.level-1 td.tree-cell::after,
.category-index tr.level-2 td.tree-cell::after,
.category-index tr.level-3 td.tree-cell::after,
.category-index tr.level-4 td.tree-cell::after{
  content:''; position:absolute; left:var(--tree-x); top:50%; width:18px; height:2px;
  background: rgba(32,25,21,.16);
}

/* ===== CARET & HOVER ===== */
.category-index .caret-toggle{
  --sz:28px; width:var(--sz); height:var(--sz);
  border-radius:8px; display:inline-grid; place-items:center;
  border:1px solid rgba(32,25,21,.12); background:#fff; transition:.18s ease;
}
.category-index .caret-toggle:hover{ background:#faf6f0; transform: translateY(-1px) }
.category-index .caret-toggle .bi{ transition: transform .15s ease }
.category-index .caret-toggle[aria-expanded="true"] .bi{ transform: rotate(90deg) }

.category-index .table-hover>tbody>tr:hover>*{ background: rgba(196,111,59,.05) }

/* ===== BADGE & BUTTON ===== */
.category-index .badge.bg-success-soft{ background:#e5f7ed; color:#1e6b3a; border:1px solid rgba(24,121,78,.15) }
.category-index .badge.bg-secondary-soft{ background:#f2f2f2; color:#545b62; border:1px solid rgba(0,0,0,.08) }
.category-index .btn{ border-radius:10px; transition:.18s ease }
.category-index .btn-primary{ background:var(--brand); border-color:var(--brand) }
.category-index .btn-primary:hover{ background:var(--brand-600); border-color:var(--brand-600); transform: translateY(-1px) }
.category-index .btn-outline-primary{ color:var(--brand); border-color:var(--brand) }
.category-index .btn-outline-primary:hover{ background:var(--brand); color:#fff; transform: translateY(-1px) }
.category-index .btn-outline-secondary{ border-color:var(--muted) }
.category-index .btn-outline-secondary:hover{ background:var(--wood-800); color:#fff; transform: translateY(-1px) }
.category-index .btn-danger:hover{ transform: translateY(-1px) }

/* ===== INDENT THEO CẤP ===== */
.category-index tr.level-1 td{ padding-left:1.5rem !important }
.category-index tr.level-2 td{ padding-left:3rem  !important }
.category-index tr.level-3 td{ padding-left:4.5rem !important }
.category-index tr.level-4 td{ padding-left:6rem  !important }

/* ===== HIGHLIGHT KHI TÌM ===== */
.category-index mark.search-hit{
  background:#fff0d6; padding:.05rem .15rem; border-radius:4px;
  box-shadow: inset 0 0 0 1px rgba(0,0,0,.04);
}

/* ===== Responsive ===== */
@media (max-width:767.98px){
  .category-index .filter-bar{ padding:.75rem }
  .category-index .card{ border-radius:12px }
  .category-index .cat-thumb{ width:40px; height:40px }
  .category-index .caret-toggle{ --sz:24px }
}
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
@endphp

<div class="container-fluid category-index">
    {{-- FILTER --}}
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
                <button class="btn btn-outline-secondary flex-fill ripple"><i class="bi bi-funnel me-1"></i>Lọc</button>
                @if(request()->hasAny(['q','status']) && (request('q')!==null || (request('status')!==null && request('status')!=='')))
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary ripple" title="Xoá bộ lọc"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    {{-- TOAST --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- LIST --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Danh sách</strong>
            <div class="d-none d-md-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary ripple" id="btnExpandAll" type="button"><i class="bi bi-arrows-expand me-1"></i>Mở tất</button>
                <button class="btn btn-sm btn-outline-secondary ripple" id="btnCollapseAll" type="button"><i class="bi bi-arrows-collapse me-1"></i>Đóng tất</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-wrap table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0 table-sticky">
                    <thead class="table-light">
                        <tr>
                            <th class="sticky-col-left" style="width:70px">ID</th>
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
                        @php $roots = $items->filter(fn($c) => is_null($c->parent_id))->values(); @endphp
                        @forelse($roots as $root)
                            @include('admins.categories._node', ['node' => $root, 'level' => 0])
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
  // tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => { try{ new bootstrap.Tooltip(el) }catch(e){} });
  const $$ = (s,root=document)=>Array.from(root.querySelectorAll(s));

  // expand / collapse all
  document.getElementById('btnExpandAll')?.addEventListener('click', ()=>{
    $$('.collapse').forEach(w=>{ if(!w.classList.contains('show')) bootstrap.Collapse.getOrCreateInstance(w).show(); });
    $$('.caret-toggle').forEach(btn=>btn.setAttribute('aria-expanded','true'));
  });
  document.getElementById('btnCollapseAll')?.addEventListener('click', ()=>{
    $$('.collapse').forEach(w=>{ if(w.classList.contains('show')) bootstrap.Collapse.getOrCreateInstance(w).hide(); });
    $$('.caret-toggle').forEach(btn=>btn.setAttribute('aria-expanded','false'));
  });

  // ripple
  function addRipple(e){
    const btn=e.currentTarget, r=btn.getBoundingClientRect(), c=document.createElement('span'), d=Math.max(r.width,r.height);
    Object.assign(c.style,{width:d+'px',height:d+'px',position:'absolute',left:(e.clientX-r.left)+'px',top:(e.clientY-r.top)+'px',transform:'translate(-50%,-50%)',background:'rgba(196,111,59,.35)',borderRadius:'50%',pointerEvents:'none',opacity:'0.6',transition:'opacity .6s, width .6s, height .6s'}); btn.appendChild(c);
    requestAnimationFrame(()=>{ c.style.width=c.style.height=(d*1.8)+'px'; c.style.opacity='0'; }); setTimeout(()=>c.remove(),600);
  }
  $$('.ripple').forEach(b=>b.addEventListener('click',addRipple));

  // confirm delete
  document.querySelectorAll('form.js-del-form').forEach(f=>{
    f.addEventListener('submit', function(e){
      const msg=this.getAttribute('data-confirm')||'Xóa mục này?';
      if(window.Swal){ e.preventDefault(); Swal.fire({icon:'warning',title:'Xác nhận xóa',text:msg,showCancelButton:true,confirmButtonText:'Xóa',cancelButtonText:'Hủy',confirmButtonColor:'#dc3545'}).then(r=>{ if(r.isConfirmed) this.submit(); }); }
      else if(!confirm(msg)){ e.preventDefault(); }
    });
  });

  // auto expand + highlight on search
  const q=(new URLSearchParams(location.search)).get('q')?.trim();
  if(q){
    document.querySelectorAll('.collapse').forEach(c=>bootstrap.Collapse.getOrCreateInstance(c).show());
    document.querySelectorAll('.caret-toggle').forEach(btn=>btn.setAttribute('aria-expanded','true'));
    const esc=s=>s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'); const re=new RegExp('('+esc(q)+')','gi');
    document.querySelectorAll('.category-index .cat-name').forEach(el=>{ el.innerHTML = el.textContent.replace(re,'<mark class="search-hit">$1</mark>'); });
  }
});
</script>
@endpush
