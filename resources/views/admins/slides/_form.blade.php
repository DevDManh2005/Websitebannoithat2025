{{-- resources/views/admins/slides/_form.blade.php --}}
<style>
  #slide-form .thumb{
    width:100%; aspect-ratio:16/9;
    background:#f6f6f6; border:1px solid rgba(0,0,0,.06);
    border-radius:12px; overflow:hidden; display:grid; place-items:center;
  }
  #slide-form .thumb img{ width:100%; height:100%; object-fit:cover }
  #slide-form .card-soft{
    border-radius:16px; border:1px solid rgba(32,25,21,.08); background:var(--card);
    box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #slide-form .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div id="slide-form" class="row g-4">

  {{-- Cột trái: Nội dung --}}
  <div class="col-lg-8">
    <div class="card card-soft">
      <div class="card-header"><strong>Nội dung slide</strong></div>
      <div class="card-body">

        <div class="mb-3">
          <label for="title" class="form-label">Tiêu đề chính <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="title" name="title"
                 value="{{ old('title', $slide->title ?? '') }}" required>
        </div>

        <div class="mb-3">
          <label for="subtitle" class="form-label">Tiêu đề phụ / Mô tả ngắn</label>
          <textarea class="form-control" id="subtitle" name="subtitle" rows="2"
                    placeholder="Một câu mô tả ngắn gọn…">{{ old('subtitle', $slide->subtitle ?? '') }}</textarea>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label for="button_text" class="form-label">Chữ trên nút bấm</label>
            <input type="text" class="form-control" id="button_text" name="button_text"
                   value="{{ old('button_text', $slide->button_text ?? '') }}" placeholder="VD: Khám phá ngay">
          </div>
          <div class="col-md-6">
            <label for="button_link" class="form-label">Link của nút bấm</label>
            <input type="url" class="form-control" id="button_link" name="button_link"
                   value="{{ old('button_link', $slide->button_link ?? '') }}" placeholder="https://example.com/...">
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- Cột phải: Ảnh & hiển thị --}}
  <div class="col-lg-4">
    <div class="card card-soft mb-3">
      <div class="card-header"><strong>Ảnh & hiển thị</strong></div>
      <div class="card-body">

        {{-- Preview ảnh --}}
        <div class="thumb mb-3" id="previewBox">
          @if(!empty($slide->image))
            <img id="previewImg" src="{{ asset('storage/' . $slide->image) }}" alt="Preview">
          @else
            <img id="previewImg" src="" alt="Preview" style="display:none">
            <span class="text-muted small">Chưa có ảnh</span>
          @endif
        </div>

        <div class="mb-2">
          <label for="image" class="form-label">Ảnh slide {{ empty($slide->image) ? ' (bắt buộc)' : '' }}</label>
          <input type="file" class="form-control" id="image" name="image"
                 {{ empty($slide->image) ? 'required' : '' }} accept="image/*">
          <div class="form-text">Khuyến nghị 1920×700px, ≤ 2MB.</div>
        </div>

        @if(!empty($slide->image))
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
            <label class="form-check-label" for="remove_image">
              Xoá ảnh hiện tại
            </label>
          </div>
        @endif

        <div class="row g-3">
          <div class="col-6">
            <label for="position" class="form-label">Vị trí</label>
            <input type="number" min="0" class="form-control" id="position" name="position"
                   value="{{ old('position', $slide->position ?? 0) }}" required>
            <div class="form-text">Số nhỏ hiển thị trước.</div>
          </div>

          <div class="col-6">
            <label class="form-label d-block">Kích hoạt</label>
            {{-- Gửi 0 khi unchecked --}}
            <input type="hidden" name="is_active" value="0">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                     {{ old('is_active', $slide->is_active ?? true) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">Hiển thị trên trang chủ</label>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- Gợi ý nhỏ --}}
    <div class="small text-muted">
      Mẹo: Dùng ảnh nhẹ (WebP), tiêu đề ngắn gọn, đặt vị trí theo thứ tự mong muốn.
    </div>
  </div>
</div>

{{-- JS: Preview ảnh live --}}
<script>
document.addEventListener('DOMContentLoaded', function(){
  const fileInput = document.getElementById('image');
  const img = document.getElementById('previewImg');
  const box = document.getElementById('previewBox');

  if (fileInput) {
    fileInput.addEventListener('change', () => {
      const file = fileInput.files && fileInput.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => {
        img.src = e.target.result;
        img.style.display = 'block';
        const text = box.querySelector('span');
        if (text) text.remove();
      };
      reader.readAsDataURL(file);
    });
  }
});
</script>
