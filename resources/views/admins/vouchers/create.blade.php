@extends('admins.layouts.app')
@section('title', 'Tạo Voucher')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tạo Voucher mới</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                @include('admins.vouchers._form')
                <button type="submit" class="btn btn-primary">Tạo mới</button>
            </form>
        </div>
    </div>
</div>
@endsection