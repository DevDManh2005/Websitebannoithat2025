@extends('admins.layouts.app')

@section('title','Vé #'.$ticket->id)

@push('styles')
<style>
  /* Modal xem trước file full-screen cảm giác thoáng hơn */
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
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-0">Vé #{{ $ticket->id }} — {{ $ticket->subject }}</h4>
    <div class="text-muted small">
      Tạo lúc {{ $ticket->created_at->format('d/m/Y H:i') }}
      · Cập nhật {{ $ticket->updated_at->diffForHumans() }}
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
    <button class="btn btn-primary"><i class="bi bi-check2-circle"></i> Cập nhật</button>
  </form>
</div>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card mb-3">
      <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
        <span>Nội dung yêu cầu</span>
        @php $map = ['open'=>'primary','in_progress'=>'warning','resolved'=>'success','closed'=>'secondary']; @endphp
        <span class="badge text-bg-{{ $map[$ticket->status] ?? 'secondary' }}">{{ $ticket->status }}</span>
      </div>
      <div class="card-body">
        <p class="mb-0" style="white-space:pre-wrap">{{ $ticket->message }}</p>
      </div>
    </div>

    <div class="card">
      <div class="card-header fw-semibold">Trao đổi</div>
      <div class="card-body">
        @forelse($ticket->replies as $r)
          @php
            $attachmentUrl = $r->attachment ? asset('storage/'.$r->attachment) : null;
            $ext = $r->attachment ? \Illuminate\Support\Str::lower(pathinfo($r->attachment, PATHINFO_EXTENSION)) : null;
          @endphp

          <div class="mb-4 pb-4 border-bottom">
            <div class="d-flex justify-content-between">
              <div class="fw-semibold">
                {{ $r->user?->name ?? 'Người dùng' }}
                <span class="text-muted small">
                  ({{ $r->user?->email ?? 'n/a' }})
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
                  class="btn btn-sm btn-outline-secondary attachment-link"
                  data-url="{{ $attachmentUrl }}"
                  data-ext="{{ $ext }}"
                >
                  <i class="bi bi-paperclip"></i> Xem tệp đính kèm
                </button>
                <a class="btn btn-sm btn-outline-secondary" href="{{ $attachmentUrl }}" target="_blank" download>
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

  <div class="col-lg-4">
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
            <button class="btn btn-primary"><i class="bi bi-send"></i> Gửi phản hồi</button>
          </div>
          <div class="text-muted small mt-2">Cần ít nhất nội dung hoặc tệp đính kèm.</div>
        </form>
      </div>
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
        <div id="previewContainer" class="text-center">
          {{-- nội dung được JS chèn vào --}}
        </div>
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

    function isImage(ext) {
      return ['jpg','jpeg','png','webp','gif'].includes((ext || '').toLowerCase());
    }
    function isPdf(ext) {
      return ext && ext.toLowerCase() === 'pdf';
    }

    document.querySelectorAll('.attachment-link').forEach(btn => {
      btn.addEventListener('click', function () {
        const url = this.getAttribute('data-url');
        const ext = (this.getAttribute('data-ext') || '').toLowerCase();

        // reset container
        previewContainer.innerHTML = '';
        previewDownload.setAttribute('href', url);

        let html = '';
        if (isImage(ext)) {
          html = '<img class="preview-image" src="'+url+'" alt="preview image">';
        } else if (isPdf(ext)) {
          html = '<iframe class="preview-frame" src="'+url+'#toolbar=1"></iframe>';
        } else {
          html = '<div class="p-3 text-center">' +
                   '<div class="display-6 mb-3"><i class="bi bi-file-earmark-text"></i></div>' +
                   '<p class="mb-2">Không hỗ trợ xem trước định dạng này.</p>' +
                   '<p class="text-muted small mb-0">Bạn có thể tải về để xem trên máy.</p>' +
                 '</div>';
        }

        previewContainer.innerHTML = html;

        // show modal
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
      });
    });
  })();
</script>
@endpush
