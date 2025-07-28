@extends('admins.layouts.app')

@section('content')
    <h1 class="mb-4">Chỉnh sửa Thương hiệu: {{ $brand->name }}</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.brands.update', $brand) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Tên thương hiệu</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name', $brand->name) }}"
                   required>
        </div>

        <div class="form-group">
            <label>Logo hiện tại</label><br>
            @if($brand->logo_url)
                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" width="120">
            @else
                <p>Chưa có logo</p>
            @endif
        </div>

        <div class="form-group">
            <label for="logo_file">Logo (Upload file mới)</label>
            <input type="file"
                   name="logo_file"
                   id="logo_file"
                   class="form-control-file">
        </div>

        <div class="form-group">
            <label for="logo_url">Logo (URL mới)</label>
            <input type="url"
                   name="logo_url"
                   id="logo_url"
                   class="form-control"
                   value="{{ old('logo_url', filter_var($brand->logo, FILTER_VALIDATE_URL) ? $brand->logo : '') }}"
                   placeholder="https://example.com/logo.png">
            <small class="form-text text-muted">
                Nếu không upload file, bạn có thể nhập đường dẫn trực tiếp.
            </small>
        </div>

        <div class="form-group form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox"
                   name="is_active"
                   id="is_active"
                   class="form-check-input"
                   value="1"
                   {{ old('is_active', $brand->is_active) ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Kích hoạt</label>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection
