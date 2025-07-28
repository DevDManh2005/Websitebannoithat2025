@extends('admins.layouts.app')

@section('content')
    <div class="container">
        <h1>Tạo nhân viên mới</h1>

        <form action="{{ route('admin.staffs.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Tên nhân viên</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            {{-- Phân quyền --}}
            @php
                $assigned = old('permissions', []);
            @endphp
            <div class="mb-3">
                <label class="form-label">Phân quyền</label>
                <select name="permissions[]" class="form-select" multiple>
                    @foreach($modules as $moduleName => $perms)
                        <optgroup label="{{ $moduleName }}">
                            @foreach($perms as $permission)
                                <option value="{{ $permission->id }}" {{ in_array($permission->id, $assigned) ? 'selected' : '' }}>
                                    {{ $permission->action }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">Tạo mới</button>
        </form>
    </div>
@endsection