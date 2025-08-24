
@extends('admins.layouts.app')

@section('title', 'Sửa sản phẩm: ' . $product->name)

@section('content')
@php
    // Tự động dùng 'staff' nếu route hiện tại thuộc nhóm staff.*, ngược lại dùng 'admin'
    $rp = request()->routeIs('staff.*') ? 'staff' : 'admin';
@endphp

@php
    $selectedCats = old('categories', $product->categories->pluck('id')->toArray());
@endphp

<style>
    .card-soft { border-radius: 16px; border: 1px solid rgba(32,25,21,.08); }
    .card-soft .card-header { background: transparent; border-bottom: 1px dashed rgba(32,25,21,.12); }
    .cat-dropdown .dropdown-menu {
        width: 100%;
        max-height: 420px;
        overflow: auto;
        border-radius: 12px;
    }
    .cat-node .toggle {
        width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;
        border: 1px solid rgba(32,25,21,.12); border-radius: 6px; background: #fff;
    }
    .cat-node .children { border-left: 1px dashed rgba(32,25,21,.15); margin-left: 22px; padding-left: 12px; }
    .cat-node .form-check-input { margin-top: 0; }
    .cat-summary {
        display: flex; align-items: center; gap: 6px; min-height: 38px;
        padding: 6px 10px; border: 1px solid var(--bs-border-color); border-radius: .375rem; background: #fff;
        cursor: pointer; width: 100%;
    }
    .cat-summary span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .badge.bg-secondary-soft { background: #f0f0f0; color: #555; }
    .variant-item .row { margin-bottom: 0.5rem; }
    .variant-item .form-label { font-size: 0.9rem; font-weight: 500; }
    .variant-item .form-control { font-size: 0.95rem; }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sửa sản phẩm: {{ $product->name }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route($rp.'.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Thông tin cơ bản --}}
        <div class="card shadow mb-4 card-soft">
            <div class="card-header">Thông tin cơ bản</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                           value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Đường dẫn (Slug)</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
                           value="{{ old('slug', $product->slug) }}" placeholder="Để trống để tự tạo theo tên">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Danh mục: Dropdown Cây + giữ select hidden --}}
                <div class="mb-3">
                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <div class="dropdown cat-dropdown">
                        <button class="cat-summary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-folder2-open"></i>
                            <span id="cat-summary-text">Chọn danh mục…</span>
                            <i class="ms-auto bi bi-caret-down-fill"></i>
                        </button>
                        <div class="dropdown-menu p-3">
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="cat-search" placeholder="Tìm danh mục…">
                            </div>
                            <div id="cat-tree"></div>
                        </div>
                    </div>
                    <select class="form-select d-none @error('categories') is-invalid @enderror" id="categories" name="categories[]" multiple required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, $selectedCats) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Bạn có thể chọn cả danh mục cha và danh mục con.</div>
                    @error('categories')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="brand_id" class="form-label">Thương hiệu</label>
                        <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                            <option value="">Không có</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id">
                            <option value="">Không có</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả sản phẩm</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                              name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="label" class="form-label">Nhãn (ví dụ: Mới, Hot, Giảm giá)</label>
                        <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label"
                               value="{{ old('label', $product->label) }}">
                        @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check mt-4">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Kích hoạt sản phẩm</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check mt-4">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Sản phẩm nổi bật</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hình ảnh --}}
        <div class="card mb-4 card-soft">
            <div class="card-header">Hình ảnh sản phẩm</div>
            <div class="card-body">
                <h6 class="mb-2">Ảnh chính</h6>
                <div class="mb-3">
                    <img src="{{ $product->cover_image_url ?? 'https://placehold.co/150x100?text=No+Image' }}"
                         alt="Ảnh chính" width="150" class="mb-2 rounded border">
                    <label for="main_image_file" class="form-label">Tải lên ảnh chính mới</label>
                    <input type="file" class="form-control @error('main_image_file') is-invalid @enderror"
                           id="main_image_file" name="main_image_file" accept="image/*">
                    @error('main_image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="main_image_url" class="form-label">Hoặc URL ảnh chính</label>
                    <input type="url" class="form-control @error('main_image_url') is-invalid @enderror"
                           id="main_image_url" name="main_image_url"
                           value="{{ old('main_image_url', (str_starts_with(optional(($product->images->firstWhere('is_primary', true) ?? $product->images->first()))->image_url ?? '', 'http') ? (optional(($product->images->firstWhere('is_primary', true) ?? $product->images->first()))->image_url ?? '') : '')) }}"
                           placeholder="https://...">
                    @error('main_image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <hr>
                <h6 class="mb-2">Ảnh phụ</h6>
                <div class="mb-3">
                    <label for="images_files" class="form-label">Tải lên ảnh phụ mới (Các ảnh cũ sẽ bị xóa)</label>
                    <input type="file" class="form-control @error('images_files.*') is-invalid @enderror"
                           id="images_files" name="images_files[]" accept="image/*" multiple>
                    @error('images_files.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Thêm ảnh phụ mới bằng URL (Các ảnh cũ sẽ bị xóa)</label>
                    <div id="images-urls-container">
                        <div class="input-group mb-2">
                            <input type="url" class="form-control" name="images_urls[]">
                            <button type="button" class="btn btn-danger remove-image-url-btn">Xóa</button>
                        </div>
                    </div>
                    <button type="button" id="add-image-url-btn" class="btn btn-sm btn-primary mt-2">Thêm URL ảnh phụ</button>
                </div>
            </div>
        </div>

        {{-- Biến thể --}}
        <div class="card mb-4 card-soft">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Biến thể sản phẩm</h5>
                <button type="button" class="btn btn-success btn-sm" id="add-variant-btn">Thêm biến thể</button>
            </div>
            <div class="card-body">
                <div id="variants-container"></div>

                <template id="variant-template">
                    <div class="card mb-3 variant-item" data-index="INDEX">
                        <div class="card-body">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-danger btn-sm remove-variant-btn">Xóa biến thể</button>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Mã biến thể (SKU) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="variants[INDEX][sku]" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cân nặng (gram) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="variants[INDEX][weight]" min="0" value="200" required placeholder="ví dụ: 200">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="variants[INDEX][stock]" min="0" required placeholder="ví dụ: 100">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thuộc tính</label>
                                <div class="attributes-container"></div>
                                <button type="button" class="btn btn-outline-secondary btn-sm add-attribute-btn mt-2">Thêm thuộc tính</button>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giá <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="variants[INDEX][price]" min="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giá khuyến mãi</label>
                                    <input type="number" class="form-control" name="variants[INDEX][sale_price]" min="0">
                                </div>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="variants[INDEX][is_main_variant]" value="0">
                                <input class="form-check-input is-main-variant-checkbox" type="checkbox" name="variants[INDEX][is_main_variant]" value="1" id="is_main_variant_INDEX">
                                <label class="form-check-label" for="is_main_variant_INDEX">Là biến thể chính</label>
                            </div>
                        </div>
                    </div>
                </template>

                <template id="attribute-template">
                    <div class="input-group mb-2 attribute-row">
                        <input type="text" class="form-control" name="variants[VARIANT_INDEX][attributes][ATTR_INDEX][name]" placeholder="Tên thuộc tính (ví dụ: Màu sắc)" required>
                        <input type="text" class="form-control" name="variants[VARIANT_INDEX][attributes][ATTR_INDEX][value]" placeholder="Giá trị (ví dụ: Đỏ)" required>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-attribute-btn">Xóa</button>
                    </div>
                </template>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
            <a href="{{ route($rp.'.products.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<style>.ck-editor__editable { min-height: 260px; }</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    ClassicEditor.create(document.querySelector('#description'), {
        toolbar: [
            'heading', '|', 'bold', 'italic', 'underline', 'link',
            '|', 'bulletedList', 'numberedList', 'blockQuote',
            '|', 'insertTable', 'imageUpload', 'mediaEmbed',
            '|', 'undo', 'redo'
        ],
        simpleUpload: { uploadUrl: '{{ route('uploads.ckeditor') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }
    }).catch(console.error);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ẢNH PHỤ */
    const urlContainer = document.getElementById('images-urls-container');
    document.getElementById('add-image-url-btn').addEventListener('click', function() {
        urlContainer.insertAdjacentHTML('beforeend',
            `<div class="input-group mb-2">
                <input type="url" class="form-control" name="images_urls[]">
                <button type="button" class="btn btn-danger remove-image-url-btn">Xóa</button>
            </div>`
        );
    });
    urlContainer.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-image-url-btn')) {
            if (urlContainer.querySelectorAll('.input-group').length > 1) {
                e.target.closest('.input-group').remove();
            }
        }
    });

    /* BIẾN THỂ */
    const variantsContainer = document.getElementById('variants-container');
    const addVariantBtn = document.getElementById('add-variant-btn');
    const variantTemplate = document.getElementById('variant-template');
    const attributeTemplate = document.getElementById('attribute-template');
    let variantIndexCounter = 0;

    function createNewVariant(data = {}) {
        const newVariantNode = variantTemplate.content.cloneNode(true).firstElementChild;
        const variantIndex = variantIndexCounter++;
        newVariantNode.dataset.index = variantIndex;
        newVariantNode.innerHTML = newVariantNode.innerHTML.replace(/INDEX/g, variantIndex);
        newVariantNode.querySelector('[name$="[sku]"]').value = data.sku || '';
        newVariantNode.querySelector('[name$="[weight]"]').value = data.weight || 200;
        newVariantNode.querySelector('[name$="[stock]"]').value = data.stock || 0;
        newVariantNode.querySelector('[name$="[price]"]').value = data.price || '';
        newVariantNode.querySelector('[name$="[sale_price]"]').value = data.sale_price || '';
        const isMainCheckbox = newVariantNode.querySelector('.is-main-variant-checkbox');
        isMainCheckbox.checked = data.is_main_variant == 1;

        const attributesContainer = newVariantNode.querySelector('.attributes-container');
        const attributes = data.attributes || {};
        if (Object.keys(attributes).length > 0) {
            let attrIndex = 0;
            for (const name in attributes) {
                const value = attributes[name];
                createNewAttribute(attributesContainer, variantIndex, attrIndex++, { name, value });
            }
        } else {
            createNewAttribute(attributesContainer, variantIndex, 0);
        }
        variantsContainer.appendChild(newVariantNode);
        updateMainVariantLogic();
    }

    function createNewAttribute(container, variantIdx, attrIdx, data = {}) {
        const newAttrNode = attributeTemplate.content.cloneNode(true).firstElementChild;
        newAttrNode.innerHTML = newAttrNode.innerHTML
            .replace(/VARIANT_INDEX/g, variantIdx)
            .replace(/ATTR_INDEX/g, attrIdx);
        if (data.name) newAttrNode.querySelector('[name$="[name]"]').value = data.name;
        if (data.value) newAttrNode.querySelector('[name$="[value]"]').value = data.value;
        container.appendChild(newAttrNode);
    }

    function updateMainVariantLogic() {
        const checkboxes = variantsContainer.querySelectorAll('.is-main-variant-checkbox');
        checkboxes.forEach(chk => {
            chk.onclick = () => {
                if (chk.checked) {
                    checkboxes.forEach(other => { if (other !== chk) other.checked = false; });
                } else {
                    if ([...checkboxes].filter(c => c.checked).length === 0) chk.checked = true;
                }
            };
        });
        if ([...checkboxes].filter(c => c.checked).length === 0 && checkboxes.length > 0) {
            checkboxes[0].checked = true;
        }
    }

    addVariantBtn.addEventListener('click', () => createNewVariant());
    variantsContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-variant-btn')) {
            if (variantsContainer.children.length > 1) {
                e.target.closest('.variant-item').remove();
                updateMainVariantLogic();
            } else {
                alert('Sản phẩm phải có ít nhất một biến thể.');
            }
        }
        if (e.target.classList.contains('add-attribute-btn')) {
            const variantItem = e.target.closest('.variant-item');
            const variantIdx = variantItem.dataset.index;
            const attributesContainer = variantItem.querySelector('.attributes-container');
            const attrIdx = attributesContainer.children.length;
            createNewAttribute(attributesContainer, variantIdx, attrIdx);
        }
        if (e.target.classList.contains('remove-attribute-btn')) {
            const attributeRow = e.target.closest('.attribute-row');
            const attributesContainer = attributeRow.parentElement;
            if (attributesContainer.children.length > 1) {
                attributeRow.remove();
            } else {
                alert('Mỗi biến thể phải có ít nhất một thuộc tính.');
            }
        }
    });

    function renderInitialVariants() {
        const initialVariants = @json(old('variants', $product->variants->toArray()));
        if (initialVariants && initialVariants.length > 0) {
            initialVariants.forEach(variantData => createNewVariant(variantData));
        } else {
            createNewVariant();
        }
    }
    renderInitialVariants();

    /* DANH MỤC DẠNG CÂY */
    const allCats = @json($categories->map(fn($c)=>[
        'id'=>$c->id,'name'=>$c->name,'parent_id'=>$c->parent_id ?? 0
    ]));
    const preselected = new Set(@json($selectedCats));
    const hiddenSelect = document.getElementById('categories');
    const treeWrap = document.getElementById('cat-tree');
    const searchBox = document.getElementById('cat-search');
    const summaryEl = document.getElementById('cat-summary-text');

    const byParent = {};
    allCats.forEach(c => {
        const p = c.parent_id || 0;
        (byParent[p] ??= []).push(c);
    });
    Object.values(byParent).forEach(list => list.sort((a,b)=>a.name.localeCompare(b.name,'vi')));

    function makeNode(cat){
        const hasChild = !!(byParent[cat.id] && byParent[cat.id].length);
        const wrap = document.createElement('div');
        wrap.className = 'cat-node';
        wrap.dataset.id = cat.id;

        const row = document.createElement('div');
        row.className = 'd-flex align-items-center gap-2 py-1';
        row.innerHTML = `
            ${hasChild ? `<button type="button" class="toggle" aria-label="Mở/đóng"><i class="bi bi-caret-down-fill"></i></button>` : `<span style="width:28px"></span>`}
            <input class="form-check-input cat-check" type="checkbox" value="${cat.id}" id="cat_${cat.id}">
            <label class="form-check-label flex-grow-1" for="cat_${cat.id}">${cat.name}</label>
        `;
        wrap.appendChild(row);

        if (hasChild){
            const children = document.createElement('div');
            children.className = 'children';
            byParent[cat.id].forEach(ch => children.appendChild(makeNode(ch)));
            wrap.appendChild(children);
        }
        return wrap;
    }

    function buildTree(){
        treeWrap.innerHTML = '';
        const roots = byParent[0] || byParent[null] || [];
        roots.forEach(r => treeWrap.appendChild(makeNode(r)));

        preselected.forEach(id => {
            const cb = treeWrap.querySelector(`#cat_${id}`);
            if(cb){ cb.checked = true; }
        });

        refreshSummary();
        syncHiddenSelect();

        treeWrap.addEventListener('click', function(e){
            if(e.target.closest('.toggle')){
                const node = e.target.closest('.cat-node');
                const child = node.querySelector(':scope > .children');
                if(child){
                    child.classList.toggle('d-none');
                    const icon = node.querySelector('.toggle i');
                    icon && icon.classList.toggle('bi-caret-right-fill');
                    icon && icon.classList.toggle('bi-caret-down-fill');
                }
            }
        });

        treeWrap.addEventListener('change', function(e){
            if(e.target.classList.contains('cat-check')){
                const id = e.target.value;
                if(e.target.checked){ preselected.add(id); }
                else{ preselected.delete(id); }
                refreshSummary();
                syncHiddenSelect();
            }
        });
    }

    function syncHiddenSelect(){
        [...hiddenSelect.options].forEach(opt => { opt.selected = false; });
        preselected.forEach(id => {
            const opt = [...hiddenSelect.options].find(o => String(o.value) === String(id));
            if(opt){ opt.selected = true; }
            else{
                const cat = allCats.find(c => String(c.id) === String(id));
                if(cat){
                    const o = new Option(cat.name, cat.id, true, true);
                    hiddenSelect.add(o);
                }
            }
        });
    }

    function refreshSummary(){
        if(preselected.size === 0){
            summaryEl.textContent = 'Chọn danh mục…';
            return;
        }
        const picked = allCats.filter(c => preselected.has(String(c.id)) || preselected.has(c.id));
        if(picked.length <= 3){
            summaryEl.textContent = picked.map(x=>x.name).join(', ');
        }else{
            summaryEl.textContent = `Đã chọn ${picked.length} danh mục`;
        }
    }

    searchBox.addEventListener('input', function(){
        const kw = this.value.trim().toLowerCase();
        if(!kw){
            treeWrap.querySelectorAll('.cat-node').forEach(n => n.classList.remove('d-none'));
            return;
        }
        const idMatch = new Set();
        allCats.forEach(c => {
            if((c.name||'').toLowerCase().includes(kw)) idMatch.add(String(c.id));
        });
        treeWrap.querySelectorAll('.cat-node').forEach(n => n.classList.add('d-none'));
        function showChain(id){
            const node = treeWrap.querySelector(`.cat-node[data-id="${id}"]`);
            if(!node) return;
            node.classList.remove('d-none');
            const parentNode = node.parentElement?.closest('.cat-node');
            if(parentNode){
                const children = parentNode.querySelector(':scope > .children');
                if(children && children.classList.contains('d-none')){
                    children.classList.remove('d-none');
                    const icon = parentNode.querySelector('.toggle i');
                    icon && icon.classList.remove('bi-caret-right-fill');
                    icon && icon.classList.add('bi-caret-down-fill');
                }
                showChain(parentNode.dataset.id);
            }
        }
        idMatch.forEach(showChain);
    });

    buildTree();
});
</script>
@endpush
