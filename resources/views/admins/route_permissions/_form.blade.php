@php
use Illuminate\Support\Str;

/**
 * Dữ liệu mong đợi từ Controller (đều có fallback an toàn):
 * - $routesByArea: [ 'admin' => ['admin.orders.index', ...], 'staff' => ['staff.orders.index', ...] ]
 * - $availablePerms: [ 'orders' => ['view','create','update','delete','ready_to_ship','cod_paid'], ... ]
 * - $moduleLabels: [ 'orders' => 'Đơn hàng', ... ]
 * - $actionLabels: [ 'view' => 'Xem', 'create' => 'Thêm', ... ]
 * - $routePermission (optional khi sửa)
 */
$routesByArea    = $routesByArea    ?? [];
$availablePerms  = $availablePerms  ?? [];
$moduleLabels    = $moduleLabels    ?? [];
$actionLabels    = $actionLabels    ?? [];

$selectedRoute   = old('route_name', $routePermission->route_name ?? '');
$selectedArea    = old('area', $routePermission->area ?? (Str::startsWith($selectedRoute, 'admin.') ? 'admin' : (Str::startsWith($selectedRoute, 'staff.') ? 'staff' : 'staff')));
$selectedPair    = old('pair') ?? (isset($routePermission) ? ($routePermission->module_name.'|'.$routePermission->action) : '');
$isActive        = old('is_active', isset($routePermission) ? (int)$routePermission->is_active : 1);

$pairHasError    = $errors->has('pair') || $errors->has('module_name') || $errors->has('action');
$routeHasError   = $errors->has('route_name');
$areaHasError    = $errors->has('area');

$areaName = fn($v) => $v === 'admin' ? 'Quản trị' : 'Nhân viên';
@endphp

<style>
  .help { color:#6c757d; font-size:.875rem }
</style>

{{-- TÊN TUYẾN (ROUTE) --}}
<div class="mb-3">
  <label class="form-label">Tên tuyến (route) <span class="text-danger">*</span></label>
  @php $hasPreset = count($routesByArea) > 0; @endphp

  @if($hasPreset)
    <select id="route_name" name="route_name" class="form-select {{ $routeHasError ? 'is-invalid' : '' }}" required>
      <option value="">— Chọn tuyến —</option>
      @foreach($routesByArea as $area => $routes)
        <optgroup label="{{ $areaName($area) }}">
          @foreach($routes as $r)
            <option value="{{ $r }}" @selected($selectedRoute === $r)>{{ $r }}</option>
          @endforeach
        </optgroup>
      @endforeach
    </select>
  @else
    <input type="text" id="route_name" name="route_name" class="form-control {{ $routeHasError ? 'is-invalid' : '' }}"
           value="{{ $selectedRoute }}" placeholder="Ví dụ: staff.orders.index" required>
  @endif

  @if($routeHasError)
    <div class="invalid-feedback d-block">{{ $errors->first('route_name') }}</div>
  @endif
  <div class="help">Bạn có thể chọn từ danh sách sẵn có hoặc nhập thủ công (ví dụ: <code>staff.orders.index</code>, <code>admin.products.update</code>…)</div>
</div>

{{-- KHU VỰC --}}
<div class="mb-3">
  <label class="form-label">Khu vực <span class="text-danger">*</span></label>
  <select id="area" name="area" class="form-select {{ $areaHasError ? 'is-invalid' : '' }}" required>
    <option value="staff" @selected($selectedArea==='staff')>Nhân viên</option>
    <option value="admin" @selected($selectedArea==='admin')>Quản trị</option>
  </select>
  @if($areaHasError)
    <div class="invalid-feedback d-block">{{ $errors->first('area') }}</div>
  @endif
</div>

{{-- CHỨC NĂNG (MODULE + HÀNH ĐỘNG) --}}
<div class="mb-3">
  <label class="form-label">Chức năng &amp; hành động <span class="text-danger">*</span></label>
  <select id="pair" name="pair" class="form-select {{ $pairHasError ? 'is-invalid' : '' }}" required>
    <option value="">— Chọn chức năng —</option>
    @foreach($availablePerms as $module => $actions)
      @php $ml = $moduleLabels[$module] ?? Str::headline($module); @endphp
      <optgroup label="{{ $ml }}">
        @foreach($actions as $a)
          @php $al = $actionLabels[$a] ?? Str::headline(str_replace('_',' ', $a)); @endphp
          <option value="{{ $module }}|{{ $a }}" @selected($selectedPair === $module.'|'.$a)>{{ $al.' '.$ml }}</option>
        @endforeach
      </optgroup>
    @endforeach
  </select>
  <div class="help">Ví dụ: <em>Thêm Sản phẩm</em>, <em>Xem Đơn hàng</em>, <em>Sẵn sàng giao Đơn hàng</em>…</div>

  @if($pairHasError)
    <div class="invalid-feedback d-block">
      {{ $errors->first('pair') ?? $errors->first('module_name') ?? $errors->first('action') }}
    </div>
  @endif
</div>

{{-- TRẠNG THÁI --}}
<div class="form-check form-switch mb-3">
  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked((int)$isActive === 1)>
  <label class="form-check-label" for="is_active">Kích hoạt</label>
</div>

{{-- Hidden để lưu xuống DB --}}
<input type="hidden" name="module_name" id="module_name" value="{{ old('module_name', $routePermission->module_name ?? '') }}">
<input type="hidden" name="action" id="action" value="{{ old('action', $routePermission->action ?? '') }}">

<div class="d-flex justify-content-between align-items-center mt-3">
  <a href="{{ route('admin.route-permissions.index') }}" class="btn btn-light">Hủy</a>
  <button class="btn btn-primary">{{ isset($routePermission) ? 'Cập nhật' : 'Lưu' }}</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const routeSel = document.getElementById('route_name');
  const areaSel  = document.getElementById('area');
  const pairSel  = document.getElementById('pair');
  const modInput = document.getElementById('module_name');
  const actInput = document.getElementById('action');

  function syncPairToHidden(){
    const v = pairSel.value || '';
    if(!v.includes('|')) { modInput.value=''; actInput.value=''; return; }
    const parts = v.split('|');
    modInput.value = parts[0] || '';
    actInput.value = parts[1] || '';
  }

  function guessActionFromRest(rest){
    const s = (rest || '').toLowerCase();
    if (['index','show'].includes(s)) return 'view';
    if (['create','store'].includes(s)) return 'create';
    if (['edit','update'].includes(s)) return 'update';
    if (['destroy','delete','remove'].includes(s)) return 'delete';
    if (s.includes('toggle') || s.includes('status') || s.includes('approve')) return 'update';
    if (s.includes('ready') || s.includes('ship')) return 'ready_to_ship';
    if (s.includes('cod') && (s.includes('paid') || s.includes('pay'))) return 'cod_paid';
    if (s.includes('moderate')) return 'moderate';
    if (s.includes('view') || s.includes('list') || s.includes('detail')) return 'view';
    return '';
  }

  function onRouteChanged(){
    const v = routeSel ? (routeSel.value || '') : '';
    if(!v) return;

    // đoán khu vực
    if (v.startsWith('admin.')) areaSel.value = 'admin';
    else if (v.startsWith('staff.')) areaSel.value = 'staff';

    // đoán module + action để preselect
    const parts = v.split('.');
    if (parts.length >= 3){
      const module = parts[1];
      const rest   = parts[2] || '';
      const guessed = guessActionFromRest(rest);

      if(module && guessed){
        const candidate = module + '|' + guessed;
        const opt = [...pairSel.options].find(o => o.value === candidate);
        if (opt) {
          pairSel.value = candidate;
          syncPairToHidden();
        }
      }
    }
  }

  pairSel && pairSel.addEventListener('change', syncPairToHidden);
  routeSel && routeSel.addEventListener('change', onRouteChanged);

  // Prefill khi sửa
  syncPairToHidden();
  onRouteChanged();
});
</script>
