@php
  use Illuminate\Support\Str;
@endphp

{{-- Hiển thị thanh tìm & nút chọn nhanh --}}
<div class="mb-3">
  <div class="input-group">
    <span class="input-group-text"><i class="bi bi-search"></i></span>
    <input id="perm-search" type="text" class="form-control" placeholder="Tìm quyền (vd: orders, view, update…)">
    <button type="button" class="btn btn-outline-secondary" id="btn-check-all">Chọn tất cả</button>
    <button type="button" class="btn btn-outline-secondary" id="btn-uncheck-all">Bỏ chọn tất cả</button>
  </div>
  <div class="form-text">
    Chỉ áp dụng cho quyền <strong>trực tiếp</strong> của nhân viên. Quyền từ vai trò (role) vẫn tự động có hiệu lực.
  </div>
</div>

@if(($modules ?? collect())->isEmpty())
  <div class="alert alert-warning">
    Chưa có quyền nào. @if(Route::has('admin.permissions.index'))
      Vào <a href="{{ route('admin.permissions.index') }}">Quyền</a> để tạo mới.
    @endif
  </div>
@else
  <div class="row g-3" id="perm-grid">
    @foreach($modules as $module => $perms)
      <div class="col-12 col-md-6 col-xl-4 perm-module" data-module="{{ $module }}">
        <div class="card h-100">
          <div class="card-header d-flex align-items-center justify-content-between py-2">
            <div class="fw-semibold text-uppercase small">{{ $module }}</div>
            <div class="form-check form-switch m-0">
              <input class="form-check-input perm-toggle-module" type="checkbox" id="toggle-{{ Str::slug($module) }}">
              <label class="form-check-label small" for="toggle-{{ Str::slug($module) }}">Chọn hết</label>
            </div>
          </div>
          <div class="card-body py-2">
            @foreach($perms as $p)
              <div class="form-check perm-item">
                <input class="form-check-input perm-checkbox"
                       type="checkbox"
                       name="permissions[]"
                       value="{{ $p->id }}"
                       id="perm-{{ $p->id }}"
                       @checked(in_array($p->id, old('permissions', $assigned ?? [])))>
                <label class="form-check-label" for="perm-{{ $p->id }}">
                  <span class="badge text-bg-light border">{{ $p->action }}</span>
                  <span class="text-muted small ms-1">({{ $module }}.{{ $p->action }})</span>
                </label>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

@push('scripts-page')
<script>
(function(){
  const grid   = document.getElementById('perm-grid');
  const search = document.getElementById('perm-search');
  const btnAll = document.getElementById('btn-check-all');
  const btnUn  = document.getElementById('btn-uncheck-all');

  function applyFilter(){
    if(!grid) return;
    const q = (search?.value || '').toLowerCase().trim();
    grid.querySelectorAll('.perm-module').forEach(mod => {
      let showModule = false;
      const moduleName = (mod.dataset.module || '').toLowerCase();
      mod.querySelectorAll('.perm-item').forEach(it => {
        const label = it.innerText.toLowerCase();
        const match = !q || label.includes(q) || moduleName.includes(q);
        it.style.display = match ? '' : 'none';
        if (match) showModule = true;
      });
      mod.style.display = showModule ? '' : 'none';
    });
  }
  search?.addEventListener('input', applyFilter);

  // Toggle từng module
  grid?.addEventListener('change', function(e){
    if (!e.target.classList.contains('perm-toggle-module')) return;
    const card = e.target.closest('.perm-module');
    card.querySelectorAll('.perm-checkbox:enabled').forEach(cb => cb.checked = e.target.checked);
  });

  // Check/uncheck all
  btnAll?.addEventListener('click', () => {
    grid?.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
    grid?.querySelectorAll('.perm-toggle-module').forEach(sw => sw.checked = true);
  });
  btnUn?.addEventListener('click', () => {
    grid?.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
    grid?.querySelectorAll('.perm-toggle-module').forEach(sw => sw.checked = false);
  });

  applyFilter();
})();
</script>
@endpush
