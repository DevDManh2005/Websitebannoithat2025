@extends('admins::layouts.app')


@section('title','Hỗ trợ khách hàng')

@push('styles')
<style>
  #tickets-index .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #tickets-index .status-pill{
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.25rem .55rem; border-radius:999px; font-weight:600; font-size:.82rem;
    border:1px solid rgba(23,26,31,.1); background:#f7f7fb;
  }
  #tickets-index .status-open{ background: color-mix(in srgb, var(--brand,#C46F3B) 12%, white); border-color: color-mix(in srgb, var(--brand,#C46F3B) 35%, #ddd) }
  #tickets-index .status-in_progress{ background:#fff5d8; border-color:#f1d08b }
  #tickets-index .status-resolved{ background:#e7f6ed; border-color:#a7d6b8 }
  #tickets-index .status-closed{ background:#eef0f3; border-color:#cfd4db; color:#5c6570 }
  #tickets-index .table tbody tr:hover{ background:#fffdf8 }
  #tickets-index .subject{ max-width: 640px }
</style>
@endpush

@section('content')
<div id="tickets-index" class="container-fluid">

  {{-- Header + filters --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Hỗ trợ khách hàng</h1>
      <span class="text-muted small">Tổng: {{ number_format($tickets->total()) }}</span>
    </div>

    <form class="d-flex align-items-center gap-2" method="get" action="{{ route('admin.support_tickets.index') }}">
      <div class="input-group" style="min-width:280px; max-width:420px">
        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
        <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Tìm tiêu đề hoặc nội dung…">
      </div>
      @php $statuses=[''=>'-- Tất cả trạng thái --','open'=>'Mở','in_progress'=>'Đang xử lý','resolved'=>'Đã giải quyết','closed'=>'Đã đóng']; @endphp
      <select class="form-select" name="status" style="max-width:240px">
        @foreach($statuses as $k=>$label)
          <option value="{{ $k }}" @selected((string)request('status')===(string)$k)>{{ $label }}</option>
        @endforeach
      </select>
      <button class="btn btn-primary"><i class="bi bi-search me-1"></i><span class="d-none d-sm-inline">Tìm</span></button>
      @if(request()->hasAny(['q','status']))
        <a href="{{ route('admin.support_tickets.index') }}" class="btn btn-light"><i class="bi bi-x-lg me-1"></i>Xóa lọc</a>
      @endif
    </form>
  </div>

  {{-- List --}}
  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
        <tr>
          <th style="width:90px">ID</th>
          <th style="width:260px">Khách</th>
          <th>Tiêu đề & nội dung</th>
          <th style="width:160px">Trạng thái</th>
          <th style="width:160px">Cập nhật</th>
          <th class="text-end" style="width:120px">Thao tác</th>
        </tr>
        </thead>
        <tbody>
        @forelse($tickets as $t)
          <tr>
            <td class="fw-semibold">#{{ $t->id }}</td>
            <td>
              @if($t->user)
                <div class="fw-semibold">{{ $t->user->name }}</div>
                <div class="text-muted small">{{ $t->user->email }}</div>
              @else
                <div class="text-muted">Khách lẻ</div>
              @endif
            </td>
            <td class="subject">
              <div class="fw-semibold text-truncate" title="{{ $t->subject }}">{{ $t->subject }}</div>
              <div class="text-muted small text-truncate" title="{{ $t->message }}">{{ \Illuminate\Support\Str::limit($t->message, 140) }}</div>
            </td>
            <td>
              @php
                $vn = ['open'=>'Mở','in_progress'=>'Đang xử lý','resolved'=>'Đã giải quyết','closed'=>'Đã đóng'];
                $class = 'status-'.($t->status ?? 'closed');
              @endphp
              <span class="status-pill {{ $class }}">
                @if($t->status==='open')<i class="bi bi-bell"></i>@endif
                @if($t->status==='in_progress')<i class="bi bi-hourglass-split"></i>@endif
                @if($t->status==='resolved')<i class="bi bi-check2-circle"></i>@endif
                @if($t->status==='closed')<i class="bi bi-lock"></i>@endif
                {{ $vn[$t->status] ?? ucfirst($t->status) }}
              </span>
            </td>
            <td class="text-muted">{{ $t->updated_at->diffForHumans() }}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.support_tickets.show',$t) }}">
                <i class="bi bi-eye"></i> Xem
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Không có vé hỗ trợ.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-body">
      {{ $tickets->withQueryString()->onEachSide(1)->links() }}
    </div>
  </div>
</div>
@endsection
