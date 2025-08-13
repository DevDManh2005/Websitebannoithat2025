@extends('admins.layouts.app') {{-- layout admin của bạn --}}
@section('title', 'Nhân viên')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Nhân viên</h1>
        <a href="{{ route('admin.staffs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Thêm nhân viên
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Quyền trực tiếp</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td><span class="badge text-bg-secondary">{{ optional($u->role)->name }}</span></td>
                            <td style="max-width:360px">
                                @php
                                    $chips = $u->directPermissions->map(fn($p) => $p->module_name . '.' . $p->action)->take(8)->implode(', ');
                                    $more = max(0, $u->directPermissions->count() - 8);
                                  @endphp
                                <div class="small text-truncate"
                                    title="{{ $u->directPermissions->map(fn($p) => $p->module_name . '.' . $p->action)->implode(', ') }}">
                                    {{ $chips }} @if($more > 0) và {{ $more }} quyền khác... @endif
                                </div>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.staffs.edit', $u->id) }}">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form class="d-inline" action="{{ route('admin.staffs.destroy', $u->id) }}" method="POST"
                                    onsubmit="return confirm('Xóa nhân viên này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có nhân viên.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $staffs->withQueryString()->links() }}
        </div>
    </div>
@endsection