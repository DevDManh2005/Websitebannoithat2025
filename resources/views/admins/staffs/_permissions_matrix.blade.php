@php
  use Illuminate\Support\Str;

  // danh sách id quyền đã gán trực tiếp
  $assignedIDs  = array_map('intval', (array) old('permissions', $assigned ?? []));
  // danh sách id quyền kế thừa từ vai trò (không khóa, chỉ tick sẵn)
  $inheritedIDs = array_map('intval', (array) ($inherited ?? []));

  // Nhãn module (có thể truyền từ view cha để override)
  $moduleLabels = (array)($moduleLabels ?? []) + [
    'orders'=>'Đơn hàng','reviews'=>'Đánh giá','products'=>'Sản phẩm','categories'=>'Danh mục',
    'brands'=>'Thương hiệu','suppliers'=>'Nhà cung cấp','inventories'=>'Kho','vouchers'=>'Voucher',
    'slides'=>'Slide','blogs'=>'Bài viết','blog-categories'=>'Chuyên mục','uploads'=>'Tải lên',
    'users'=>'Tài khoản','permissions'=>'Quyền'
  ];

  $humanModule = fn (string $m) => $moduleLabels[$m]
    ?? Str::of($m)->replace(['_','-'],' ')->headline();
@endphp

<style>
  /* ====== Permissions Matrix – brand-aware, equal rows ====== */
  #pmx, #pmx * { box-sizing: border-box; }
  #pmx{ width:100%; overflow-x:hidden }

  /* Toolbar */
  #pmx .pmx-toolbar{
    padding:10px 12px;border-radius:14px;
    background:linear-gradient(130deg, rgba(196,111,59,.10), rgba(78,107,82,.08) 70%), var(--card);
    border:1px solid rgba(32,25,21,.08);
    box-shadow:var(--shadow,0 10px 30px rgba(32,25,21,.12));
  }
  #pmx .pmx-toolbar .btn-outline-secondary{ border-color: rgba(32,25,21,.15) }
  #pmx .pmx-toolbar .btn-outline-secondary:hover{
    background:var(--brand,#C46F3B); border-color:var(--brand,#C46F3B); color:#fff
  }

  /* Grid độc lập (không dùng .row/.col để tránh margin âm) */
  #pmx .pmx-grid{
    --gap:18px; --padX:6px;
    display:grid; gap:var(--gap);
    grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
    padding:6px var(--padX);
    margin:0 !important; width:100%;
  }
  #pmx .pmx-cell{ min-width:0 }

  /* Card */
  #pmx .pmx-card{
    display:flex; flex-direction:column; width:100%; max-width:100%;
    background:var(--card,#fff);
    border:1px solid rgba(32,25,21,.10);
    border-radius:var(--radius,16px);
    box-shadow:var(--shadow,0 10px 30px rgba(32,25,21,.12));
    transition:transform .12s ease, box-shadow .2s ease;
    overflow:hidden;
    height:auto; /* equalizer sẽ set lại theo hàng */
  }
  #pmx .pmx-card:hover{ transform: translateY(-1px) }

  /* Header */
  #pmx .pmx-head{
    padding:12px 16px; border-bottom:1px dashed rgba(32,25,21,.12);
    background:linear-gradient(180deg, rgba(234,223,206,.22), transparent), var(--card);
  }
  /* Nhóm hành động bên phải – gom switch + label để không "dính mép" */
  #pmx .pmx-actions{
    display:flex; align-items:center; gap:.45rem;
    padding:.15rem .6rem; border-radius:999px;
    background:rgba(234,223,206,.45);
    border:1px solid rgba(32,25,21,.10);
  }
  #pmx .pmx-actions .form-check{ margin:0; padding:0 }
  #pmx .pmx-actions .form-switch .form-check-input{
    width:2.35rem; height:1.2rem; margin:0;
    cursor:pointer; background-color:#e9ecef; border-color:#ced4da;
  }
  #pmx .pmx-actions .form-switch .form-check-input:checked{
    background-color:var(--brand,#C46F3B); border-color:var(--brand,#C46F3B);
  }
  #pmx .pmx-actions .form-switch .form-check-input:focus{
    box-shadow:0 0 0 .2rem rgba(196,111,59,.25);
  }
  #pmx .pmx-actions label{ margin:0; white-space:nowrap; color:var(--text,#2B2623) }

  /* Body + Items */
  #pmx .pmx-body{ padding:12px 16px; min-width:0; flex:1; min-height:0 }
  #pmx .perm-item.form-check{
    padding:.42rem .55rem; border-radius:8px;
    display:flex; align-items:flex-start; gap:.55rem;
    margin:0;               /* dập margin mặc định */
    padding-left:.55rem;    /* gutter trái nhẹ */
    transition: background .15s;
  }
  #pmx .perm-item:hover{ background:#fafbff }
  #pmx .perm-item .form-check-input{
    float:none;             /* dập float trái bootstrap */
    margin:.15rem .35rem 0 0;
    accent-color: var(--brand,#C46F3B);
    flex:0 0 auto;
  }
  #pmx .perm-item .form-check-label{
    margin:0; display:flex; align-items:center; gap:.5rem; overflow-wrap:anywhere;
  }

  /* Chips & text */
  #pmx .pill{
    display:inline-block; padding:.22rem .6rem; border-radius:999px;
    background:#f2f4f7; border:1px solid rgba(23,26,31,.12);
    font-weight:600; font-size:.82rem; color:var(--text,#2B2623);
    transition: transform .08s ease, background .12s ease, border-color .12s ease;
  }
  #pmx .mono{ font:500 .9rem ui-monospace, Menlo, Consolas, "Courier New", monospace; overflow-wrap:anywhere }
  #pmx .muted{ color:#6c757d }

  /* Chip active theo brand */
  #pmx .pmx-cb:checked + label .pill{
    background: color-mix(in srgb, var(--brand,#C46F3B) 18%, white);
    border-color: color-mix(in srgb, var(--brand,#C46F3B) 42%, #ddd);
    transform: translateY(-1px);
  }

  /* Footer */
  #pmx .pmx-foot{ padding:9px 16px; color:#6c757d; background:transparent }

  /* (Tuỳ chọn) min-height nhẹ để cảm giác đều hơn trên màn lớn */
  @media (min-width: 1200px){ #pmx .pmx-card{ min-height: 320px } }
  @media (min-width: 1600px){ #pmx .pmx-card{ min-height: 340px } }
</style>

<div id="pmx">
  {{-- Toolbar --}}
  <div class="pmx-toolbar mb-3 d-flex justify-content-end gap-2">
    <button type="button" class="btn btn-outline-secondary" id="btn-pmx-all">
      <i class="bi bi-check2-square me-1"></i> Chọn hết
    </button>
    <button type="button" class="btn btn-outline-secondary" id="btn-pmx-none">
      <i class="bi bi-x-square me-1"></i> Bỏ hết
    </button>
  </div>

  @if(collect($modules)->isEmpty())
    <div class="alert alert-warning mb-0">Chưa có quyền nào.</div>
  @else
    <div class="pmx-grid" id="pmx-grid">
      @foreach($modules as $module => $perms)
        @php
          $mid   = Str::slug((string)$module);
          $total = count($perms);
        @endphp

        <div class="pmx-cell">
          <div class="pmx-card">
            <div class="pmx-head d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2">
                <div class="fw-semibold">{{ $humanModule((string)$module) }}</div>
                <span class="muted small">({{ $module }})</span>
                <span class="pill small">
                  <span class="pmx-count-checked">0</span>/<span class="pmx-count-total">{{ $total }}</span>
                </span>
              </div>
              <div class="pmx-actions">
                <div class="form-check form-switch m-0">
                  <input class="form-check-input pmx-toggle" type="checkbox" id="toggle-{{ $mid }}" aria-label="Chọn hết {{ $humanModule((string)$module) }}">
                </div>
                <label class="small" for="toggle-{{ $mid }}">Chọn hết</label>
              </div>
            </div>

            <div class="pmx-body">
              @foreach($perms as $p)
                @php
                  $preset = in_array((int) $p->id, $assignedIDs, true)
                         || in_array((int) $p->id, $inheritedIDs, true);
                @endphp
                <div class="form-check perm-item">
                  <input class="form-check-input pmx-cb"
                         type="checkbox"
                         name="permissions[]"
                         value="{{ $p->id }}"
                         id="pmx-{{ $p->id }}"
                         @checked($preset)>
                  <label class="form-check-label" for="pmx-{{ $p->id }}">
                    <span class="pill">{{ $p->action }}</span>
                    <span class="muted small mono">({{ $module }}.{{ $p->action }})</span>
                  </label>
                </div>
              @endforeach
            </div>

            <div class="pmx-foot small">
              <span class="mono">{{ $module }}</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const grid = document.getElementById('pmx-grid');
  if(!grid) return;

  /* ---------- Helpers ---------- */
  const debounce = (fn, ms=120) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; };

  function refreshCard(card){
    const all     = card.querySelectorAll('.pmx-cb');
    const checked = card.querySelectorAll('.pmx-cb:checked');
    const countEl = card.querySelector('.pmx-count-checked');

    if (countEl) countEl.textContent = checked.length;

    const toggle = card.querySelector('.pmx-toggle');
    if (toggle){
      toggle.indeterminate = (checked.length > 0 && checked.length < all.length);
      toggle.checked       = (checked.length === all.length);
    }
  }
  function refreshAll(){
    grid.querySelectorAll('.pmx-card').forEach(refreshCard);
    equalizeRows();
  }

  // Equalize height theo từng hàng (nhóm theo offsetTop)
  function equalizeRows(){
    const cards = Array.from(grid.querySelectorAll('.pmx-card'));
    if(!cards.length) return;

    // reset trước khi đo
    cards.forEach(c => c.style.height = 'auto');

    const rows = {};
    cards.forEach(c => {
      const top = c.offsetTop;
      (rows[top] ||= []).push(c);
    });

    Object.values(rows).forEach(row => {
      const maxH = Math.max(...row.map(c => c.getBoundingClientRect().height));
      row.forEach(c => c.style.height = maxH + 'px');
    });
  }

  /* ---------- Init ---------- */
  refreshAll();
  window.addEventListener('load', equalizeRows);
  window.addEventListener('resize', debounce(equalizeRows, 120));

  /* ---------- Events ---------- */
  grid.addEventListener('change', (e)=>{
    const t = e.target;
    if (t.classList.contains('pmx-toggle')){
      const card  = t.closest('.pmx-card');
      const check = !t.indeterminate && t.checked;
      card.querySelectorAll('.pmx-cb').forEach(cb => cb.checked = check);
      refreshCard(card);
      equalizeRows();
    } else if (t.classList.contains('pmx-cb')){
      refreshCard(t.closest('.pmx-card'));
      equalizeRows();
    }
  });

  document.getElementById('btn-pmx-all')?.addEventListener('click', ()=>{
    grid.querySelectorAll('.pmx-cb').forEach(cb => cb.checked = true);
    refreshAll();
  });
  document.getElementById('btn-pmx-none')?.addEventListener('click', ()=>{
    grid.querySelectorAll('.pmx-cb').forEach(cb => cb.checked = false);
    refreshAll();
  });
});
</script>
