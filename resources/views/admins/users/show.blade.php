@extends('admins.layouts.app')

@section('title', 'Chi tiết Người dùng: ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết Người dùng: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Sửa
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            {{-- THÔNG TIN TÀI KHOẢN --}}
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Tài khoản</h5></div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="{{ optional($user->profile)->avatar_url ?? 'https://via.placeholder.com/150' }}" 
                             alt="Avatar" class="img-thumbnail rounded-circle" 
                             style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <p><strong>Tên:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Vai trò:</strong> <span class="badge bg-info">{{ ucfirst($user->role->name) }}</span></p>
                    <p><strong>Trạng thái:</strong> 
                        @if($user->is_active)
                            <span class="badge bg-success">Hoạt động</span>
                        @else
                            <span class="badge bg-danger">Khóa</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- THÔNG TIN HỒ SƠ --}}
            <div class="card shadow mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Hồ sơ chi tiết</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Ngày sinh:</dt>
                        <dd class="col-sm-8">{{ optional($user->profile)->dob ? \Carbon\Carbon::parse(optional($user->profile)->dob)->format('d/m/Y') : 'Chưa cập nhật' }}</dd>

                        <dt class="col-sm-4">Giới tính:</dt>
                        <dd class="col-sm-8">{{ optional($user->profile)->gender ?? 'Chưa cập nhật' }}</dd>

                        <dt class="col-sm-4">Địa chỉ:</dt>
                        <dd class="col-sm-8">
                            @if(optional($user->profile)->province_name)
                                {{ optional($user->profile)->ward_name }}, {{ optional($user->profile)->district_name }}, {{ optional($user->profile)->province_name }}
                            @else
                                Chưa cập nhật
                            @endif
                        </dd>

                        <dt class="col-sm-4">Địa chỉ chi tiết:</dt>
                        <dd class="col-sm-8">{{ optional($user->profile)->address ?? 'Chưa cập nhật' }}</dd>
                        
                        <dt class="col-sm-4">Ngày tham gia:</dt>
                        <dd class="col-sm-8">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>


        </div>
    </div>
</div>
@endsection