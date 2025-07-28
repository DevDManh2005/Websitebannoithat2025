@extends('admins.layouts.app')

@section('title', 'Chi tiết Người dùng')

@section('content')
<div class="container">
  <h1 class="mb-4">Chi tiết Người dùng #{{ $user->id }}</h1>

  <dl class="row">
    <dt class="col-sm-3">Tên</dt>
    <dd class="col-sm-9">{{ $user->name }}</dd>

    <dt class="col-sm-3">Email</dt>
    <dd class="col-sm-9">{{ $user->email }}</dd>

    <dt class="col-sm-3">Trạng thái</dt>
    <dd class="col-sm-9">
      @if($user->is_active)
        <span class="badge bg-success">Hoạt động</span>
      @else
        <span class="badge bg-danger">Khóa</span>
      @endif
    </dd>

    <dt class="col-sm-3">Ngày sinh</dt>
    <dd class="col-sm-9">{{ optional($user->profile)->dob ?? '—' }}</dd>

    <dt class="col-sm-3">Giới tính</dt>
    <dd class="col-sm-9">
      {{
        optional($user->profile)->gender === 'male'   ? 'Nam' :
        optional($user->profile)->gender === 'female' ? 'Nữ'  :
        optional($user->profile)->gender === 'other'  ? 'Khác': '—'
      }}
    </dd>

    <dt class="col-sm-3">Tỉnh/Thành</dt>
    <dd class="col-sm-9">{{ optional($user->profile)->province_name ?? '—' }}</dd>

    <dt class="col-sm-3">Quận/Huyện</dt>
    <dd class="col-sm-9">{{ optional($user->profile)->district_name ?? '—' }}</dd>

    <dt class="col-sm-3">Phường/Xã</dt>
    <dd class="col-sm-9">{{ optional($user->profile)->ward_name     ?? '—' }}</dd>

    <dt class="col-sm-3">Ghi chú / Tiểu sử</dt>
    <dd class="col-sm-9">{{ optional($user->profile)->bio ?? '—' }}</dd>
  </dl>

  <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
