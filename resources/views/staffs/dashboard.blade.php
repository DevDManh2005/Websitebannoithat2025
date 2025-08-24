@extends('staffs.layouts.app')

@section('title','Bảng điều khiển')

@section('content')
<div class="row g-3">
    {{-- Header + shortcuts --}}
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
                                $href = \Illuminate\Support\Facades\Route::has($sc['route'] ?? '')
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

    {{-- Widgets (mỗi module 1 card) --}}
    @forelse($widgets as $key => $w)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div class="card-title mb-0">
                        <i class="{{ $w['icon'] }}"></i>
                        {{ $w['title'] }}
                    </div>
                    @if(!empty($w['route']) && \Illuminate\Support\Facades\Route::has($w['route']))
                        <a href="{{ route($w['route']) }}" class="btn btn-sm btn-light">Xem tất cả</a>
                    @endif
                </div>
                <div class="card-body">
                    {{-- Stats --}}
                    @if(!empty($w['stats']))
                        <div class="row g-2 mb-3">
                            @foreach($w['stats'] as $label => $value)
                                <div class="col-6 col-md-6">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted small">{{ $label }}</div>
                                        <div class="fw-semibold fs-5">{{ is_numeric($value) ? number_format($value,0,',','.') : $value }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Recent table --}}
                    @php $items = collect($w['recent'] ?? []); @endphp
                    @if($items->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Chưa có dữ liệu.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                <tr>
                                    @foreach(array_keys($items->first()) as $col)
                                        <th>{{ $col }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $row)
                                    <tr>
                                        @foreach($row as $val)
                                            <td>{{ $val }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">Bạn chưa được cấp quyền xem bất kỳ module nào.</div>
        </div>
    @endforelse

    {{-- Quyền hiện có --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="card-title mb-0">Quyền hiện có</div>
            </div>
            <div class="card-body">
                @if($allPerms->isEmpty())
                    <div class="text-muted">Chưa được cấp quyền nào.</div>
                @else
                    @foreach($allPerms as $p)
                        <span class="badge bg-primary-soft me-1 mb-1">{{ $p->module_name.'.'.$p->action }}</span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
