@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title', 'Quản lý Danh mục')

@push('styles')
<style>
    /* Đồng bộ với bảng màu từ app.blade.php */
    .category-index .filter-bar {
        border-radius: var(--radius);
        padding: 1rem;
        background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
        border: 1px solid rgba(32,25,21,.08);
        box-shadow: var(--shadow);
        animation: fadeIn 0.5s ease;
    }

    .category-index .card {
        border-radius: var(--radius);
        border: 0;
        box-shadow: var(--shadow);
        background: linear-gradient(180deg, rgba(234,223,206,.25), transparent), var(--card);
        animation: fadeIn 0.5s ease;
    }

    .category-index .card-header {
        background: transparent;
        border-bottom: 1px dashed rgba(32,25,21,.1);
        font-weight: 600;
        color: var(--text);
    }

    .category-index .alert-success {
        background: #e5f7ed;
        color: #1e6b3a;
        border-radius: 12px;
        border: 1px solid rgba(24,121,78,.15);
        animation: fadeIn 0.5s ease;
    }

    .category-index .cat-thumb {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 12px;
        background: #f7f2eb;
        border: 1px solid rgba(32,25,21,.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .category-index .cat-thumb:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(32,25,21,.12);
    }

    .category-index .table-wrap {
        position: relative;
    }

    .category-index .table-sticky thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: var(--card);
        border-bottom-color: rgba(32,25,21,.12) !important;
    }

    .category-index .table td, .category-index .table th {
        vertical-align: middle;
    }

    .category-index .table .text-truncate {
        max-width: 360px;
    }

    @media (max-width: 575.98px) {
        .category-index .table .text-truncate {
            max-width: 200px;
        }
    }

    .category-index .badge.bg-success-soft {
        background: #e5f7ed;
        color: #1e6b3a;
        border: 1px solid rgba(24,121,78,.15);
    }

    .category-index .badge.bg-secondary-soft {
        background: #f2f2f2;
        color: #545b62;
        border: 1px solid rgba(0,0,0,.08);
    }

    .category-index .expander {
        --sz: 28px;
        width: var(--sz);
        height: var(--sz);
        border-radius: 8px;
        display: inline-grid;
        place-items: center;
        border: 1px solid rgba(32,25,21,.12);
        background: #fff;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .category-index .expander:hover {
        background: #faf6f0;
        transform: translateY(-1px);
    }

    .category-index .chev {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .category-index .chev.rot {
        transform: rotate(180deg);
    }

    .category-index .child-row > td {
        padding: 0;
        background: #faf6f0;
        transition: background 0.3s ease;
    }

    .category-index .child-wrap {
        overflow: hidden;
        height: 0;
        transition: height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .category-index .child-inner {
        padding: 0.75rem;
        border-left: 3px solid color-mix(in srgb, var(--brand) 35%, white);
        background: #fff;
        border-radius: 0 12px 12px 0;
        box-shadow: inset 0 2px 8px rgba(32,25,21,.06);
    }

    .category-index .subtable thead th {
        background: #faf6f0;
    }

    .category-index .table-hover > tbody > tr:hover > * {
        background: rgba(196,111,59,.05);
        transition: background 0.2s ease;
    }

    .category-index .btn-icon {
        padding: 0.3rem 0.5rem;
    }

    .category-index .btn {
        border-radius: 10px;
        transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }

    .category-index .btn-primary {
        background: var(--brand);
        border-color: var(--brand);
    }

    .category-index .btn-primary:hover {
        background: var(--brand-600);
        border-color: var(--brand-600);
        transform: translateY(-1px);
    }

    .category-index .btn-outline-primary {
        color: var(--brand);
        border-color: var(--brand);
    }

    .category-index .btn-outline-primary:hover {
        background: var(--brand);
        color: #fff;
        transform: translateY(-1px);
    }

    .category-index .btn-outline-secondary {
        border-color: var(--muted);
    }

    .category-index .btn-outline-secondary:hover {
        background: var(--wood-800);
        border-color: var(--wood-800);
        color: #fff;
        transform: translateY(-1px);
    }

    .category-index .btn.ripple {
        position: relative;
        overflow: hidden;
    }

    .category-index .btn.ripple:after {
        content: '';
        position: absolute;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,.35);
        transform: translate(-50%,-50%);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.6s, width 0.6s, height 0.6s;
    }

    .category-index .btn.ripple:active:after {
        width: 100px;
        height: 100px;
        opacity: 0.6;
    }

    /* Indent cho các cấp con */
    .category-index tr.level-1 td {
        padding-left: 1.5rem !important;
    }

    .category-index tr.level-2 td {
        padding-left: 3rem !important;
    }

    .category-index tr.level-3 td {
        padding-left: 4.5rem !important;
    }

    .category-index tr.level-4 td {
        padding-left: 6rem !important;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .category-index .filter-bar {
            padding: 0.75rem;
        }

        .category-index .card {
            border-radius: 12px;
        }

        .category-index .cat-thumb {
            width: 40px;
            height: 40px;
        }

        .category-index .expander {
            --sz: 24px;
        }

        .category-index .h5 {
            font-size: 1.1rem;
        }

        .category-index .btn {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        .category-index .alert-success {
            font-size: 0.9rem;
            padding: 0.75rem;
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Pagination\AbstractPaginator;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection $categories */
    $isPaginated = $categories instanceof AbstractPaginator;
    $total       = method_exists($categories, 'total') ? $categories->total() : $categories->count();
    $items       = $isPaginated ? collect($categories->items()) : collect($categories);

    // Gom con theo parent_id trong trang hiện tại
    $childrenMap = $items->groupBy('parent_id');

    /**
     * Hàm đệ quy hiển thị 1 hàng danh mục + các con cháu
     * Thêm $level để indent và class level
     */
    function renderCategoryRow($cat, $childrenMap, $level = 0){
        $hasChildren = isset($childrenMap[$cat->id]);
        $imgUrl = $cat->image
            ? (Str::startsWith($cat->image, ['http://','https://','//']) ? $cat->image : asset('storage/'.$cat->image))
            : null;

        echo '<tr class="level-' . $level . '">';
          echo '<td class="text-muted">#'.$cat->id.'</td>';
          echo '<td><img src="'.($imgUrl ?: 'https://via.placeholder.com/56x56?text=DM').'" class="cat-thumb" alt="'.$cat->name.'" loading="lazy" onerror="this.onerror=null;this.src=\'https://via.placeholder.com/56x56?text=DM\';"></td>';
          echo '<td class="text-truncate">';
            echo '<div class="d-flex align-items-center gap-2">';
              if($hasChildren){
                echo '<button class="expander btn-icon border-0" type="button" data-toggle-row="#row-'.$cat->id.'"><i class="bi bi-chevron-down chev"></i></button>';
              }else{
                echo '<span class="text-muted"><i class="bi bi-dot"></i></span>';
              }
              echo '<a class="fw-semibold text-decoration-none" href="'.route('admin.categories.edit',$cat).'" title="'.$cat->name.'">'.$cat->name.'</a>';
            echo '</div>';
          echo '</td>';
          echo '<td class="text-truncate">'.($cat->parent?->name ?? '—').'</td>';
          echo '<td>'.($cat->is_active ? '<span class="badge bg-success-soft">Hiện</span>' : '<span class="badge bg-secondary-soft">Ẩn</span>').'</td>';
          echo '<td>'.$cat->position.'</td>';
          echo '<td class="text-end">';
            echo '<div class="d-none d-md-inline-flex gap-1">';
              echo '<a href="'.route('admin.categories.show',$cat).'" class="btn btn-sm btn-outline-secondary ripple" data-bs-toggle="tooltip" title="Xem"><i class="bi bi-eye"></i></a>';
              echo '<a href="'.route('admin.categories.edit',$cat).'" class="btn btn-sm btn-outline-primary ripple" data-bs-toggle="tooltip" title="Sửa"><i class="bi bi-pencil-square"></i></a>';
              echo '<form action="'.route('admin.categories.destroy',$cat).'" method="POST" class="d-inline js-del-form" data-confirm="Xóa “'.$cat->name.'”?">'.csrf_field().method_field('DELETE').'<button class="btn btn-sm btn-danger ripple" type="submit" data-bs-toggle="tooltip" title="Xóa"><i class="bi bi-trash"></i></button></form>';
            echo '</div>';
            echo '<div class="dropdown d-inline d-md-none">';
              echo '<button class="btn btn-sm btn-outline-secondary dropdown-toggle ripple" data-bs-toggle="dropdown">Hành động</button>';
              echo '<ul class="dropdown-menu dropdown-menu-end">';
                echo '<li><a class="dropdown-item" href="'.route('admin.categories.show',$cat).'"><i class="bi bi-eye me-2"></i>Xem</a></li>';
                echo '<li><a class="dropdown-item" href="'.route('admin.categories.edit',$cat).'"><i class="bi bi-pencil-square me-2"></i>Sửa</a></li>';
                echo '<li><hr class="dropdown-divider"></li>';
                echo '<li>';
                  echo '<form action="'.route('admin.categories.destroy',$cat).'" method="POST" class="js-del-form" data-confirm="Xóa “'.$cat->name.'”?">';
                    echo csrf_field().method_field('DELETE');
                    echo '<button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>Xóa</button>';
                  echo '</form>';
                echo '</li>';
              echo '</ul>';
            echo '</div>';
          echo '</td>';
          echo '<td></td>';
        echo '</tr>';

        if($hasChildren){
          echo '<tr class="child-row level-' . $level . '" id="row-'.$cat->id.'">';
            echo '<td colspan="8">';
              echo '<div class="child-wrap" id="wrap-'.$cat->id.'">';
                echo '<div class="child-inner">';
                  echo '<div class="table-responsive">';
                    echo '<table class="table table-sm subtable align-middle mb-0"><tbody>';
                      foreach($childrenMap[$cat->id] as $child){
                        renderCategoryRow($child, $childrenMap, $level + 1); // Đệ quy với level +1
                      }
                    echo '</tbody></table>';
                  echo '</div>';
                echo '</div>';
              echo '</div>';
            echo '</td>';
          echo '</tr>';
        }
    }
@endphp

<div class="container-fluid category-index">
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
        @if(request()->hasAny(['q','status']) && (request('q')!==null || (request('status')!==null && request('status')!=='')))
          <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary ripple" title="Xoá bộ lọc"><i class="bi bi-x-lg"></i></a>
        @endif
      </div>
    </form>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
  @endif

  {{-- Bảng danh sách --}}
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
            @php $parents = $items->filter(fn($c)=> is_null($c->parent_id))->values(); @endphp
            @forelse($parents as $cat)
              @php renderCategoryRow($cat, $childrenMap, 0); @endphp
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  // Khởi tạo tooltip
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    try { new bootstrap.Tooltip(el); } catch(e) {}
  });

  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

  // Hàm toggle slide mượt mà
  function slideToggle(wrap, show) {
    if (!wrap) return;
    const isOpen = wrap.offsetHeight > 0;
    if (show === undefined) show = !isOpen;
    const duration = 400; // ms
    if (show) {
      wrap.style.display = 'block';
      wrap.style.overflow = 'hidden';
      const h = wrap.scrollHeight;
      wrap.style.transition = `height ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
      wrap.style.height = '0px';
      requestAnimationFrame(() => {
        wrap.style.height = h + 'px';
      });
    } else {
      const h = wrap.scrollHeight;
      wrap.style.transition = `height ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
      wrap.style.height = h + 'px';
      requestAnimationFrame(() => {
        wrap.style.height = '0px';
      });
    }
  }

  // Xử lý transition end
  $$('.child-wrap').forEach(wrap => {
    wrap.addEventListener('transitionend', (e) => {
      if (e.propertyName !== 'height') return;
      if (wrap.offsetHeight > 0) {
        wrap.style.height = 'auto';
        wrap.style.overflow = 'visible';
      } else {
        wrap.style.display = 'none';
        wrap.style.overflow = 'hidden';
      }
    });
  });

  // Toggle từng hàng
  $$('[data-toggle-row]').forEach(btn => {
    btn.addEventListener('click', () => {
      const rowSel = btn.getAttribute('data-toggle-row');
      const row = document.querySelector(rowSel);
      const wrap = row ? row.querySelector('.child-wrap') : null;
      const willOpen = wrap && wrap.offsetHeight === 0;
      slideToggle(wrap, willOpen);
      btn.querySelector('.chev')?.classList.toggle('rot', willOpen);
    });
  });

  // Mở tất cả
  document.getElementById('btnExpandAll')?.addEventListener('click', () => {
    $$('.child-wrap').forEach(w => {
      if (w.offsetHeight === 0) {
        slideToggle(w, true);
      }
    });
    $$('.chev').forEach(i => i.classList.add('rot'));
  });

  // Đóng tất cả
  document.getElementById('btnCollapseAll')?.addEventListener('click', () => {
    $$('.child-wrap').forEach(w => {
      if (w.offsetHeight > 0) {
        slideToggle(w, false);
      }
    });
    $$('.chev').forEach(i => i.classList.remove('rot'));
  });

  // Hiệu ứng ripple cho nút
  function addRipple(e) {
    const btn = e.currentTarget;
    const rect = btn.getBoundingClientRect();
    const circle = document.createElement('span');
    const d = Math.max(rect.width, rect.height);
    Object.assign(circle.style, {
      width: d + 'px',
      height: d + 'px',
      position: 'absolute',
      left: (e.clientX - rect.left) + 'px',
      top: (e.clientY - rect.top) + 'px',
      transform: 'translate(-50%, -50%)',
      background: 'rgba(196,111,59,.35)',
      borderRadius: '50%',
      pointerEvents: 'none',
      opacity: '0.6',
      transition: 'opacity 0.6s, width 0.6s, height 0.6s'
    });
    btn.appendChild(circle);
    requestAnimationFrame(() => {
      circle.style.width = circle.style.height = (d * 1.8) + 'px';
      circle.style.opacity = '0';
    });
    setTimeout(() => circle.remove(), 600);
  }

  $$('.ripple').forEach(btn => {
    btn.addEventListener('click', addRipple);
  });

  // Xác nhận xóa với SweetAlert
  document.querySelectorAll('form.js-del-form').forEach(f => {
    f.addEventListener('submit', function(e) {
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
        }).then(r => { if (r.isConfirmed) this.submit(); });
      } else {
        if (!confirm(msg)) { e.preventDefault(); }
      }
    });
  });
});
</script>
@endpush
@endsection