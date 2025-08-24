@extends(auth()->user()->role->name === 'staff' ? 'staff.layouts.app' : 'admins.layouts.app')



@section('title', 'Chi tiết nhà cung cấp')

@push('styles')
<style>
  #supplier-show .bar{
    border-radius:16px; padding:12px;
    background:linear-gradient(140deg, rgba(196,111,59,.08), rgba(78,107,82,.06) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08); box-shadow:0 6px 22px rgba(18,38,63,.06);
  }
  #supplier-show .meta-label{
    font-size:.8rem; color:#6c757d; text-transform:uppercase; letter-spacing:.04em;
  }
  #supplier-show .value{
    font-weight:600; color:var(--text,#2B2623);
  }
  #supplier-show .stat{
    border-radius:12px; padding:.75rem .9rem; background:linear-gradient(180deg, rgba(234,223,206,.25), transparent);
    border:1px solid rgba(32,25,21,.08)
  }
</style>
@endpush

@section('content')
<div id="supplier-show" class="container-fluid">

  {{-- Header --}}
  <div class="bar mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <h1 class="h5 fw-bold mb-0">Chi tiết nhà cung cấp</h1>
      <span class="text-muted small">#{{ $supplier->id }}</span>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left-short me-1"></i> Quay lại
      </a>
      <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-outline-primary">
        <i class="bi bi-pencil-square me-1"></i> Sửa
      </a>
      <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline"
            onsubmit="return confirm('Xóa nhà cung cấp này?')">
        @csrf @method('DELETE')
        <button class="btn btn-outline-danger">
          <i class="bi bi-trash me-1"></i> Xóa
        </button>
      </form>
    </div>
  </div>

  {{-- Flash --}}
  @includeIf('admins.shared.flash')

  {{-- Content --}}
  <div class="row g-3">
    <div class="col-12 col-xl-8">
      <div class="card">
        <div class="card-header">
          <strong>Thông tin nhà cung cấp</strong>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="meta-label mb-1">Tên</div>
              <div class="value">{{ $supplier->name }}</div>
            </div>
            <div class="col-md-6">
              <div class="meta-label mb-1">Người liên hệ</div>
              <div class="value">{{ $supplier->contact_name ?? '—' }}</div>
            </div>

            <div class="col-md-6">
              <div class="meta-label mb-1">Điện thoại</div>
              <div class="d-flex align-items-center gap-2 value">
                <span>{{ $supplier->phone ?? '—' }}</span>
                @if($supplier->phone)
                  <a class="btn btn-sm btn-outline-primary" href="tel:{{ $supplier->phone }}"><i class="bi bi-telephone"></i></a>
                  <button class="btn btn-sm btn-outline-secondary" type="button"
                          title="Sao chép số điện thoại"
                          data-copy="{{ $supplier->phone }}" data-label="số điện thoại">
                    <i class="bi bi-clipboard"></i>
                  </button>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <div class="meta-label mb-1">Email</div>
              <div class="d-flex align-items-center gap-2 value">
                <span>{{ $supplier->email ?? '—' }}</span>
                @if($supplier->email)
                  <a class="btn btn-sm btn-outline-primary" href="mailto:{{ $supplier->email }}"><i class="bi bi-envelope"></i></a>
                  <button class="btn btn-sm btn-outline-secondary" type="button"
                          title="Sao chép email"
                          data-copy="{{ $supplier->email }}" data-label="email">
                    <i class="bi bi-clipboard"></i>
                  </button>
                @endif
              </div>
            </div>

            <div class="col-12">
              <div class="meta-label mb-1">Địa chỉ</div>
              <div class="d-flex align-items-center gap-2 value">
                <span class="me-1">{{ $supplier->address ?? '—' }}</span>
                @if($supplier->address)
                  <a class="btn btn-sm btn-outline-primary"
                     href="https://www.google.com/maps/search/?api=1&query={{ urlencode($supplier->address) }}"
                     target="_blank" rel="noopener">
                    <i class="bi bi-geo-alt"></i>
                  </a>
                  <button class="btn btn-sm btn-outline-secondary" type="button"
                          title="Sao chép địa chỉ"
                          data-copy="{{ $supplier->address }}" data-label="địa chỉ">
                    <i class="bi bi-clipboard"></i>
                  </button>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer d-flex flex-wrap gap-2">
          @if($supplier->phone)
            <a class="btn btn-outline-primary" href="tel:{{ $supplier->phone }}"><i class="bi bi-telephone me-1"></i>Gọi</a>
          @endif
          @if($supplier->email)
            <a class="btn btn-outline-primary" href="mailto:{{ $supplier->email }}"><i class="bi bi-envelope me-1"></i>Email</a>
          @endif
          @if($supplier->address)
            <a class="btn btn-outline-primary"
               href="https://www.google.com/maps/search/?api=1&query={{ urlencode($supplier->address) }}"
               target="_blank" rel="noopener"><i class="bi bi-map me-1"></i>Mở Maps</a>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-4">
      <div class="card h-100">
        <div class="card-header"><strong>Nhật ký</strong></div>
        <div class="card-body">
          <div class="stat mb-2">
            <div class="meta-label mb-1">Tạo lúc</div>
            <div class="value">{{ optional($supplier->created_at)->format('d/m/Y H:i') ?? '—' }}</div>
          </div>
          <div class="stat">
            <div class="meta-label mb-1">Cập nhật</div>
            <div class="value">{{ optional($supplier->updated_at)->format('d/m/Y H:i') ?? '—' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
(function(){
  // copy-to-clipboard + toast
  document.querySelectorAll('[data-copy]').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const text = btn.getAttribute('data-copy') || '';
      const label = btn.getAttribute('data-label') || 'nội dung';
      if(!text) return;
      navigator.clipboard?.writeText(text).then(()=> toast(`Đã sao chép ${label}!`));
    });
  });

  function toast(message){
    const cont = document.querySelector('.toast-container') || document.body;
    const el = document.createElement('div');
    el.className = 'toast align-items-center text-bg-success border-0 mb-2';
    el.role = 'alert';
    el.innerHTML = `<div class="d-flex">
        <div class="toast-body"><i class="bi bi-clipboard-check me-2"></i>${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>`;
    cont.appendChild(el);
    try{ new bootstrap.Toast(el, {delay:1600}).show(); }catch(e){}
    setTimeout(()=> el.remove(), 2200);
  }
})();
</script>
@endpush
@endsection
