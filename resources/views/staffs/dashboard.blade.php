@extends('staffs.layouts.app')

@section('title','Bảng điều khiển')

@section('content')
@php
    $user = \Illuminate\Support\Facades\Auth::user();

    // Lấy danh sách quyền hiện có (gộp quyền từ role + gán trực tiếp)
    $rolePerms   = optional($user->role)->permissions ?? collect();
    $directPerms = method_exists($user, 'permissions') ? $user->permissions : collect();
    $allPerms    = $rolePerms->concat($directPerms)->unique('id');

    // Gợi ý các lối tắt hay dùng (chỉ hiển thị nếu có quyền view module tương ứng)
    $shortcuts = [
        ['name'=>'Đơn hàng',  'route'=>'staff.orders.index',   'module'=>'orders'],
        ['name'=>'Sản phẩm',  'route'=>'staff.products.index', 'module'=>'products'],
        ['name'=>'Đánh giá',  'route'=>'staff.reviews.index',  'module'=>'reviews'],
        ['name'=>'Voucher',   'route'=>'staff.vouchers.index', 'module'=>'vouchers'],
    ];
@endphp

<div class="row g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <div>
                    <h2 class="h5 mb-1">Xin chào, {{ $user?->name ?? 'Staff' }}</h2>
                    <div class="text-muted">Chúc bạn một ngày làm việc hiệu quả ✨</div>
                </div>
                <div class="mt-3 mt-md-0">
                    @foreach($shortcuts as $sc)
                        @perm($sc['module'],'view')
                            @php
                                $href = \Illuminate\Support\Facades\Route::has($sc['route'])
                                    ? route($sc['route'])
                                    : '#';
                            @endphp
                            <a href="{{ $href }}"
                               class="btn btn-outline-primary btn-sm me-1 mb-1"
                               @if($href==='#') title="Route chưa khai báo" @endif>
                                {{ $sc['name'] }}
                            </a>
                        @endperm
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Widget: đơn hàng mới nhất (placeholder – tuỳ bạn đổ dữ liệu sau) --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <div class="card-title mb-0">Đơn hàng mới nhất</div>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center text-muted py-5">
                <div class="text-center">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Chưa có đơn hàng nào.
                </div>
            </div>
        </div>
    </div>

    {{-- Widget: quyền hiện có --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <div class="card-title mb-0">Quyền hiện có</div>
            </div>
            <div class="card-body">
                @if($allPerms->isEmpty())
                    <div class="text-muted">Chưa được cấp quyền nào.</div>
                @else
                    @foreach($allPerms->sortBy(['module_name','action']) as $p)
                        <span class="chip">{{ $p->module_name.'.'.$p->action }}</span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection