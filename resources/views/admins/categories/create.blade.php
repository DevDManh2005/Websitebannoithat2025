@extends('admins.layouts.app')

@section('content')
<h1>Tạo danh mục mới</h1>

<form action="{{ route('admin.categories.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name') }}" required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="parent_id" class="form-label">Danh mục cha</label>
        <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
            <option value="">— Chọn —</option>
            @foreach($parents as $p)
                <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                    {{ $p->name }}
                </option>
            @endforeach
        </select>
        @error('parent_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               class="form-check-input @error('is_active') is-invalid @enderror"
               {{ old('is_active', 1) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Hiển thị</label>
        @error('is_active')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="position" class="form-label">Vị trí</label>
        <input type="number" name="position" id="position"
               class="form-control @error('position') is-invalid @enderror"
               value="{{ old('position', 0) }}" min="0">
        @error('position')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button class="btn btn-success">Lưu</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
</form>
@endsection
