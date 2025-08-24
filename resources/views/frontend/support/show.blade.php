@extends('layouts.app')

@section('title', 'Vé #' . $ticket->id)

@section('content')
    {{-- Banner --}}
    <div class="support-banner d-flex align-items-center justify-content-center text-white mb-5">
        <div class="container text-center" data-aos="fade-in">
            <h1 class="display-5">Chi Tiết Yêu Cầu</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('support.index') }}">Hỗ trợ</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Vé #{{ $ticket->id }}</li>
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
        @if ($errors->any())
            <div class="alert alert-danger" data-aos="fade-up">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $map = [
                'open' => 'info',
                'in_progress' => 'brand',
                'resolved' => 'success',
                'closed' => 'muted',
            ];
            $labelMap = [
                'open' => 'Đang mở',
                'in_progress' => 'Đang xử lý',
                'resolved' => 'Đã giải quyết',
                'closed' => 'Đã đóng'
            ];
            $locked = in_array($ticket->status, ['resolved','closed'], true);
        @endphp

        <div class="row g-4">
            {{-- Nội dung + Trao đổi --}}
            <div class="col-lg-8" data-aos="fade-right">
                <div class="card card-glass mb-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold text-brand">Vé #{{ $ticket->id }} — {{ $ticket->subject }}</h5>
                            <small class="text-muted">Tạo lúc {{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <span class="badge badge-soft-{{ $map[$ticket->status] ?? 'secondary' }}">{{ $labelMap[$ticket->status] ?? $ticket->status }}</span>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="white-space:pre-wrap">{{ $ticket->message }}</p>
                    </div>
                </div>

                <div class="card card-glass" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-brand">Trao đổi</h6>
                    </div>
                    <div class="card-body">
                        @forelse($ticket->replies as $r)
                            @php
                                $attachmentUrl = $r->attachment ? asset('storage/'.$r->attachment) : null;
                                $ext = $r->attachment ? \Illuminate\Support\Str::lower(pathinfo($r->attachment, PATHINFO_EXTENSION)) : null;
                            @endphp

                            <div class="mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <div class="fw-semibold">
                                        {{ $r->user_id === auth()->id() ? 'Bạn' : ($r->user?->name ?? 'Người dùng') }}
                                        <span class="text-muted small">
                                            {{ $r->user_id === auth()->id() ? '' : '(' . ($r->user?->email ?? 'n/a') . ')' }}
                                        </span>
                                    </div>
                                    <div class="text-muted small">{{ $r->created_at->format('d/m/Y H:i') }}</div>
                                </div>

                                @if($r->message)
                                    <div class="mt-2" style="white-space:pre-wrap">{{ $r->message }}</div>
                                @endif

                                @if($attachmentUrl)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-brand"
                                            data-bs-toggle="modal"
                                            data-bs-target="#previewModal"
                                            data-preview-url="{{ $attachmentUrl }}"
                                            data-preview-ext="{{ $ext }}"
                                        >
                                            <i class="bi bi-paperclip"></i> Xem tệp đính kèm
                                        </button>
                                        <a class="btn btn-sm btn-outline-brand" href="{{ $attachmentUrl }}" target="_blank" download>
                                            <i class="bi bi-download"></i> Tải về
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-muted">Chưa có phản hồi nào.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Cột phải: Form phản hồi / Thông báo khoá --}}
            <div class="col-lg-4" data-aos="fade-left">
                @if(!$locked)
                    <div class="card card-glass">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold text-brand">Gửi phản hồi</h6>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('support.reply', $ticket) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Nội dung</label>
                                    <textarea name="message" rows="5" class="form-control form-control-modern @error('message') is-invalid @enderror"
                                                placeholder="Nhập tin nhắn của bạn...">{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Đính kèm (tuỳ chọn)</label>
                                    <input type="file" name="attachment" class="form-control form-control-modern @error('attachment') is-invalid @enderror">
                                    @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp, pdf, doc, docx, xls, xlsx (≤ 4MB).</div>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-brand"><i class="bi bi-send me-1"></i> Gửi</button>
                                </div>
                                <div class="text-muted small mt-2">Bạn có thể gửi chỉ tệp đính kèm nếu không có nội dung.</div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card card-glass">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <span class="badge badge-soft-{{ $map[$ticket->status] ?? 'secondary' }}">{{ $labelMap[$ticket->status] ?? $ticket->status }}</span>
                            </div>
                            <p class="mb-3">Vé này đã {{ $labelMap[$ticket->status] ?? 'khoá' }} nên không thể gửi phản hồi.</p>
                            <a href="{{ route('support.create') }}" class="btn btn-brand">
                                <i class="bi bi-plus-circle me-1"></i> Tạo yêu cầu mới
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal xem trước file --}}
    <div class="modal fade preview-modal" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Xem tệp đính kèm</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContainer" class="text-center"></div>
                </div>
                <div class="modal-footer">
                    <a id="previewDownload" href="#" class="btn btn-outline-secondary" download>
                        <i class="bi bi-download"></i> Tải về
                    </a>
                    <button type="button" class="btn btn-brand" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* =================== Banner =================== */
        .support-banner {
            height: 260px;
            background-image: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
                url('https://images.unsplash.com/photo-1524758631624-e2822e304c36?q=80&w=1600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .support-banner .breadcrumb-item a {
            color: #f8f9fa;
            text-decoration: none;
        }
        .support-banner .breadcrumb-item a:hover {
            color: var(--brand);
        }
        .support-banner .breadcrumb-item.active {
            color: #adb5bd;
        }

        /* =================== Form and Button Styles =================== */
        .form-control-modern, .form-select.form-control-modern {
            border-radius: 0.8rem;
            border: 1px solid #e9ecef;
            background: #fff;
        }
        .form-control-modern:focus, .form-select.form-control-modern:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.2rem var(--ring);
        }
        .btn-brand {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
            padding: 0.5rem 1rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }
        .btn-brand:hover {
            background-color: var(--brand-600);
            border-color: var(--brand-600);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .btn-outline-brand {
            color: var(--brand);
            border-color: var(--brand);
            padding: 0.45rem 0.9rem;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .btn-outline-brand:hover {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }
        .text-brand {
            color: var(--brand);
        }

        /* =================== Badges =================== */
        .badge-soft-info {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .badge-soft-brand {
            background: rgba(162, 14, 56, 0.1);
            color: var(--brand);
        }
        .badge-soft-success {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }
        .badge-soft-muted {
            background: rgba(125, 114, 108, 0.1);
            color: var(--muted);
        }
        .badge-soft-secondary {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        /* =================== Card Styles =================== */
        .card-glass {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 255, 255, 0.98));
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid rgba(15, 23, 42, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-glass:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        /* =================== Modal Preview =================== */
        .preview-modal .modal-dialog {
            max-width: 960px;
        }
        .preview-frame {
            width: 100%;
            height: 70vh;
            border: 0;
        }
        .preview-image {
            max-width: 100%;
            max-height: 70vh;
            display: block;
            margin: 0 auto;
        }

        /* =================== Links =================== */
        a {
            color: var(--brand);
            text-decoration: none;
        }
        a:hover {
            color: var(--brand-600);
        }

        /* =================== Responsive Design =================== */
        @media (max-width: 991px) {
            .support-banner {
                height: 220px;
            }
            .support-banner .display-5 {
                font-size: 2rem;
            }
            .row.g-4 {
                gap: 1rem;
            }
            .col-lg-8, .col-lg-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .preview-modal .modal-dialog {
                max-width: 90vw;
            }
            .preview-frame, .preview-image {
                max-height: 60vh;
            }
        }

        @media (max-width: 767px) {
            .support-banner {
                height: 180px;
            }
            .support-banner .display-5 {
                font-size: 1.8rem;
            }
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            .card-body {
                padding: 1rem;
            }
            .btn-brand {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
            .btn-outline-brand {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .form-control-modern, .form-select.form-control-modern {
                font-size: 0.9rem;
            }
            .preview-frame, .preview-image {
                max-height: 50vh;
            }
        }

        @media (max-width: 575px) {
            .support-banner {
                height: 160px;
            }
            .support-banner .display-5 {
                font-size: 1.6rem;
            }
            .support-banner .breadcrumb {
                font-size: 0.85rem;
            }
            .card-glass {
                padding: 0.75rem;
            }
            .card-body {
                padding: 0.75rem;
            }
            .btn-brand {
                padding: 0.35rem 0.7rem;
                font-size: 0.85rem;
            }
            .btn-outline-brand {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }
            .form-control-modern, .form-select.form-control-modern {
                font-size: 0.85rem;
            }
            .preview-modal .modal-dialog {
                max-width: 85vw;
            }
            .preview-frame, .preview-image {
                max-height: 40vh;
            }
        }
    </style>
@endpush

@push('scripts-page')
<script>
    (function(){
        const modalEl = document.getElementById('previewModal');
        const previewContainer = document.getElementById('previewContainer');
        const previewDownload = document.getElementById('previewDownload');

        const IMG_EXTS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];
        const PDF_EXTS = ['pdf'];
        const MS_EXTS = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        function guessExt(url) {
            if (!url) return '';
            const clean = url.split('?')[0];
            const m = clean.match(/\.([a-z0-9]+)$/i);
            return m ? m[1].toLowerCase() : '';
        }

        function render(url, ext) {
            previewContainer.innerHTML = '';
            previewDownload.removeAttribute('href');
            if (!url) {
                previewContainer.innerHTML = '<div class="p-3 text-center text-muted">Không có tệp đính kèm.</div>';
                return;
            }
            ext = (ext || guessExt(url)).toLowerCase();
            let html = '';
            if (IMG_EXTS.includes(ext)) {
                html = `<img class="preview-image" src="${url}" alt="preview" 
                        onerror="this.parentNode.innerHTML='<div class=&quot;p-3 text-center text-muted&quot;>Không thể tải ảnh.</div>'">`;
            } else if (PDF_EXTS.includes(ext)) {
                html = `<iframe class="preview-frame" src="${url}#toolbar=1"></iframe>`;
            } else if (MS_EXTS.includes(ext)) {
                const viewer = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(url);
                html = `<iframe class="preview-frame" src="${viewer}"></iframe>`;
            } else {
                html = `<div class="p-4 text-center">
                        <div class="display-6 mb-3"><i class="bi bi-file-earmark-text"></i></div>
                        <p class="mb-2">Định dạng này chưa hỗ trợ xem trước.</p>
                        <p class="text-muted small mb-0">Vui lòng bấm "Tải về" để mở bằng ứng dụng trên máy.</p>
                        </div>`;
            }
            previewContainer.innerHTML = html;
            previewDownload.setAttribute('href', url);
        }

        modalEl.addEventListener('show.bs.modal', (e) => {
            const btn = e.relatedTarget;
            const url = btn?.getAttribute('data-preview-url') || '';
            const ext = btn?.getAttribute('data-preview-ext') || '';
            render(url, ext);
        });
    })();
</script>
@endpush