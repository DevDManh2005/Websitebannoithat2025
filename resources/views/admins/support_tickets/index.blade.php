@extends('admins.layouts.app')

@section('title','Hỗ trợ khách hàng')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h4 class="mb-0">Hỗ trợ khách hàng</h4>

  <form class="d-flex gap-2" method="get" action="{{ route('admin.support_tickets.index') }}">
    <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Tìm tiêu đề/nội dung">
    <select class="form-select" name="status" style="max-width: 200px">
      <option value="">-- Tất cả trạng thái --</option>
      @php $statuses=['open'=>'Mở','in_progress'=>'Đang xử lý','resolved'=>'Đã giải quyết','closed'=>'Đã đóng']; @endphp
      @foreach($statuses as $k=>$label)
        <option value="{{ $k }}" @selected(request('status')===$k)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn btn-primary">
      <i class="bi bi-search"></i>
      <span class="d-none d-sm-inline">Tìm</span>
    </button>
  </form>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
      <tr>
        <th style="width:80px">ID</th>
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
          <td>#{{ $t->id }}</td>
          <td>
            @if($t->user)
              <div class="fw-semibold">{{ $t->user->name }}</div>
              <div class="text-muted small">{{ $t->user->email }}</div>
            @else
              <div class="text-muted">N/A</div>
            @endif
          </td>
          <td class="text-truncate" style="max-width: 520px">
            <div class="fw-semibold">{{ $t->subject }}</div>
            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($t->message, 120) }}</div>
          </td>
          <td>
            @php $map = ['open'=>'primary','in_progress'=>'warning','resolved'=>'success','closed'=>'secondary']; @endphp
            <span class="badge text-bg-{{ $map[$t->status] ?? 'secondary' }}">{{ $t->status }}</span>
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
    {{ $tickets->onEachSide(1)->links() }}
  </div>
</div>
@endsection
