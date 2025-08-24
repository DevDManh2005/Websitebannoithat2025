@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title','Quyền')

@section('content')
@php
use Illuminate\Support\Str;

/** @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|array $perms */

/* Tổng số item an toàn cho cả paginator/collection */
$total = method_exists($perms,'total')
    ? (int) $perms->total()
    : (is_countable($perms) ? count($perms) : 0);

/* Lấy ra collection các item để pluck, tránh collect($perms) nhầm vào object */
$items = $perms instanceof \Illuminate\Contracts\Pagination\Paginator
    ? $perms->getCollection()
    : collect($perms ?? []);

/* Việt hoá nhãn module / action (có thể truyền từ controller) */
$moduleLabels = (array)($moduleLabels ?? []);
$actionLabels = (array)($actionLabels ?? []);

$defaultModuleLabels = [
  'orders'=>'Đơn hàng','order'=>'Đơn hàng',
  'products'=>'Sản phẩm','product'=>'Sản phẩm',
  'categories'=>'Danh mục','category'=>'Danh mục',
  'brands'=>'Thương hiệu','brand'=>'Thương hiệu',
  'blogs'=>'Bài viết','blog'=>'Bài viết',
  'users'=>'Tài khoản','user'=>'Tài khoản',
  'permissions'=>'Quyền','permission'=>'Quyền',
  'roles'=>'Vai trò','role'=>'Vai trò',
  'settings'=>'Cài đặt','setting'=>'Cài đặt',
];
$defaultActionLabels = [
  'index'=>'Danh sách','list'=>'Danh sách',
  'view'=>'Xem','show'=>'Xem',
  'create'=>'Thêm','store'=>'Lưu','add'=>'Thêm',
  'edit'=>'Sửa','update'=>'Cập nhật',
  'delete'=>'Xóa','destroy'=>'Xóa','remove'=>'Xóa',
  'approve'=>'Duyệt','ship'=>'Giao hàng','ready_to_ship'=>'Sẵn sàng giao',
  'export'=>'Xuất','import'=>'Nhập','assign'=>'Gán','revoke'=>'Thu hồi',
];

$M = $moduleLabels + $defaultModuleLabels;
$A = $actionLabels + $defaultActionLabels;

/* Options lọc: ưu tiên biến từ controller, fallback theo dữ liệu hiện có */
$moduleOptions = (array)($moduleOptions ?? []);
$actionOptions = (array)($actionOptions ?? []);
if (empty($moduleOptions)) {
  try { $moduleOptions = $items->pluck('module_name')->filter()->unique()->values()->all(); } catch (\Throwable $e) { $moduleOptions = []; }
}
if (empty($actionOptions)) {
  try { $actionOptions = $items->pluck('action')->filter()->unique()->values()->all(); } catch (\Throwable $e) { $actionOptions = []; }
}
@endphp

<style>
  :root{
    --soft-border: 1px solid rgba(32,25,21,.08);
    --soft-shadow: 0 4px 18px rgba(18, 38, 63, .06);
  }
  .filter-bar{
    border-radius:16px; padding:12px;
    background: linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:var(--soft-border); box-shadow: var(--soft-shadow);
  }
  .card-soft{ border-radius:16px; border:var(--soft-border); box-shadow: var(--soft-shadow); }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
  .table td,.table th{ vertical-align: middle; }
  .table-hover tbody tr:hover{ background:#fafbff; }

  .code-chip{
    display:inline-flex; align-items:center; gap:6px; padding:6px 10px;
    background:#f7f7fb; border:1px dashed rgba(23,26,31,.15); border-radius:10px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size:.9375rem;
  }
  .code-chip .copy-btn{
    border:0; background:transparent; cursor:pointer; padding:0; line-height:1; color:#6c757d;
  }
  .code-chip .copy-btn:hover{ color:#000; }

  /* Kill mọi pseudo-element lạ “đắp” vào prev/next phân trang */
  .pagination::before, .pagination::after,
  .pagination *::before, .pagination *::after,
  .page-item::before, .page-item::after,
  .page-item .page-link::before, .page-item .page-link::after,
  a[rel="prev"]::before, a[rel="prev"]::after,
  a[rel="next"]::before, a[rel="next"]::after,
  .previous::before, .previous::after,
  .next::before, .next::after,
  .prev::before, .prev::after{
    content:none !important; display:none !important;
    background:none !important; border:0 !important; box-shadow:none !important;
  }
  .pagination .page-item.prev .page-link,
  .pagination .page-item.next .page-link,
  .pagination .page-link[rel="prev"],
  .pagination .page-link[rel="next"]{
    position:static !important; background:none !important; box-shadow:none !important;
    width:auto !important; height:auto !important; overflow:visible !important;
  }
</style>

<div class="container-fluid">
  {{-- Thanh tiêu đề + hành động --}}
  <div class="filter-bar mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <h1 class="h5 mb-0 fw-bold">Quyền</h1>
        <span class="text-muted small">(tổng {{ number_format($total) }})</span>
      </div>
      <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Thêm quyền
      </a>
    </div>

    {{-- Bộ lọc nhanh (GET) --}}
    <form class="row g-2 mt-2" method="get" action="{{ route('admin.permissions.index') }}">
      <div class="col-12 col-lg-6">
        <div class="input-group">
          <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control" value="{{ request('q') }}"
                 placeholder="Tìm theo phân hệ, hành động hoặc diễn giải…">
        </div>
      </div>

      <div class="col-6 col-lg-3">
        <select name="module" class="form-select">
          <option value="">-- Phân hệ --</option>
          @foreach($moduleOptions as $opt)
            @php $label = $M[$opt] ?? Str::of($opt)->replace(['_','-'],' ')->headline(); @endphp
            <option value="{{ $opt }}" @selected(request('module')===$opt)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-6 col-lg-2">
        <select name="action" class="form-select">
          <option value="">-- Hành động --</option>
          @foreach($actionOptions as $opt)
            @php $label = $A[$opt] ?? Str::of($opt)->replace('_',' ')->headline(); @endphp
            <option value="{{ $opt }}" @selected(request('action')===$opt)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-lg-1 d-grid">
        <button class="btn btn-outline-secondary"><i class="bi bi-funnel me-1"></i> Lọc</button>
      </div>

      @if(request()->hasAny(['q','module','action']))
        <div class="col-12">
          <a class="small text-decoration-none text-muted" href="{{ route('admin.permissions.index') }}">
            <i class="bi bi-x-circle me-1"></i> Xóa bộ lọc
          </a>
        </div>
      @endif
    </form>
  </div>

  {{-- Bảng dữ liệu --}}
  <div class="card card-soft">
    <div class="card-header d-flex justify-content-between align-items-center">
      <strong>Danh sách quyền</strong>
      @if(method_exists($perms,'firstItem') && method_exists($perms,'lastItem'))
        <span class="text-muted small">
          Hiển thị {{ number_format($perms->firstItem() ?? 0) }}–{{ number_format($perms->lastItem() ?? 0) }} / {{ number_format($total) }}
        </span>
      @else
        <span class="text-muted small">Tổng: {{ number_format($total) }}</span>
      @endif
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover">
          <thead class="table-light">
          <tr>
            <th style="width:70px">#</th>
            <th style="width:22%">Phân hệ</th>
            <th style="width:22%">Hành động</th>
            <th>Diễn giải</th>
            <th class="text-end" style="width:160px">Thao tác</th>
          </tr>
          </thead>
          <tbody>
          @forelse($items as $p)
            @php
              $module = (string)($p->module_name ?? '');
              $action = (string)($p->action ?? '');
              $modLabel = $M[$module] ?? Str::of($module)->replace(['_','-'],' ')->headline();
              $actLabel = $A[$action] ?? Str::of($action)->replace('_',' ')->headline();
              $human = trim($actLabel.' '.$modLabel);
              $code = $module && $action ? "{$module}.{$action}" : ($p->name ?? trim($module.' '.$action));
            @endphp
            <tr>
              <td class="text-muted">#{{ $p->id }}</td>
              <td>
                {{ $modLabel }}
                <div class="small text-muted">({{ $module }})</div>
              </td>
              <td>
                {{ $actLabel }}
                <div class="small text-muted">({{ $action }})</div>
              </td>
              <td>
                <div class="fw-medium mb-1">{{ $human }}</div>
                <span class="code-chip" title="Mã quyền">
                  <code class="me-1">{{ $code }}</code>
                  <button class="copy-btn js-copy" type="button" data-value="{{ $code }}">
                    <i class="bi bi-clipboard"></i>
                  </button>
                </span>
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.permissions.edit', $p) }}" title="Sửa">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('admin.permissions.destroy', $p) }}" method="post" class="d-inline"
                      onsubmit="return confirm('Xóa quyền này?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="Xóa">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">Chưa có quyền nào.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-muted small">
          @if(method_exists($perms,'firstItem') && method_exists($perms,'lastItem'))
            Hiển thị {{ number_format($perms->firstItem() ?? 0) }}–{{ number_format($perms->lastItem() ?? 0) }} / {{ number_format($total) }}
          @else
            Tổng: {{ number_format($total) }}
          @endif
        </div>
        <div>
          @if(method_exists($perms,'links'))
            {{-- Giữ tham số lọc khi chuyển trang --}}
            {{ $perms->appends(request()->query())->links() }}
          @endif
        </div>
      </div>

    </div>
  </div>
</div>

{{-- JS: copy clipboard --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.js-copy').forEach(btn => {
    btn.addEventListener('click', async () => {
      const txt = btn.getAttribute('data-value') || '';
      try {
        await navigator.clipboard.writeText(txt);
        const old = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-clipboard-check"></i>';
        setTimeout(() => btn.innerHTML = old, 1200);
      } catch (e) {
        const ta = document.createElement('textarea');
        ta.value = txt;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
      }
    });
  });
});
</script>
@endsection
