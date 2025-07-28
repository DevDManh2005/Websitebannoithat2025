@extends('admins.layouts.app')

@section('title', 'Quản lý Kho hàng')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kho hàng</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Danh sách Kho hàng</h5>
            <a href="{{ route('admin.inventories.create') }}" class="btn btn-primary">Thêm mới</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sản phẩm</th>
                            <th>Biến thể</th>
                            <th>Số lượng</th>
                            <th>Vị trí</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventories as $inventory)
                            <tr>
                                <td>{{ $inventory->id }}</td>
                                <td>{{ $inventory->product?->name ?? 'N/A' }}</td>
                                <td>
                                    @if($inventory->variant)
                                        <strong>{{ $inventory->variant->sku }}</strong>
                                        @if(!empty($inventory->variant->attributes))
                                            @php
                                                $attributeParts = [];
                                                foreach($inventory->variant->attributes as $key => $value) {
                                                    $attributeParts[] = ucfirst($key) . ': ' . $value;
                                                }
                                            @endphp
                                            <br><small class="text-muted">({{ implode(', ', $attributeParts) }})</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Không áp dụng</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-info">{{ $inventory->quantity }}</span></td>
                                <td>
                                    @if($inventory->location)
                                        {{ $inventory->location->address ?: '' }}
                                        {{ $inventory->location->ward_name ? ', ' . $inventory->location->ward_name : '' }}
                                        {{ $inventory->location->district_name ? ', ' . $inventory->location->district_name : '' }}
                                        {{ $inventory->location->city_name ? ', ' . $inventory->location->city_name : '' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.inventories.show', $inventory->id) }}" class="btn btn-sm btn-info">Xem</a>
                                    <a href="{{ route('admin.inventories.edit', $inventory->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                    <form action="{{ route('admin.inventories.destroy', $inventory->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi này? Việc này cũng sẽ xóa vị trí liên kết.')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Chưa có bản ghi kho hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $inventories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection