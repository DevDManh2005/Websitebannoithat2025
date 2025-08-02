@extends('admins.layouts.app')

@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Danh sách sản phẩm</h5>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Thêm mới</a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá (Chính)</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td><a href="{{ route('admin.products.edit', $product->id) }}">{{ $product->name }}</a></td>
                                <td>
                                    {{-- Hiển thị nhiều danh mục --}}
                                    @foreach($product->categories as $category)
                                        <span class="badge bg-secondary">{{ $category->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @php $mainVariant = $product->variants->firstWhere('is_main_variant', true) ?? $product->variants->first(); @endphp
                                    @if($mainVariant)
                                        @if($mainVariant->sale_price > 0 && $mainVariant->sale_price < $mainVariant->price)
                                            <span class="text-danger">{{ number_format($mainVariant->sale_price) }} ₫</span>
                                            <small class="text-muted text-decoration-line-through">{{ number_format($mainVariant->price) }} ₫</small>
                                        @else
                                            <span>{{ number_format($mainVariant->price) }} ₫</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>@if ($product->is_active) <span class="badge bg-success">Hiển thị</span> @else <span class="badge bg-secondary">Ẩn</span> @endif</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">Xem</a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Không có sản phẩm nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
