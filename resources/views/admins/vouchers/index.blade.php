@extends('admins.layouts.app')
@section('title', 'Quản lý Voucher')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-4 text-gray-800">Quản lý Voucher</h1>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary mb-4">Tạo mới</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Đã dùng</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $voucher)
                        <tr>
                            <td>{{ $voucher->code }}</td>
                            <td>{{ $voucher->type == 'fixed' ? 'Cố định' : 'Phần trăm' }}</td>
                            <td>{{ $voucher->type == 'fixed' ? number_format($voucher->value).' ₫' : $voucher->value.'%' }}</td>
                            <td>{{ $voucher->used_count }} / {{ $voucher->usage_limit ?? '∞' }}</td>
                            <td>
                                @if($voucher->is_active) <span class="badge bg-success">Hoạt động</span> @else <span class="badge bg-danger">Không hoạt động</span> @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-warning btn-sm">Sửa</a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">Chưa có voucher nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $vouchers->links() }}
        </div>
    </div>
</div>
@endsection