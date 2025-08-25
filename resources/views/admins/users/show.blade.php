@extends('admins.layouts.app')

@section('title', 'Chi tiết Người dùng: ' . $user->name)

@push('styles')
<style>
  /* ===== User Show (scoped) ===== */
  #user-show .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #user-show .card-soft{
    border-radius:16px; border:1px solid rgba(32,25,21,.08);
    box-shadow:0 6px 22px rgba(18,38,63,.06); background:var(--card);
  }
  #user-show .card-soft .card-header{
    background:transparent; border-bottom:1px dashed rgba(32,25,21,.12);
  }
  #user-show .avatar{
    width:140px; height:140px; object-fit:cover; border-radius:50%;
    box-shadow:0 10px 24px rgba(0,0,0,.12);
    border:3px solid color-mix(in srgb, var(--brand,#C46F3B) 55%, white);
  }
  #user-show .chip{
    display:inline-block; padding:.22rem .6rem; border-radius:999px;
    background:#f2f4f7; border:1px solid rgba(23,26,31,.12);
    font-weight:600; font-size:.82rem; color:var(--text,#2B2623); white-space:nowrap;
  }
  #user-show .mono{ font:500 .95rem ui-monospace, Menlo, Consolas, "Courier New", monospace }
  #user-show .muted{ color:#6c757d }

  /* list đẹp hơn */
  #user-show dl{ margin-bottom:0 }
  #user-show dt{ color: var(--muted,#7d726c) }
  #user-show dd{ margin-bottom:.75rem }

  /* Nút nhỏ gọn cạnh email */
  #user-show .icon-btn{
    --size:30px; width:var(--size); height:var(--size);
    display:inline-grid; place-items:center; border-radius:8px;
    border:1px solid rgba(32,25,21,.12); background:#fff;
  }
  #user-show .icon-btn:hover{ background:#fff7f1; border-color: rgba(196,111,59,.35) }

  /* Nhãn trạng thái theo brand */
  #user-show .badge-status{ font-weight:700; letter-spacing:.2px }
</style>
@endpush

@section('content')
<div id="user-show" class="container-fluid">

  {{-- Bar tiêu đề --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Chi tiết Người dùng</h1>
      <span class="muted small">#{{ $user->id }}</span>
      <span class="chip">{{ $user->name }}</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">
        <i class="bi bi-pencil-square me-1"></i> Sửa
      </a>
      <a href="{{ route('admin.users.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left-short me-1"></i> Quay lại
      </a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-4">
      {{-- Tài khoản --}}
      <div class="card card-soft">
        <div class="card-header fw-semibold">Tài khoản</div>
        <div class="card-body">
          <div class="text-center mb-3">
            @php
              $avatar = optional($user->profile)->avatar_url
                        ?? (optional($user->profile)->avatar ? asset('storage/'.optional($user->profile)->avatar) : null);
            @endphp
            <img
              src="{{ $avatar ?: 'https://via.placeholder.com/150' }}"
              alt="Avatar"
              class="avatar"
            >
          </div>

          <div class="mb-2">
            <div class="muted small">Tên</div>
            <div class="fw-semibold">{{ $user->name }}</div>
          </div>

          <div class="mb-2">
            <div class="muted small d-flex align-items-center justify-content-between">
              <span>Email</span>
              <button class="icon-btn copy-email" type="button" data-email="{{ $user->email }}" title="Copy email">
                <i class="bi bi-clipboard"></i>
              </button>
            </div>
            <div class="mono">{{ $user->email }}</div>
          </div>

          <div class="mb-2">
            <div class="muted small">Vai trò</div>
            <div>
              <span class="badge text-bg-info">{{ ucfirst(optional($user->role)->name ?? 'user') }}</span>
            </div>
          </div>

          <div class="mb-0">
            <div class="muted small">Trạng thái</div>
            @if($user->is_active)
              <span class="badge text-bg-success badge-status">Hoạt động</span>
            @else
              <span class="badge text-bg-danger badge-status">Khóa</span>
            @endif
          </div>
        </div>
        <div class="card-footer d-flex justify-content-between small text-muted">
          <span>Tham gia</span>
          <time datetime="{{ $user->created_at->toIso8601String() }}">{{ $user->created_at->format('d/m/Y H:i') }}</time>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      {{-- Hồ sơ chi tiết --}}
      <div class="card card-soft mb-3">
        <div class="card-header fw-semibold">Hồ sơ chi tiết</div>
        <div class="card-body">
          <dl class="row">
            <dt class="col-sm-4">Ngày sinh</dt>
            <dd class="col-sm-8">
              {{ optional($user->profile)->dob ? \Carbon\Carbon::parse(optional($user->profile)->dob)->format('d/m/Y') : 'Chưa cập nhật' }}
            </dd>

            <dt class="col-sm-4">Giới tính</dt>
            <dd class="col-sm-8">{{ optional($user->profile)->gender ?? 'Chưa cập nhật' }}</dd>

            <dt class="col-sm-4">Địa chỉ</dt>
            <dd class="col-sm-8">
              @if(optional($user->profile)->province_name)
                <span class="chip">{{ optional($user->profile)->ward_name }}</span>
                <span class="chip">{{ optional($user->profile)->district_name }}</span>
                <span class="chip">{{ optional($user->profile)->province_name }}</span>
              @else
                Chưa cập nhật
              @endif
            </dd>

            <dt class="col-sm-4">Địa chỉ chi tiết</dt>
            <dd class="col-sm-8">
              {{ optional($user->profile)->address ?? 'Chưa cập nhật' }}
            </dd>
          </dl>
        </div>
      </div>

      {{-- Thẻ nhanh --}}
      <div class="card card-soft">
        <div class="card-header fw-semibold">Ghi chú</div>
        <div class="card-body">
          <div class="text-muted">
            Không có ghi chú thêm.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Copy email
  document.querySelectorAll('.copy-email').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      const email = btn.dataset.email || '';
      if(!email) return;
      try{
        await navigator.clipboard.writeText(email);
        btn.innerHTML = '<i class="bi bi-clipboard-check"></i>';
        setTimeout(()=> btn.innerHTML = '<i class="bi bi-clipboard"></i>', 1200);
      }catch(e){ console.error(e); }
    });
  });
</script>
@endpush
