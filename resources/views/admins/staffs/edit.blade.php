@extends('admins.layouts.app')

@section('content')
<div class="container">
    <h1>Edit nhân viên</h1>

    <form action="{{ route('admin.staffs.update', $staff->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Name, Email giống trước --}}
        <div class="mb-3">
            <label class="form-label">Tên nhân viên</label>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $staff->name) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email', $staff->email) }}">
        </div>

        {{-- Phân quyền --}}
        <div class="mb-3">
            <label class="form-label">Phân quyền</label>
            <select name="permissions[]" class="form-select" multiple>
                @foreach($modules as $moduleName => $perms)
                    <optgroup label="{{ $moduleName }}">
                        @foreach($perms as $permission)
                            <option value="{{ $permission->id }}"
                                {{ in_array($permission->id, $assigned) ? 'selected' : '' }}>
                                {{ $permission->action }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection
