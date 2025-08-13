<div class="mb-3">
  <label class="form-label">Module <span class="text-danger">*</span></label>
  <input type="text" name="module_name" class="form-control"
         value="{{ old('module_name', $permission->module_name ?? '') }}" required>
  <small class="text-muted">Ví dụ: orders, products, vouchers…</small>
</div>

<div class="mb-3">
  <label class="form-label">Action <span class="text-danger">*</span></label>
  <input type="text" name="action" class="form-control"
         value="{{ old('action', $permission->action ?? '') }}" required>
  <small class="text-muted">Ví dụ: view, create, update, delete…</small>
</div>

<div class="text-end">
  <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Hủy</a>
  <button class="btn btn-primary">{{ isset($permission) ? 'Cập nhật' : 'Lưu' }}</button>
</div>
