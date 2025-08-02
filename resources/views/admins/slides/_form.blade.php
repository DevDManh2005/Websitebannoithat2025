<div class="mb-3">
    <label for="title" class="form-label">Tiêu đề chính <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $slide->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label for="subtitle" class="form-label">Tiêu đề phụ / Mô tả ngắn</label>
    <textarea class="form-control" id="subtitle" name="subtitle" rows="2">{{ old('subtitle', $slide->subtitle ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label for="image" class="form-label">Ảnh slide <span class="text-danger">*</span></label>
    @if(isset($slide) && $slide->image)
        <img src="{{ asset('storage/' . $slide->image) }}" alt="Slide image" class="img-thumbnail d-block mb-2" width="200">
    @endif
    <input type="file" class="form-control" id="image" name="image" {{ isset($slide) && $slide->image ? '' : 'required' }} accept="image/*">
    <small class="form-text">Nên dùng ảnh có kích thước 1920x700px.</small>
</div>
<hr>
<div class="mb-3">
    <label for="button_text" class="form-label">Chữ trên nút bấm</label>
    <input type="text" class="form-control" id="button_text" name="button_text" value="{{ old('button_text', $slide->button_text ?? '') }}" placeholder="Ví dụ: Khám phá ngay">
</div>
<div class="mb-3">
    <label for="button_link" class="form-label">Link của nút bấm</label>
    <input type="url" class="form-control" id="button_link" name="button_link" value="{{ old('button_link', $slide->button_link ?? '') }}" placeholder="https://example.com/san-pham">
</div>
<hr>
<div class="mb-3">
    <label for="position" class="form-label">Vị trí (Số nhỏ hơn sẽ hiển thị trước)</label>
    <input type="number" class="form-control" id="position" name="position" value="{{ old('position', $slide->position ?? 0) }}" required>
</div>
<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $slide->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Kích hoạt</label>
</div>
