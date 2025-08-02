@extends('admins.layouts.app')

@section('title', 'Chi tiết Kho hàng')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventories.index') }}">Kho hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Chi tiết Kho hàng #{{ $inventory->id }}</h5></div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Sản phẩm:</dt>
                        <dd class="col-sm-8">{{ $inventory->product?->name ?? 'N/A' }}</dd>
                        <dt class="col-sm-4">Biến thể:</dt>
                        <dd class="col-sm-8">
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
                        </dd>
                        <dt class="col-sm-4">Số lượng hiện tại:</dt>
                        <dd class="col-sm-8"><h4><span class="badge bg-primary">{{ $inventory->quantity }}</span></h4></dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Vị trí Lưu trữ</h5></div>
                <div class="card-body">
                    {{-- ĐÃ ĐƯỢC ĐƠN GIẢN HÓA --}}
                    <dl class="row">
                         <dt class="col-sm-4">Địa chỉ kho:</dt>
                         <dd class="col-sm-8">{{ optional($inventory->location)->address ?? 'Chưa cập nhật' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Lịch sử Giao dịch</h5></div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-borderless">
                            <tbody>
                                @forelse($inventory->transactions->sortByDesc('created_at') as $transaction)
                                    <tr>
                                        <td>
                                            @if($transaction->type === 'import')
                                                <span class="badge bg-success">Nhập</span>
                                            @elseif($transaction->type === 'export')
                                                <span class="badge bg-danger">Xuất</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Điều chỉnh</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->type === 'import')
                                                <strong class="text-success">+{{ $transaction->quantity }}</strong>
                                            @else
                                                <strong class="text-danger">-{{ $transaction->quantity }}</strong>
                                            @endif
                                        </td>
                                        <td class="text-muted text-end">
                                            {{ $transaction->user?->name ?? 'Hệ thống' }}<br>
                                            <small>{{ $transaction->created_at->format('d/m/y H:i') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center text-muted">Chưa có giao dịch nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                 <div class="card-footer text-end">
                    <a href="{{ route('admin.inventories.edit', $inventory->id) }}" class="btn btn-warning">Chỉnh sửa Kho hàng</a>
                    <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection