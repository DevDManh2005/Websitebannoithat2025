@extends('admins.layouts.app')

@section('title', 'Sửa sản phẩm: ' . $product->name)

@section('content')
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

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Thông tin cơ bản --}}
        <div class="card shadow mb-4">
            <div class="card-header">Thông tin cơ bản</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                        value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug (URL thân thiện)</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
                        value="{{ old('slug', $product->slug) }}" placeholder="Để trống để tự động tạo">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="categories" class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <select class="form-select @error('categories') is-invalid @enderror" id="categories" name="categories[]" multiple required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text">Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều danh mục.</small>
                    @error('categories')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="brand_id" class="form-label">Thương hiệu</label>
                    <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                        <option value="">Không có</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                    <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id"
                        name="supplier_id">
                        <option value="">Không có</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả sản phẩm</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                        name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="label" class="form-label">Nhãn (ví dụ: Mới, Hot, Giảm giá)</label>
                    <input type="text" class="form-control @error('label') is-invalid @enderror" id="label" name="label"
                        value="{{ old('label', $product->label) }}">
                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Kích hoạt sản phẩm</label>
                </div>
                <div class="form-check mb-3">
                    <input type="hidden" name="is_featured" value="0">
                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_featured">Sản phẩm nổi bật</label>
                </div>
            </div>
        </div>

        {{-- Quản lý hình ảnh --}}
        <div class="card mb-4">
            <div class="card-header">Hình ảnh sản phẩm</div>
            <div class="card-body">
                <h5>Ảnh chính</h5>
                <div class="mb-3">
                    @php
                        $mainImage = $product->images->where('is_primary', true)->first();
                        $mainImageUrl = $mainImage ? (Str::startsWith($mainImage->image_url, 'http') ? $mainImage->image_url : asset('storage/' . $mainImage->image_url)) : 'https://placehold.co/150x100?text=No+Image';
                    @endphp
                    <img src="{{ $mainImageUrl }}" alt="Ảnh chính" width="150" class="mb-2">
                    <label for="main_image_file" class="form-label">Upload ảnh chính mới</label>
                    <input type="file" class="form-control @error('main_image_file') is-invalid @enderror"
                        id="main_image_file" name="main_image_file">
                    @error('main_image_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="main_image_url" class="form-label">Hoặc URL ảnh chính</label>
                    <input type="url" class="form-control @error('main_image_url') is-invalid @enderror"
                        id="main_image_url" name="main_image_url"
                        value="{{ old('main_image_url', $mainImage && Str::startsWith($mainImage->image_url, 'http') ? $mainImage->image_url : '') }}"
                        placeholder="https://example.com/main.jpg">
                    @error('main_image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <hr>
                <h5>Ảnh phụ</h5>
                <div class="mb-3">
                    <label for="images_files" class="form-label">Tải lên ảnh phụ mới (Các ảnh cũ sẽ bị xóa)</label>
                    <input type="file" class="form-control @error('images_files.*') is-invalid @enderror"
                        id="images_files" name="images_files[]" multiple>
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

        {{-- Quản lý biến thể --}}
        <div class="card mb-4">
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
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="variants[INDEX][sku]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cân nặng (gram) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="variants[INDEX][weight]" min="0" value="200" required placeholder="ví dụ: 200">
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

        <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // SCRIPT CHO THÊM/XÓA URL ẢNH PHỤ
    const urlContainer = document.getElementById('images-urls-container');
    document.getElementById('add-image-url-btn').addEventListener('click', function() {
        const newField = `
            <div class="input-group mb-2">
                <input type="url" class="form-control" name="images_urls[]">
                <button type="button" class="btn btn-danger remove-image-url-btn">Xóa</button>
            </div>
        `;
        urlContainer.insertAdjacentHTML('beforeend', newField);
    });
    urlContainer.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-image-url-btn')) {
            if (urlContainer.querySelectorAll('.input-group').length > 1) {
                e.target.closest('.input-group').remove();
            }
        }
    });

    // SCRIPT CHO BIẾN THỂ
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

    function renderInitialData() {
        const initialVariants = @json(old('variants', $product->variants->toArray()));
        if (initialVariants && initialVariants.length > 0) {
            initialVariants.forEach(variantData => createNewVariant(variantData));
        } else {
            createNewVariant();
        }
    }
    renderInitialData();
});
</script>
@endpush
