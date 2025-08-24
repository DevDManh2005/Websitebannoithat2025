@extends('admins.layouts.app')

@section('title', 'Quản lý người dùng')

@push('styles')
<style>
  #users-index .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #users-index .status-pill{
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.22rem .55rem; border-radius:999px; font-weight:600; font-size:.82rem;
    border:1px solid rgba(23,26,31,.1); background:#f7f7fb;
  }
  #users-index .status-active  { background:#e7f6ed; border-color:#a7d6b8; }
  #users-index .status-locked  { background:#fde7e7; border-color:#f1b2b2; }
  #users-index .user-cell{
    display:flex; align-items:center; gap:.65rem; min-width:0;
  }
  #users-index .user-cell img{
    width:36px; height:36px; border-radius:50%; object-fit:cover;
    box-shadow:0 0 0 2px rgba(255,255,255,.85), 0 0 0 3px rgba(0,0,0,.05);
  }
  #users-index .user-meta{ min-width:0 }
  #users-index .user-meta .name{ font-weight:600; }
  #users-index .user-meta .email{ color:#6c757d; font-size:.875rem }
  #users-index .table tbody tr:hover{ background:#fffdf8 }
  #users-index .w-actions{ width: 260px }
  @media (max-width: 575.98px){
    #users-index .w-actions{ width: 200px }
  }
</style>
@endpush

@section('content')
<div id="users-index" class="container-fluid">

  {{-- Header + (tuỳ chọn) bộ lọc --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Quản lý người dùng</h1>
      <span class="text-muted small">Tổng: {{ number_format($users->total()) }}</span>
    </div>

    <form class="d-flex align-items-center gap-2" method="get" action="{{ route('admin.users.index') }}">
      <div class="input-group" style="min-width:280px; max-width:420px">
        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
        <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Tìm tên hoặc email…">
      </div>
      <select class="form-select" name="status" style="max-width:220px">
        <option value="">-- Tất cả trạng thái --</option>
        <option value="active" @selected(request('status')==='active')>Hoạt động</option>
        <option value="locked" @selected(request('status')==='locked')>Khóa</option>
      </select>
      <button class="btn btn-primary"><i class="bi bi-search me-1"></i><span class="d-none d-sm-inline">Tìm</span></button>
      @if(request()->hasAny(['q','status']))
        <a href="{{ route('admin.users.index') }}" class="btn btn-light"><i class="bi bi-x-lg me-1"></i>Xóa lọc</a>
      @endif
    </form>
  </div>

  {{-- Flash (nếu có) --}}
  @if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
  @endif

  {{-- List --}}
  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:90px">ID</th>
            <th>Người dùng</th>
            <th style="width:140px">Trạng thái</th>
            <th style="width:160px">Tỉnh/Thành</th>
            <th style="width:160px">Quận/Huyện</th>
            <th style="width:160px">Phường/Xã</th>
            <th class="text-end w-actions">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
            @php
              $avatar = optional($user->profile)->avatar ? asset('storage/'. $user->profile->avatar) : 'https://via.placeholder.com/64';
            @endphp
            <tr>
              <td class="fw-semibold">#{{ $user->id }}</td>
              <td>
                <div class="user-cell">
                  <img src="{{ $avatar }}" alt="avatar">
                  <div class="user-meta">
                    <div class="name text-truncate" title="{{ $user->name }}">{{ $user->name }}</div>
                    <div class="email text-truncate" title="{{ $user->email }}">{{ $user->email }}</div>
                  </div>
                </div>
              </td>
              <td>
                @if($user->is_active)
                  <span class="status-pill status-active"><i class="bi bi-check2-circle"></i> Hoạt động</span>
                @else
                  <span class="status-pill status-locked"><i class="bi bi-lock-fill"></i> Khóa</span>
                @endif
              </td>
              <td>{{ optional($user->profile)->province_name ?? '—' }}</td>
              <td>{{ optional($user->profile)->district_name ?? '—' }}</td>
              <td>{{ optional($user->profile)->ward_name ?? '—' }}</td>
              <td class="text-end">
                <div class="btn-group" role="group">
                  <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Xem">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="d-inline">
                    @csrf
                    @if($user->is_active)
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Khóa"
                              onclick="return confirm('Khóa tài khoản #{{ $user->id }}?')">
                        <i class="bi bi-shield-lock"></i>
                      </button>
                    @else
                      <button type="submit" class="btn btn-sm btn-outline-success" title="Mở khóa"
                              onclick="return confirm('Mở khóa tài khoản #{{ $user->id }}?')">
                        <i class="bi bi-unlock"></i>
                      </button>
                    @endif
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="card-body">
      {{ $users->withQueryString()->onEachSide(1)->links() }}
    </div>
  </div>
</div>
@endsection
