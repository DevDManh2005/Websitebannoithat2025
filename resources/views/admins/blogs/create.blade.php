{{-- resources/views/admins/blogs/create.blade.php --}}
@extends('admins::layouts.app')


@section('title', 'Tạo bài viết')

@section('content')
<style>
  .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
  .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Tạo bài viết</h1>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" id="blogForm" novalidate>
    @csrf
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card card-soft">
          <div class="card-header"><strong>Nội dung</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
              <input type="text" name="title" id="titleInput" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Đường dẫn (slug)</label>
              <input type="text" name="slug" id="slugInput" class="form-control" value="{{ old('slug') }}">
              <small class="text-muted d-block mt-1">
                URL sẽ là: {{ rtrim(config('app.url'),'/') }}/bai-viet/<span id="slugPreview">{{ old('slug') }}</span>
              </small>
            </div>

            <div class="mb-3">
              <label class="form-label">Tóm tắt</label>
              <textarea name="excerpt" rows="4" class="form-control">{{ old('excerpt') }}</textarea>
            </div>

            <div class="mb-0">
              <label class="form-label">Nội dung <span class="text-danger">*</span></label>
              {{-- KHÔNG đặt required vì CKEditor sẽ ẩn phần tử này --}}
              <textarea name="content" id="editor" rows="10" class="form-control">{{ old('content') }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card card-soft mb-3">
          <div class="card-header"><strong>Phân loại</strong></div>
          <div class="card-body">
            <div class="mb-3 d-flex justify-content-between align-items-center">
              <label class="form-label mb-0">Danh mục</label>
              <a href="{{ route('admin.blog-categories.create') }}" class="small text-decoration-none">+ Thêm danh mục</a>
            </div>
            <select name="category_id" class="form-select">
              <option value="">— Không chọn —</option>
              @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
                  {{ $c->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="card card-soft mb-3">
          <div class="card-header"><strong>Ảnh đại diện</strong></div>
          <div class="card-body">
            <input type="file" name="thumbnail" id="thumbInput" accept="image/*" class="form-control">
            <img id="thumbPreview" class="img-fluid rounded mt-2 d-none" alt="preview">
          </div>
        </div>

        <div class="card card-soft">
          <div class="card-body d-flex align-items-center justify-content-between">
            <span>Xuất bản ngay</span>
            <div class="form-check form-switch m-0">
              <input class="form-check-input" type="checkbox" role="switch" id="publishedSwitch" name="is_published"
                     value="1" {{ old('is_published') ? 'checked' : '' }}>
            </div>
          </div>
          <div class="card-footer bg-white border-0 pt-0 d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i> Lưu bài viết
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
<script>
  // --- Helpers ---
  const slugify = (str) => (str || '')
    .toString()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .toLowerCase()
    .replace(/[^a-z0-9\- ]/g,' ')
    .trim().replace(/\s+/g,'-').replace(/\-+/g,'-');

  const $title = document.getElementById('titleInput');
  const $slug  = document.getElementById('slugInput');
  const $slugPrev = document.getElementById('slugPreview');
  const updateSlugPreview = () => $slugPrev.textContent = $slug.value || '';

  // Auto-slugify khi slug chưa bị user chạm
  $slug?.addEventListener('input', () => { $slug.dataset.touched = '1'; $slug.value = slugify($slug.value); updateSlugPreview(); });
  $title?.addEventListener('input', () => {
    if (!$slug.dataset.touched && !$slug.value) { $slug.value = slugify($title.value); updateSlugPreview(); }
  });
  updateSlugPreview();

  // Thumbnail preview
  const $thumbInput = document.getElementById('thumbInput');
  const $thumbPreview = document.getElementById('thumbPreview');
  $thumbInput?.addEventListener('change', e => {
    const f = e.target.files?.[0];
    if (!f) { $thumbPreview.classList.add('d-none'); $thumbPreview.src=''; return; }
    const reader = new FileReader();
    reader.onload = ev => { $thumbPreview.src = ev.target.result; $thumbPreview.classList.remove('d-none'); };
    reader.readAsDataURL(f);
  });

  // CKEditor + sync textarea before submit
  ClassicEditor.create(document.querySelector('#editor'), {
    simpleUpload: {
      uploadUrl: '{{ route('admin.uploads.ckeditor') }}',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }
  })
  .then(editor => {
    const form = document.getElementById('blogForm');
    const textarea = document.querySelector('textarea[name="content"]');
    textarea.removeAttribute('required');
    form.addEventListener('submit', function (e) {
      textarea.value = editor.getData().trim();
      if (!textarea.value) {
        e.preventDefault();
        alert('Vui lòng nhập nội dung bài viết.');
        editor.editing.view.focus();
        document.querySelector('.ck-editor')?.scrollIntoView({behavior:'smooth', block:'center'});
      }
    });
  })
  .catch(console.error);
</script>
@endpush
