{{-- resources/views/admins/categories/index.blade.php --}}
@extends('admins.layouts.app')

@section('title', 'Quản lý Danh mục')

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Pagination\AbstractPaginator;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $categories */
    $isPaginated = $categories instanceof AbstractPaginator;
    $total       = method_exists($categories, 'total') ? $categories->total() : $categories->count();
    $items       = $isPaginated ? collect($categories->items()) : collect($categories);

    // Gom con theo parent TRONG trang hiện tại
    $childrenMap = $items->groupBy('parent_id');
@endphp

@push('styles')
<style>
  .filter-bar{
    border-radius:16px; padding:12px;
    background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow: var(--shadow,0 10px 30px rgba(32,25,21,.12));
  }
  .card-soft{
    border-radius:16px; border:1px solid rgba(32,25,21,.08);
    box-shadow: var(--shadow,0 10px 30px rgba(32,25,21,.12)); background: var(--card,#fff);
  }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }

  .cat-thumb{
    width:56px; height:56px; object-fit:cover; border-radius:12px;
    background:#f7f2eb; border:1px solid rgba(32,25,21,.06);
  }

  .table-wrap{ position:relative }
  .table-sticky thead th{
    position: sticky; top: 0; z-index: 5;
    background: var(--card,#fff);
    border-bottom-color: rgba(32,25,21,.12) !important;
  }

  .table td,.table th{ vertical-align: middle }
  .table .text-truncate{ max-width: 360px }
  @media (max-width: 575.98px){ .table .text-truncate{ max-width: 200px } }

  .badge-soft-success{ background:#e5f7ed; color:#1e6b3a; border:1px solid rgba(24,121,78,.15) }
  .badge-soft-secondary{ background:#f2f2f2; color:#545b62; border:1px solid rgba(0,0,0,.08) }

  .expander{ --sz:28px; width:var(--sz); height:var(--sz); border-radius:8px;
    display:inline-grid; place-items:center; border:1px solid rgba(32,25,21,.12); background:#fff; }
  .expander:hover{ background:#faf6f0 }
  .chev{ transition: transform .18s ease }
  .chev.rot{ transform: rotate(180deg) }

  /* Hàng con trong table: không dùng collapse của Bootstrap để tránh giật */
  .child-row > td{ padding:0; background:#faf6f0 }
  .child-wrap{ overflow:hidden; height:0; transition: height .24s ease }
  .child-inner{
    padding:.6rem .75rem;
    border-left: 3px solid color-mix(in srgb, var(--brand,#C46F3B) 35%, white);
    background:#fff;
    border-radius: 0 12px 12px 0;
  }

  .subtable thead th{ background:#faf6f0 }
  .table-hover>tbody>tr:hover>*{ background: rgba(196,111,59,.05) }

  .btn-icon{ padding:.3rem .5rem }

  .mono{ font:500 .9rem ui-monospace, Menlo, Consolas, "Courier New", monospace }

  @media (prefers-reduced-motion: reduce){
    .chev, .child-wrap { transition: none !important }
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
              <th style="width: 90px;">Ảnh</th>
              <th>Tên</th>
              <th>Danh mục cha</th>
              <th>Trạng thái</th>
              <th style="width:100px">Vị trí</th>
              <th class="text-end" style="width:160px">Hành động</th>
              <th style="width:34px"></th>
            </tr>
          </thead>

          <tbody>
          @php $parents = $items->filter(fn($c)=> is_null($c->parent_id))->values(); @endphp

          @forelse($parents as $cat)
            @php
              $img       = $cat->image ?? null;
              $imgUrl    = $img ? (Str::startsWith($img, ['http://','https://','//']) ? $img : asset('storage/'.$img)) : null;
              $children  = $childrenMap->get($cat->id, collect());
              $rowId     = 'row-'.$cat->id;    // id của hàng con
              $wrapId    = 'wrap-'.$cat->id;   // id của wrapper animate
              $toggleSel = '#'.$rowId;
            @endphp
            <tr>
              <td class="text-muted">#{{ $cat->id }}</td>
              <td>
                <img src="{{ $imgUrl ?: 'https://via.placeholder.com/56x56?text=DM' }}"
                     alt="{{ $cat->name }}" class="cat-thumb" loading="lazy"
                     onerror="this.onerror=null;this.src='https://via.placeholder.com/56x56?text=DM';">
              </td>
              <td class="text-truncate">
                <div class="d-flex align-items-center gap-2">
                  @if($children->count() > 0)
                    <button class="expander btn-icon border-0" type="button"
                            data-toggle-row="{{ $toggleSel }}">
                      <i class="bi bi-chevron-down chev"></i>
                    </button>
                  @else
                    <span class="text-muted"><i class="bi bi-dot"></i></span>
                  @endif

                  <a class="fw-semibold text-decoration-none"
                     href="{{ route('admin.categories.edit', $cat) }}" title="{{ $cat->name }}">
                    {{ $cat->name }}
                  </a>
                </div>
              </td>
              <td class="text-truncate">—</td>
              <td>
                @if($cat->is_active)
                  <span class="badge badge-soft-success">Hiện</span>
                @else
                  <span class="badge badge-soft-secondary">Ẩn</span>
                @endif
              </td>
              <td>{{ $cat->position }}</td>
              <td class="text-end">
                <div class="d-none d-md-inline-flex gap-1">
                  <a href="{{ route('admin.categories.show', $cat) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Xem">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Sửa">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline js-del-form" data-confirm="Xóa “{{ $cat->name }}”?">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit" data-bs-toggle="tooltip" title="Xóa">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>

                <div class="dropdown d-inline d-md-none">
                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>
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

            @if($children->count() > 0)
              <tr class="child-row" id="{{ $rowId }}">
                <td colspan="8">
                  <div class="child-wrap" id="{{ $wrapId }}">
                    <div class="child-inner">
                      <div class="table-responsive">
                        <table class="table table-sm subtable align-middle mb-0">
                          <thead>
                            <tr>
                              <th style="width:70px">ID</th>
                              <th style="width:90px">Ảnh</th>
                              <th>Tên</th>
                              <th>Trạng thái</th>
                              <th style="width:100px">Vị trí</th>
                              <th class="text-end" style="width:160px">Hành động</th>
                            </tr>
                          </thead>
                          <tbody>
                          @foreach($children as $child)
                            @php
                              $cimg = $child->image ?? null;
                              $cimgUrl = $cimg ? (Str::startsWith($cimg, ['http://','https://','//']) ? $cimg : asset('storage/'.$cimg)) : null;
                            @endphp
                            <tr>
                              <td class="text-muted">#{{ $child->id }}</td>
                              <td>
                                <img src="{{ $cimgUrl ?: 'https://via.placeholder.com/56x56?text=DM' }}"
                                     class="cat-thumb" alt="{{ $child->name }}" loading="lazy"
                                     onerror="this.onerror=null;this.src='https://via.placeholder.com/56x56?text=DM';">
                              </td>
                              <td class="text-truncate">
                                <a class="fw-semibold text-decoration-none"
                                   href="{{ route('admin.categories.edit', $child) }}" title="{{ $child->name }}">
                                  {{ $child->name }}
                                </a>
                              </td>
                              <td>
                                @if($child->is_active)
                                  <span class="badge badge-soft-success">Hiện</span>
                                @else
                                  <span class="badge badge-soft-secondary">Ẩn</span>
                                @endif
                              </td>
                              <td>{{ $child->position }}</td>
                              <td class="text-end">
                                <div class="d-none d-md-inline-flex gap-1">
                                  <a href="{{ route('admin.categories.show', $child) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Xem">
                                    <i class="bi bi-eye"></i>
                                  </a>
                                  <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                  </a>
                                  <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="d-inline js-del-form" data-confirm='Xóa “{{ $child->name }}”?'>
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit" data-bs-toggle="tooltip" title="Xóa"><i class="bi bi-trash"></i></button>
                                  </form>
                                </div>

                                <div class="dropdown d-inline d-md-none">
                                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>
                                  <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.categories.show', $child) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.categories.edit', $child) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                      <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="js-del-form" data-confirm='Xóa “{{ $child->name }}”?'>
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>Xóa</button>
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
                      <div class="small text-muted mt-2">
                        Hiển thị các danh mục con nằm trong <em>trang hiện tại</em>.
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

          {{-- Orphan: con nhưng cha không nằm ở trang hiện tại --}}
          
          {{-- @php
            $parentsIds = $parents->pluck('id')->all();
            $orphans = $items->filter(fn($c)=> !is_null($c->parent_id) && !in_array($c->parent_id,$parentsIds,true));
          @endphp
          @foreach($orphans as $cat)
            @php
              $img = $cat->image ?? null;
              $imgUrl = $img ? (Str::startsWith($img, ['http://','https://','//']) ? $img : asset('storage/'.$img)) : null;
            @endphp
            <tr>
              <td class="text-muted">#{{ $cat->id }}</td>
              <td>
                <img src="{{ $imgUrl ?: 'https://via.placeholder.com/56x56?text=DM' }}"
                     class="cat-thumb" alt="{{ $cat->name }}" loading="lazy"
                     onerror="this.onerror=null;this.src='https://via.placeholder.com/56x56?text=DM';">
              </td>
              <td class="text-truncate">
                <a class="fw-semibold text-decoration-none" href="{{ route('admin.categories.edit', $cat) }}" title="{{ $cat->name }}">
                  {{ $cat->name }}
                </a>
              </td>
              <td class="text-truncate">{{ $cat->parent->name ?? '—' }}</td>
              <td>
                @if($cat->is_active)
                  <span class="badge badge-soft-success">Hiện</span>
                @else
                  <span class="badge badge-soft-secondary">Ẩn</span>
                @endif
              </td>
              <td>{{ $cat->position }}</td>
              <td class="text-end">
                <div class="d-none d-md-inline-flex gap-1">
                  <a href="{{ route('admin.categories.show', $cat) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Xem">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Sửa">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline js-del-form" data-confirm='Xóa “{{ $cat->name }}”?'>
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" type="submit" data-bs-toggle="tooltip" title="Xóa"><i class="bi bi-trash"></i></button>
                  </form>
                </div>
                <div class="dropdown d-inline d-md-none">
                  <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Hành động</button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.categories.show', $cat) }}"><i class="bi bi-eye me-2"></i>Xem</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.categories.edit', $cat) }}"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="js-del-form" data-confirm='Xóa “{{ $cat->name }}”?'>
                        @csrf @method('DELETE')
                        <button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>Xóa</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
              <td></td>
            </tr>
          @endforeach --}}

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
  document.addEventListener('DOMContentLoaded', function(){
    // Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      try{ new bootstrap.Tooltip(el); }catch(e){}
    });

    // Helper
    const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

    // Smooth slide height toggle
    function slideToggle(wrap, show){
      if(!wrap) return;
      const isOpen = wrap.offsetHeight > 0;

      if (show === undefined) show = !isOpen;

      if (show){ // open
        wrap.style.display = 'block';
        const h = wrap.scrollHeight + 'px';
        wrap.style.height = '0px';
        requestAnimationFrame(()=>{ wrap.style.height = h; });
      } else { // close
        const h = wrap.scrollHeight + 'px';
        wrap.style.height = h; // set current to pixel
        requestAnimationFrame(()=>{ wrap.style.height = '0px'; });
      }
    }

    // Transition end: fix to auto height when opened, set display none when closed
    $$('.child-wrap').forEach(wrap=>{
      wrap.addEventListener('transitionend', (e)=>{
        if(e.propertyName !== 'height') return;
        if (wrap.offsetHeight > 0){
          wrap.style.height = 'auto';
        }else{
          wrap.style.display = 'none';
        }
      });
    });

    // Row toggles
    $$('[data-toggle-row]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const rowSel = btn.getAttribute('data-toggle-row');
        const row = document.querySelector(rowSel);
        const wrap = row ? row.querySelector('.child-wrap') : null;
        const willOpen = wrap && wrap.offsetHeight === 0;
        slideToggle(wrap, willOpen);
        btn.querySelector('.chev')?.classList.toggle('rot', willOpen);
      });
    });

    // Expand / Collapse all
    document.getElementById('btnExpandAll')?.addEventListener('click', ()=>{
      $$('.child-wrap').forEach(w=>{
        if (w.offsetHeight === 0){
          w.style.display = 'block';
          const h = w.scrollHeight + 'px';
          w.style.height = '0px';
          requestAnimationFrame(()=>{ w.style.height = h; });
        }
      });
      $$('.chev').forEach(i=>i.classList.add('rot'));
    });
    document.getElementById('btnCollapseAll')?.addEventListener('click', ()=>{
      $$('.child-wrap').forEach(w=>{
        if (w.offsetHeight > 0){
          const h = w.scrollHeight + 'px';
          w.style.height = h;
          requestAnimationFrame(()=>{ w.style.height = '0px'; });
        }
      });
      $$('.chev').forEach(i=>i.classList.remove('rot'));
    });

    // Xác nhận xoá (SweetAlert2 nếu có)
    document.querySelectorAll('form.js-del-form').forEach(f=>{
      f.addEventListener('submit', function(e){
        const msg = this.getAttribute('data-confirm') || 'Xóa mục này?';
        if (window.Swal){
          e.preventDefault();
          Swal.fire({
            icon:'warning', title:'Xác nhận xoá', text: msg,
            showCancelButton:true, confirmButtonText:'Xóa', cancelButtonText:'Hủy',
            confirmButtonColor:'#dc3545'
          }).then(r=>{ if(r.isConfirmed) this.submit(); });
        }else{
          if(!confirm(msg)){ e.preventDefault(); }
        }
      });
    });
  });
</script>
@endpush
@endsection
