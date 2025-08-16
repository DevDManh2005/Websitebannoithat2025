@php
use Illuminate\Support\Str;
$moduleLabels   = $moduleLabels   ?? [];
$actionLabels   = $actionLabels   ?? [];
$availablePerms = $availablePerms ?? [];
$selectedPair   = old('pair') ?? (isset($permission) ? ($permission->module_name.'|'.$permission->action) : '');
@endphp

<div class="mb-3">
  <label class="form-label">Chức năng <span class="text-danger">*</span></label>
  <select id="pair" name="pair" class="form-select @error('pair') is-invalid @enderror" required>
    <option value="">— Chọn chức năng —</option>
    @foreach($availablePerms as $module => $actions)
      @php $ml = $moduleLabels[$module] ?? Str::headline($module); @endphp
      <optgroup label="{{ $ml }}">
        @foreach($actions as $a)
          @php $al = $actionLabels[$a] ?? Str::headline(str_replace('_',' ', $a)); @endphp
          <option value="{{ $module }}|{{ $a }}" @selected($selectedPair === $module.'|'.$a)>
            {{ $al.' '.$ml }}
          </option>
        @endforeach
      </optgroup>
    @endforeach
  </select>
  @error('pair')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
  <div class="form-text">
    Ví dụ: <em>Thêm Sản phẩm, Xem Đơn hàng, Sẵn sàng giao Đơn hàng…</em>
  </div>
</div>

{{-- Hidden giữ tương thích (không bắt buộc dùng) --}}
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
