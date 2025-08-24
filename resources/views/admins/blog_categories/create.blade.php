{{-- resources/views/admins/blog_categories/create.blade.php --}}
@extends(auth()->user()->role->name === 'staff' ? 'staff.layouts.app' : 'admins.layouts.app')


@section('title','Thêm danh mục bài viết')

@section('content')
<style>
    .card-soft{ border-radius:16px; border:1px solid rgba(32,25,21,.08) }
    .card-soft .card-header{ background:transparent; border-bottom:1px dashed rgba(32,25,21,.12) }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 fw-bold mb-0">Thêm danh mục</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <form action="{{ route('admin.blog-categories.store') }}" method="POST" enctype="multipart/form-data" class="row g-4">
        @csrf
        <div class="col-lg-8">
            <div class="card card-soft">
                <div class="card-header"><strong>Thông tin cơ bản</strong></div>
                <div class="card-body p-3">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Tên *</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" oninput="syncSlug(this.value)" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="form-control @error('slug') is-invalid @enderror">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Nếu để trống sẽ tự sinh từ tên.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-soft">
                <div class="card-header"><strong>Cấu hình</strong></div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label">Danh mục cha</label>
                        <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— Không có —</option>
                            @foreach($parents as $p)
                                <option value="{{ $p->id }}" {{ (string)old('parent_id')===(string)$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thứ tự hiển thị</label>
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order',0) }}" class="form-control @error('sort_order') is-invalid @enderror">
                        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Số nhỏ hiển thị trước.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ảnh</label>
                        <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*" onchange="previewImage(this)">
                        @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <img id="thumbPreview" class="img-fluid mt-2 d-none" alt="Thumbnail">
                        <div class="form-text">Chấp nhận file ảnh tối đa 2MB.</div>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active',1))>
                        <label class="form-check-label" for="is_active">Kích hoạt</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i> Lưu
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
  function slugify(str){
    return str.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
      .replace(/[^a-z0-9\s-]/g,'')
      .trim().replace(/\s+/g,'-').replace(/-+/g,'-');
  }
  function syncSlug(val){
    const slugInput = document.querySelector('input[name="slug"]');
    if(!slugInput.dataset.touched){ slugInput.value = slugify(val); }
  }
  const slugField = document.querySelector('input[name="slug"]');
  if (slugField) slugField.addEventListener('input', function(){ this.dataset.touched = '1'; });

  function previewImage(input){
    const img = document.getElementById('thumbPreview');
    if(input.files && input.files[0]){
      img.src = URL.createObjectURL(input.files[0]);
      img.classList.remove('d-none');
    } else {
      img.src = '';
      img.classList.add('d-none');
    }
  }
</script>
@endpush
