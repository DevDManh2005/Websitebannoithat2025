@php
use Illuminate\Support\Str;

/**
 * Controller có thể truyền vào các map sau để Việt hoá theo ý:
 *  - $moduleLabels   = ['orders'=>'Đơn hàng', 'products'=>'Sản phẩm', ...]
 *  - $actionLabels   = ['view'=>'Xem', 'create'=>'Thêm', ...]
 *  - $availablePerms = ['orders'=>['view','create',...], ...]
 * Dưới đây mình có default map tiếng Việt cho các key hay gặp.
 */
$moduleLabels   = (array)($moduleLabels   ?? []);
$actionLabels   = (array)($actionLabels   ?? []);
$availablePerms = (array)($availablePerms ?? []);

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

$selectedPair = old('pair') ?? (isset($permission) ? ($permission->module_name.'|'.$permission->action) : '');
@endphp

<div class="mb-3">
  <label class="form-label">Quyền chức năng <span class="text-danger">*</span></label>
  <select id="pair" name="pair" class="form-select @error('pair') is-invalid @enderror" required>
    <option value="">— Chọn quyền —</option>
    @foreach($availablePerms as $module => $actions)
      @php
        $ml = $M[$module] ?? Str::of($module)->replace(['_','-'],' ')->headline();
      @endphp
      <optgroup label="{{ $ml }}">
        @foreach((array)$actions as $a)
          @php
            $al = $A[$a] ?? Str::of($a)->replace('_',' ')->headline();
            $val = $module.'|'.$a;
          @endphp
          <option value="{{ $val }}" @selected($selectedPair === $val)>{{ $al.' '.$ml }}</option>
        @endforeach
      </optgroup>
    @endforeach
  </select>
  @error('pair')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
  <div class="form-text">
    Ví dụ: <em>Xem Đơn hàng, Thêm Sản phẩm, Sẵn sàng giao Đơn hàng…</em>
  </div>
</div>

{{-- Hidden để controller vẫn nhận 2 cột module_name & action --}}
<input type="hidden" name="module_name" id="module_name" value="">
<input type="hidden" name="action" id="action" value="">

<div class="d-flex justify-content-between align-items-center mt-3">
  <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Hủy</a>
  <button class="btn btn-primary">{{ isset($permission) ? 'Cập nhật' : 'Lưu' }}</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const sel = document.getElementById('pair');
  const mod = document.getElementById('module_name');
  const act = document.getElementById('action');
  function sync(){
    const v = sel.value || '';
    if(!v.includes('|')) { mod.value=''; act.value=''; return; }
    const parts = v.split('|');
    mod.value = parts[0] || '';
    act.value = parts[1] || '';
  }
  sel.addEventListener('change', sync);
  sync();
});
</script>
