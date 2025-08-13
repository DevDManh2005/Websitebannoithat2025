@extends('admins.layouts.app')
@section('title','Sửa danh mục bài viết')

@section('content')
<form action="{{ route('admin.blog-categories.update',$category) }}" method="POST" enctype="multipart/form-data" class="row g-4">
  @csrf @method('PUT')
  <input type="hidden" name="id" value="{{ $category->id }}">

  <div class="col-lg-8">
    <div class="card p-3">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <div class="mb-3">
        <label class="form-label">Tên *</label>
        <input type="text" name="name" value="{{ old('name',$category->name) }}" class="form-control" oninput="syncSlug(this.value)" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="slug" value="{{ old('slug',$category->slug) }}" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" rows="4" class="form-control">{{ old('description',$category->description) }}</textarea>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card p-3">
      <div class="mb-3">
        <label class="form-label">Ảnh</label>
        <input type="file" name="thumbnail" class="form-control" accept="image/*" onchange="previewImage(this)">
        @if($category->thumbnail)
          <img id="thumbPreview" src="{{ asset('storage/'.$category->thumbnail) }}" class="img-fluid mt-2" alt="">
        @else
          <img id="thumbPreview" class="img-fluid mt-2 d-none" alt="">
        @endif
      </div>

      <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active',$category->is_active))>
        <label class="form-check-label" for="is_active">Kích hoạt</label>
      </div>

      <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Cập nhật</button>
    </div>
  </div>
</form>
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
  document.querySelector('input[name="slug"]').addEventListener('input', function(){ this.dataset.touched = '1'; });

  function previewImage(input){
    const img = document.getElementById('thumbPreview');
    if(input.files && input.files[0]){ img.src = URL.createObjectURL(input.files[0]); img.classList.remove('d-none'); }
    else { img.src = ''; img.classList.add('d-none'); }
  }
</script>
@endpush
