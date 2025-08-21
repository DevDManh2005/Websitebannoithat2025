{{-- resources/views/admins/categories/_form.blade.php --}}
@php
    use Illuminate\Support\Str;
    $img = $category->image ?? null;
    $imgUrl = $img
        ? (Str::startsWith($img, ['http://','https://','//']) ? $img : asset('storage/'.$img))
        : null;
@endphp

<div class="row g-3">
  <div class="col-lg-8">
    <div class="mb-3">
      <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
      <input type="text" name="name" id="name"
             class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name', $category->name ?? '') }}" required>
      @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
      <label for="parent_id" class="form-label">Danh mục cha</label>
      <select name="parent_id" id="parent_id"
              class="form-select @error('parent_id') is-invalid @enderror">
        <option value="">— Là danh mục gốc —</option>
        @foreach($parents as $p)
          <option value="{{ $p->id }}"
            {{ (string)old('parent_id', $category->parent_id ?? '') === (string)$p->id ? 'selected' : '' }}>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
      @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
      <label for="position" class="form-label">Vị trí</label>
      <input type="number" name="position" id="position"
             class="form-control @error('position') is-invalid @enderror"
             value="{{ old('position', $category->position ?? 0) }}" min="0">
      @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="col-lg-4">
    <div class="mb-3">
      <label class="form-label">Hình ảnh</label>
      <input type="file" name="image" id="image"
             class="form-control @error('image') is-invalid @enderror"
             accept="image/*">
      @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

      <div class="mt-2">
        @if($imgUrl)
          <img id="catPreview" src="{{ $imgUrl }}" class="img-fluid rounded border" alt="preview">
        @else
          <img id="catPreview" class="img-fluid rounded border d-none" alt="preview">
        @endif
      </div>
    </div>

    <div class="form-check form-switch mb-3">
      <input type="hidden" name="is_active" value="0">
      <input class="form-check-input @error('is_active') is-invalid @enderror"
             type="checkbox" id="is_active" name="is_active" value="1"
             {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="is_active">Hiển thị</label>
      @error('is_active') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Preview ảnh
  document.getElementById('image')?.addEventListener('change', function(e){
    const f = e.target.files?.[0]; const img = document.getElementById('catPreview');
    if(!img) return;
    if(!f){ img.src=''; img.classList.add('d-none'); return; }
    const r = new FileReader();
    r.onload = ev => { img.src = ev.target.result; img.classList.remove('d-none'); };
    r.readAsDataURL(f);
  });
</script>
@endpush
