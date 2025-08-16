{{-- Bridge: khi admin views chạy trong staff.*, dùng layout của staff --}}
@extends('staffs.layouts.app')

@section('content')
  @yield('content')
@endsection

@hasSection('title')
  @section('title') @yield('title') @endsection
@endif
