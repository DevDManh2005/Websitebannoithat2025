@csrf
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Route name <span class="text-danger">*</span></label>
    <select name="route_name" class="form-select" required>
      <option value="">-- Chọn route --</option>
      @foreach($routes as $name => $label)
        <option value="{{ $name }}" @selected(old('route_name', $rp->route_name ?? '') === $name)>{{ $label }}</option>
      @endforeach
    </select>
    <div class="form-text">Chỉ hiển thị các route <code>admin.*</code> và <code>staff.*</code>.</div>
  </div>

  <div class="col-md-3">
    <label class="form-label">Module <span class="text-danger">*</span></label>
    <input type="text" name="module_name" class="form-control"
           value="{{ old('module_name', $rp->module_name ?? '') }}" placeholder="vd: orders, products…" required>
  </div>

  <div class="col-md-3">
    <label class="form-label">Action <span class="text-danger">*</span></label>
    <input type="text" name="action" class="form-control"
           value="{{ old('action', $rp->action ?? '') }}" placeholder="vd: view, create, update, delete…" required>
  </div>
</div>

<div class="mt-3">
  <button class="btn btn-primary">Lưu</button>
  <a href="{{ route('admin.route-permissions.index') }}" class="btn btn-light">Hủy</a>
</div>
