@extends('admins.layouts.app')
@section('title', 'Sửa Voucher')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sửa Voucher: {{ $voucher->code }}</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.vouchers.update', $voucher) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admins.vouchers._form')
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </form>
        </div>
    </div>
</div>
@endsection