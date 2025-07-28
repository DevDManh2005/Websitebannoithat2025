@extends('admins.layouts.app')

@section('content')
    <h1 class="mb-4">Tạo Thương hiệu mới</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="name">Tên thương hiệu</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control"
                   value="{{ old('name') }}"
                   required>
        </div>

        <div class="form-group">
            <label for="logo_file">Logo (Upload file)</label>
            <input type="file"
                   name="logo_file"
                   id="logo_file"
                   class="form-control-file">
        </div>

        <div class="form-group">
            <label for="logo_url">Logo (URL)</label>
            <input type="url"
                   name="logo_url"
                   id="logo_url"
                   class="form-control"
                   value="{{ old('logo_url') }}"
                   placeholder="https://example.com/logo.png">
        </div>

        <div class="form-group form-check">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox"
                   name="is_active"
                   id="is_active"
                   class="form-check-input"
                   value="1"
                   {{ old('is_active', 1) ? 'checked' : '' }}>
            <label for="is_active" class="form-check-label">Kích hoạt</label>
        </div>

        <button type="submit" class="btn btn-success">Lưu lại</button>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
@endsection
