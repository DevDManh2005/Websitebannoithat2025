@extends(auth()->user()->role->name === 'staff' ? 'staff.layouts.app' : 'admins.layouts.app')


@section('title','Vé #'.$ticket->id)

@push('styles')
<style>
  #ticket-show .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #ticket-show .status-pill{
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.25rem .55rem; border-radius:999px; font-weight:600; font-size:.82rem;
    border:1px solid rgba(23,26,31,.1); background:#f7f7fb;
  }
  #ticket-show .status-open{ background: color-mix(in srgb, var(--brand,#C46F3B) 12%, white); border-color: color-mix(in srgb, var(--brand,#C46F3B) 35%, #ddd) }
  #ticket-show .status-in_progress{ background:#fff5d8; border-color:#f1d08b }
  #ticket-show .status-resolved{ background:#e7f6ed; border-color:#a7d6b8 }
  #ticket-show .status-closed{ background:#eef0f3; border-color:#cfd4db; color:#5c6570 }

  /* Replies timeline */
  #ticket-show .reply{
    position:relative; padding-left:1rem; margin-left:.25rem;
  }
  #ticket-show .reply::before{
    content:''; position:absolute; left:0; top:.35rem; bottom:.35rem; width:3px;
    background:linear-gradient(180deg, var(--brand,#C46F3B), transparent);
    border-radius:4px;
  }
  #ticket-show .reply .bubble{
    background:linear-gradient(180deg, rgba(234,223,206,.25), transparent), var(--card);
    border:1px solid rgba(32,25,21,.08); border-radius:12px; padding:.75rem .9rem;
  }

  /* Attachment preview modal */
  .preview-modal .modal-dialog { max-width: 1000px }
  .preview-frame{ width:100%; height:70vh; border:0 }
  .preview-image{ max-width:100%; max-height:70vh; display:block; margin:0 auto }
</style>
@endpush

@section('content')
<div id="ticket-show" class="container-fluid">

  {{-- Header --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <div class="d-flex align-items-center gap-2">
        <h1 class="h5 fw-bold mb-0">Vé #{{ $ticket->id }}</h1>
        @php $vn = ['open'=>'Mở','in_progress'=>'Đang xử lý','resolved'=>'Đã giải quyết','closed'=>'Đã đóng']; @endphp
        <span class="status-pill status-{{ $ticket->status }}">{{ $vn[$ticket->status] ?? ucfirst($ticket->status) }}</span>
      </div>
      <div class="text-muted small mt-1">
        Tạo lúc {{ $ticket->created_at->format('d/m/Y H:i') }} · Cập nhật {{ $ticket->updated_at->diffForHumans() }}
      </div>
    </div>

    <form method="post" action="{{ route('admin.support_tickets.updateStatus',$ticket) }}" class="d-flex align-items-center gap-2">
      @csrf @method('PATCH')
      @php $statuses=['open'=>'Mở','in_progress'=>'Đang xử lý','resolved'=>'Đã giải quyết','closed'=>'Đã đóng']; @endphp
      <select name="status" class="form-select">
        @foreach($statuses as $k=>$label)
          <option value="{{ $k }}" @selected($ticket->status===$k)>{{ $label }}</option>
        @endforeach
      </select>
      <button class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i> Cập nhật</button>
      <a href="{{ route('admin.support_tickets.index') }}" class="btn btn-light"><i class="bi bi-arrow-left-short me-1"></i> Quay lại</a>
    </form>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      {{-- Ticket content --}}
      <div class="card mb-3">
        <div class="card-header fw-semibold">Nội dung yêu cầu</div>
        <div class="card-body">
          <div class="mb-2 fw-semibold">{{ $ticket->subject }}</div>
          <div style="white-space:pre-wrap">{{ $ticket->message }}</div>
        </div>
      </div>

      {{-- Replies --}}
      <div class="card">
        <div class="card-header fw-semibold">Trao đổi</div>
        <div class="card-body">
          @forelse($ticket->replies as $r)
            @php
              $attachmentUrl = $r->attachment ? asset('storage/'.$r->attachment) : null;
              $ext = $r->attachment ? \Illuminate\Support\Str::lower(pathinfo($r->attachment, PATHINFO_EXTENSION)) : null;
            @endphp

            <div class="reply mb-4 pb-1">
              <div class="d-flex justify-content-between">
                <div class="fw-semibold">
                  {{ $r->user?->name ?? 'Người dùng' }}
                  <span class="text-muted small">({{ $r->user?->email ?? 'n/a' }})</span>
                </div>
                <div class="text-muted small">{{ $r->created_at->format('d/m/Y H:i') }}</div>
              </div>
              <div class="bubble mt-2">
                @if($r->message)
                  <div style="white-space:pre-wrap">{{ $r->message }}</div>
                @endif

                @if($attachmentUrl)
                  <div class="mt-2 d-flex align-items-center gap-2">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary attachment-link"
                            data-url="{{ $attachmentUrl }}" data-ext="{{ $ext }}">
                      <i class="bi bi-paperclip"></i> Xem tệp đính kèm
                    </button>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ $attachmentUrl }}" target="_blank" download>
                      <i class="bi bi-download"></i> Tải về
                    </a>
                  </div>
                @endif
              </div>
            </div>
          @empty
            <div class="text-muted">Chưa có phản hồi nào.</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      {{-- Reply form --}}
      <div class="card">
        <div class="card-header fw-semibold">Gửi phản hồi</div>
        <div class="card-body">
          <form method="post" action="{{ route('admin.support_tickets.replies.store',$ticket) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label class="form-label">Nội dung</label>
              <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4">{{ old('message') }}</textarea>
              @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Đính kèm (tuỳ chọn)</label>
              <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror">
              @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">Hỗ trợ: jpg, jpeg, png, webp, pdf, doc, docx, xls, xlsx (≤ 4MB).</div>
            </div>

            <div class="d-grid">
              <button class="btn btn-primary"><i class="bi bi-send me-1"></i> Gửi phản hồi</button>
            </div>
            <div class="text-muted small mt-2">Cần ít nhất nội dung hoặc tệp đính kèm.</div>
          </form>
        </div>
      </div>

      {{-- Meta --}}
      <div class="card mt-3">
        <div class="card-header fw-semibold">Thông tin</div>
        <div class="card-body">
          <div class="mb-2"><span class="text-muted">Khách:</span>
            @if($ticket->user)
              <div class="fw-semibold">{{ $ticket->user->name }}</div>
              <div class="text-muted small">{{ $ticket->user->email }}</div>
            @else
              <div class="text-muted">Khách lẻ</div>
            @endif
          </div>
          <div class="text-muted small">ID: #{{ $ticket->id }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Preview Modal --}}
<div class="modal fade preview-modal" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Xem tệp đính kèm</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div id="previewContainer" class="text-center"><!-- JS will inject --></div>
      </div>
      <div class="modal-footer">
        <a id="previewDownload" href="#" class="btn btn-outline-secondary" download>
          <i class="bi bi-download"></i> Tải về
        </a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function () {
    const modalEl = document.getElementById('previewModal');
    const previewContainer = document.getElementById('previewContainer');
    const previewDownload = document.getElementById('previewDownload');

    const isImage = (ext) => ['jpg','jpeg','png','webp','gif'].includes((ext||'').toLowerCase());
    const isPdf   = (ext) => (ext||'').toLowerCase() === 'pdf';

    document.querySelectorAll('.attachment-link').forEach(btn => {
      btn.addEventListener('click', function () {
        const url = this.getAttribute('data-url');
        const ext = (this.getAttribute('data-ext') || '').toLowerCase();

        previewContainer.innerHTML = '';
        previewDownload.setAttribute('href', url);

        let html = '';
        if (isImage(ext)) {
          html = `<img class="preview-image" src="${url}" alt="preview">`;
        } else if (isPdf(ext)) {
          html = `<iframe class="preview-frame" src="${url}#toolbar=1"></iframe>`;
        } else {
          html = `
            <div class="p-3 text-center">
              <div class="display-6 mb-3"><i class="bi bi-file-earmark-text"></i></div>
              <p class="mb-2">Không hỗ trợ xem trước định dạng này.</p>
              <p class="text-muted small mb-0">Bạn có thể tải về để xem trên máy.</p>
            </div>`;
        }

        previewContainer.innerHTML = html;
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
      });
    });
  })();
</script>
@endpush
