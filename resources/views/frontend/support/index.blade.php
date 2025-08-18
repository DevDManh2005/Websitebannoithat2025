@extends('layouts.app')

@section('title', 'Hỗ trợ của tôi')

@section('content')
  {{-- Banner --}}
  <div class="support-banner d-flex align-items-center justify-content-center text-white mb-5">
    <div class="container text-center" data-aos="fade-in">
      <h1 class="display-5">Hỗ Trợ Của Tôi</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center bg-transparent p-0 m-0">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
          <li class="breadcrumb-item active" aria-current="page">Hỗ trợ</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="container my-5">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert" data-aos="fade-up">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
      </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
      <h5 class="mb-0">Danh sách yêu cầu</h5>
      <a href="{{ route('support.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tạo yêu cầu mới
      </a>
    </div>

    <div class="row g-4">
      @forelse($tickets as $t)
        @php
          $map = ['open'=>'primary','in_progress'=>'warning','resolved'=>'success','closed'=>'secondary'];
        @endphp
        <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index*50 }}">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge text-bg-{{ $map[$t->status] ?? 'secondary' }}">{{ $t->status }}</span>
                <small class="text-muted">{{ $t->updated_at->diffForHumans() }}</small>
              </div>
              <h6 class="fw-semibold mb-2 text-truncate" title="{{ $t->subject }}">
                {{ $t->subject }}
              </h6>
              <div class="text-muted small mb-3">
                {{ \Illuminate\Support\Str::limit($t->message, 120) }}
              </div>
              <div class="mt-auto">
                <a href="{{ route('support.show', $t) }}" class="btn btn-outline-primary w-100">
                  Xem chi tiết
                </a>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12" data-aos="fade-up">
          <div class="text-center text-muted py-5">
            Chưa có yêu cầu nào. <a href="{{ route('support.create') }}">Tạo yêu cầu đầu tiên</a>.
          </div>
        </div>
      @endforelse
    </div>

    <div class="mt-4" data-aos="fade-up">
      {{ $tickets->onEachSide(1)->links() }}
    </div>
  </div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
  <style>
    .support-banner{
      height: 260px;
      background-image: linear-gradient(rgba(0,0,0,.45), rgba(0,0,0,.45)),
        url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop');
      background-size: cover; background-position: center;
    }
    .support-banner .breadcrumb-item a{ color:#f8f9fa; }
    .support-banner .breadcrumb-item.active{ color:#adb5bd; }

    .form-control:focus, .form-select:focus {
      border-color: #A20E38;
      box-shadow: 0 0 0 0.25rem rgba(162,14,56,.25);
    }
    .btn-primary{ background-color:#A20E38; border-color:#A20E38; }
    .btn-primary:hover{ background-color:#8b0c30; border-color:#8b0c30; }
    .btn-outline-primary{ color:#A20E38; border-color:#A20E38; }
    .btn-outline-primary:hover{ background-color:#A20E38; border-color:#A20E38; }
    a { color:#A20E38; }
    a:hover { color:#8b0c30; }
  </style>
@endpush

@push('scripts')
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>AOS.init({ once:true, duration:600, offset:80 });</script>
@endpush
