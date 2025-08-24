@extends(auth()->user()->role->name === 'staff' ? 'staffs.layouts.app' : 'admins.layouts.app')

@section('title', 'Sửa bài viết')

@push('styles')
<style>
  .card-soft {
      background: linear-gradient(180deg, rgba(234,223,206,.25), transparent), var(--card);
      border: 0;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
  }

  .card-soft .card-header {
      background: transparent;
      border-bottom: 1px dashed rgba(32,25,21,.1);
      font-weight: 600;
      color: var(--text);
  }

  .alert-success {
      background: #e5f7ed;
      color: #1e6b3a;
      border-radius: 12px;
      border: 1px solid rgba(24,121,78,.15);
      animation: fadeIn 0.5s ease;
  }

  .alert-danger {
      background: #fde7e7;
      color: #992f2f;
      border-radius: 12px;
      border: 1px solid rgba(153,47,47,.25);
      animation: fadeIn 0.5s ease;
  }

  #thumbPreview {
      opacity: 0;
      transition: opacity 0.3s ease;
  }

  #thumbPreview.show {
      opacity: 1;
  }

  .form-select {
      border-radius: 10px;
      border: 1px solid rgba(196,111,59,.25);
      background: #fff6ed;
      transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
  }

  .form-select:focus {
      border-color: var(--brand);
      box-shadow: 0 0 0 3px var(--ring);
      transform: translateY(-1px);
  }

  .form-select option {
      padding-left: calc(10px * var(--level, 0));
  }

  .btn-primary {
      background: var(--brand);
      border-color: var(--brand);
  }

  .btn-primary:hover {
      background: var(--brand-600);
      border-color: var(--brand-600);
      transform: translateY(-1px);
  }

  .btn-outline-secondary {
      color: var(--brand);
      border-color: var(--brand);
  }

  .btn-outline-secondary:hover {
      background: var(--brand);
      color: #fff;
      transform: translateY(-1px);
  }

  .btn.ripple {
      position: relative;
      overflow: hidden;
  }

  .btn.ripple:after {
      content: '';
      position: absolute;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,.35);
      transform: translate(-50%,-50%);
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.6s, width 0.6s, height 0.6s;
  }

  .btn.ripple:active:after {
      width: 100px;
      height: 100px;
      opacity: 0.6;
  }

  /* Responsive */
  @media (max-width: 767.98px) {
      .h5 {
          font-size: 1.1rem;
      }

      .card {
          border-radius: 12px;
      }

      .btn {
          font-size: 0.9rem;
          padding: 0.5rem 1rem;
      }

      .alert-success, .alert-danger {
          font-size: 0.9rem;
          padding: 0.75rem;
      }
  }

  @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
  }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush

@section('content')
@php
    use Illuminate\Support\Str;
    $thumb = $blog->thumbnail ?? null;
    $thumbUrl = $thumb
        ? (Str::startsWith($thumb, ['http://','https://','//']) ? $thumb : \Illuminate\Support\Facades\Storage::url($thumb))
        : null;
    // Hàm đệ quy để hiển thị danh mục dạng cây
    function renderCategoryOptions($categories, $level = 0, $prefix = '') {
        $output = '';
        foreach ($categories as $cat) {
            $indent = str_repeat('—', $level) . ($level > 0 ? ' ' : '');
            $selected = in_array($cat->id, old('category_ids', $cat->categories->pluck('id')->toArray())) ? 'selected' : '';
            $output .= '<option value="' . $cat->id . '" ' . $selected . '>' . $prefix . $indent . $cat->name . '</option>';
            if ($cat->children && $cat->children->isNotEmpty()) {
                $output .= renderCategoryOptions($cat->children, $level + 1, $prefix);
            }
        }
        return $output;
    }
@endphp

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0 fw-bold">Sửa bài viết</h1>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary ripple">
      <i class="bi bi-arrow-left"></i> Quay lại
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" id="blogForm" novalidate>
    @csrf @method('PUT')
    <input type="hidden" name="id" value="{{ $blog->id }}">

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card card-soft">
          <div class="card-header"><strong>Nội dung</strong></div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
              <input type="text" name="title" id="titleInput" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $blog->title) }}" required>
              @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Đường dẫn (slug)</label>
              <input type="text" name="slug" id="slugInput" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $blog->slug) }}">
              <small class="text-muted d-block mt-1">
                URL sẽ là: {{ rtrim(config('app.url'),'/') }}/bai-viet/<span id="slugPreview">{{ old('slug', $blog->slug) }}</span>
              </small>
              @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Tóm tắt</label>
              <textarea name="excerpt" rows="4" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $blog->excerpt) }}</textarea>
              @error('excerpt') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-0">
              <label class="form-label">Nội dung <span class="text-danger">*</span></label>
              <textarea name="content" id="editor" rows="10" class="form-control @error('content') is-invalid @enderror">{{ old('content', $blog->content) }}</textarea>
              @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
            <select name="category_ids[]" multiple class="form-select @error('category_ids') is-invalid @enderror" id="categorySelect">
              {!! renderCategoryOptions($categories, 0, '') !!}
            </select>
            @error('category_ids') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="card card-soft mb-3">
          <div class="card-header"><strong>Ảnh đại diện</strong></div>
          <div class="card-body">
            <input type="file" name="thumbnail" id="thumbInput" accept="image/*" class="form-control @error('thumbnail') is-invalid @enderror">
            @error('thumbnail') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <img id="thumbPreview" class="img-fluid rounded mt-2 {{ $thumbUrl ? 'show' : 'd-none' }}" src="{{ $thumbUrl ?: '' }}" alt="preview">
          </div>
        </div>

        <div class="card card-soft">
          <div class="card-body d-flex align-items-center justify-content-between">
            <span>Xuất bản ngay</span>
            <div class="form-check form-switch m-0">
              <input class="form-check-input @error('is_published') is-invalid @enderror" type="checkbox" role="switch" id="publishedSwitch" name="is_published"
                     value="1" {{ old('is_published', $blog->is_published) ? 'checked' : '' }}>
              @error('is_published') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="card-footer bg-white border-0 pt-0 d-grid">
            <button type="submit" class="btn btn-primary ripple" id="submitBtn">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

  // Chỉ auto-slug nếu người dùng chưa chỉnh slug
  $slug?.addEventListener('input', () => { $slug.dataset.touched = '1'; $slug.value = slugify($slug.value); updateSlugPreview(); });
  $title?.addEventListener('input', () => {
    if (!$slug.dataset.touched && !$slug.value) { $slug.value = slugify($title.value); updateSlugPreview(); }
  });
  updateSlugPreview();

  // Thumb preview với fade-in
  const $thumbInput = document.getElementById('thumbInput');
  const $thumbPreview = document.getElementById('thumbPreview');
  $thumbInput?.addEventListener('change', e => {
    const f = e.target.files?.[0];
    if (!f) { return; }
    const reader = new FileReader();
    reader.onload = ev => { $thumbPreview.src = ev.target.result; $thumbPreview.classList.add('show'); $thumbPreview.classList.remove('d-none'); };
    reader.readAsDataURL(f);
  });

  // Select2 cho multiple select danh mục
  $(document).ready(function() {
    $('#categorySelect').select2({
      placeholder: '— Chọn danh mục —',
      allowClear: true,
      closeOnSelect: false // Giữ mở khi chọn multiple
    });
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

  // Ripple cho nút
  document.querySelectorAll('.ripple').forEach(el => {
    el.addEventListener('click', function(e) {
      const btn = e.currentTarget;
      const rect = btn.getBoundingClientRect();
      const circle = document.createElement('span');
      const d = Math.max(rect.width, rect.height);
      Object.assign(circle.style, {
        width: d + 'px',
        height: d + 'px',
        position: 'absolute',
        left: (e.clientX - rect.left) + 'px',
        top: (e.clientY - rect.top) + 'px',
        transform: 'translate(-50%, -50%)',
        background: 'rgba(255,255,255,.35)',
        borderRadius: '50%',
        pointerEvents: 'none',
        opacity: '0.6',
        transition: 'opacity .6s, width .6s, height .6s'
      });
      btn.appendChild(circle);
      requestAnimationFrame(() => {
        circle.style.width = circle.style.height = (d * 1.8) + 'px';
        circle.style.opacity = '0';
      });
      setTimeout(() => circle.remove(), 600);
    });
  });
</script>
@endpush
@endsection