@extends('admins.layouts.app')

@section('title', 'Lịch sử hoạt động')

@push('styles')
<style>
  /* ===== Scoped styles cho trang User Logs ===== */
  #user-logs .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #user-logs .card-soft{
    border-radius:16px; border:1px solid rgba(32,25,21,.08);
    box-shadow:0 6px 22px rgba(18,38,63,.06); background:var(--card);
  }
  #user-logs .card-soft .card-header{
    background:transparent; border-bottom:1px dashed rgba(32,25,21,.12);
  }

  #user-logs .chip{
    display:inline-block; padding:.22rem .55rem; border-radius:999px;
    background:#f2f4f7; border:1px solid rgba(23,26,31,.12);
    font-weight:600; font-size:.82rem; color:var(--text,#2B2623);
    white-space:nowrap;
  }
  #user-logs .mono{ font:500 .92rem ui-monospace, Menlo, Consolas, "Courier New", monospace }

  /* Bảng đẹp hơn + header sticky khi cuộn */
  #user-logs .table-wrap{ max-height: 70vh; overflow:auto }
  #user-logs thead th{ position: sticky; top:0; z-index:1; background: #fffaf4; }
  #user-logs table thead th{ border-bottom:1px dashed rgba(32,25,21,.15) }
  #user-logs table tbody tr:hover{ background:#fffdf9 }
  #user-logs .w-actions{ width: 120px }
  #user-logs .w-time{ width: 180px }
  #user-logs .w-ip{ width: 140px }
</style>
@endpush

@section('content')
<div id="user-logs" class="container-fluid">
  {{-- Header bar --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Lịch sử hoạt động</h1>
      <span class="text-muted small">Người dùng: <strong>{{ $user->name }}</strong></span>
      <span class="chip">Tổng: {{ number_format($logs->total()) }}</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.users.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left-short me-1"></i> Quay lại
      </a>
    </div>
  </div>

  {{-- Card danh sách --}}
  <div class="card card-soft">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div class="fw-semibold">Nhật ký thao tác</div>
      <div class="text-muted small">Trang {{ $logs->currentPage() }} / {{ $logs->lastPage() }}</div>
    </div>

    <div class="table-wrap">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th class="w-time">Thời gian</th>
            <th class="w-actions">Hành động</th>
            <th style="width:220px">Mô-đun</th>
            <th>Mô tả</th>
            <th class="w-ip text-end">IP</th>
          </tr>
        </thead>
        <tbody>
          @php
            // map màu cho action
            $actMap = [
              'create' => 'success',
              'update' => 'warning',
              'delete' => 'danger',
              'view'   => 'secondary',
              'login'  => 'primary',
              'logout' => 'secondary',
            ];
          @endphp

          @forelse($logs as $log)
            @php
              $act = strtolower($log->action ?? '');
              $badge = $actMap[$act] ?? 'secondary';
              $timeTitle = $log->created_at?->toIso8601String();
            @endphp
            <tr>
              <td class="text-nowrap">
                <time datetime="{{ $timeTitle }}" data-bs-toggle="tooltip" title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                  {{ $log->created_at->diffForHumans() }}
                </time>
              </td>
              <td>
                <span class="badge text-bg-{{ $badge }}">{{ ucfirst($log->action) }}</span>
              </td>
              <td>
                <span class="chip">{{ $log->module }}</span>
              </td>
              <td>
                <div class="text-break">{{ $log->description }}</div>
              </td>
              <td class="text-end">
                <span class="mono me-2">{{ $log->ip_address }}</span>
                <button
                  type="button"
                  class="btn btn-sm btn-outline-secondary copy-ip"
                  data-ip="{{ $log->ip_address }}"
                  title="Copy IP"
                >
                  <i class="bi bi-clipboard"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                Không có lịch sử hoạt động.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-body">
      {{ $logs->onEachSide(1)->links() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // tooltips có sẵn từ layout; chỉ cần kích hoạt nếu chưa
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    try { new bootstrap.Tooltip(el); } catch(e) {}
  });

  // Copy IP
  document.querySelectorAll('.copy-ip').forEach(btn => {
    btn.addEventListener('click', async () => {
      const ip = btn.dataset.ip || '';
      if (!ip) return;
      try {
        await navigator.clipboard.writeText(ip);
        btn.innerHTML = '<i class="bi bi-clipboard-check"></i>';
        setTimeout(() => btn.innerHTML = '<i class="bi bi-clipboard"></i>', 1200);
      } catch (e) {
        console.error(e);
      }
    });
  });
</script>
@endpush
