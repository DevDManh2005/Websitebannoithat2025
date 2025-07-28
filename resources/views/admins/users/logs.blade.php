@extends('admins.layouts.app')

@section('title', 'Lịch sử hoạt động')

@section('content')
<div class="container">
  <h1 class="mb-4">Lịch sử hoạt động của {{ $user->name }}</h1>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Thời gian</th>
        <th>Hành động</th>
        <th>Mô-đun</th>
        <th>Mô tả</th>
        <th>IP</th>
      </tr>
    </thead>
    <tbody>
      @foreach($logs as $log)
        <tr>
          <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
          <td>{{ ucfirst($log->action) }}</td>
          <td>{{ $log->module }}</td>
          <td>{{ $log->description }}</td>
          <td>{{ $log->ip_address }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="d-flex justify-content-center">
    {{ $logs->links() }}
  </div>

  <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
