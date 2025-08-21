{{-- resources/views/admins/shared/flash.blade.php --}}
@php
  // Map loại thông báo → lớp Bootstrap + icon + nhãn (nếu cần)
  $flashMap = [
    'success' => ['class' => 'success', 'icon' => 'check-circle'],
    'error'   => ['class' => 'danger',  'icon' => 'x-circle'],
    'warning' => ['class' => 'warning', 'icon' => 'exclamation-triangle'],
    'info'    => ['class' => 'info',    'icon' => 'info-circle'],
    // Laravel hay dùng 'status' cho message thành công
    'status'  => ['class' => 'success', 'icon' => 'check-circle'],
  ];
@endphp

{{-- Session-based flashes --}}
@foreach ($flashMap as $key => $cfg)
  @if (session()->has($key))
    @php $msg = session($key); @endphp
    <div class="alert alert-{{ $cfg['class'] }} alert-dismissible fade show" role="alert">
      <i class="bi bi-{{ $cfg['icon'] }} me-2"></i>
      @if (is_array($msg))
        <ul class="mb-0 ps-3">
          @foreach ($msg as $line)
            <li>{{ $line }}</li>
          @endforeach
        </ul>
      @else
        {!! nl2br(e($msg)) !!}
      @endif
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
@endforeach

{{-- Validation errors --}}
@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-x-circle me-2"></i>
    <div class="fw-semibold mb-1">Vui lòng kiểm tra lại:</div>
    <ul class="mb-0 ps-3">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

{{-- Tự ẩn sau 5 giây (có thể đổi) --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.alert').forEach(function (el) {
      setTimeout(function () {
        try { bootstrap.Alert.getOrCreateInstance(el).close(); } catch (e) {}
      }, 5000);
    });
  });
</script>
