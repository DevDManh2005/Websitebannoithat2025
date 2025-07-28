@extends('admins.layouts.app')

@section('title', 'Tạo sản phẩm')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tạo sản phẩm</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Tạo sản phẩm
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên sản phẩm</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug (URL)</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                       id="slug" name="slug" value="{{ old('slug') }}">
                                @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                        id="category_id" name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="brand_id" class="form-label">Thương hiệu</label>
                                <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id"
                                        name="brand_id">
                                    <option value="">Chọn thương hiệu</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Nhà cung cấp</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror"
                                        id="supplier_id" name="supplier_id">
                                    <option value="">Chọn nhà cung cấp</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description"
                                          rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="label" class="form-label">Nhãn</label>
                                <input type="text" class="form-control @error('label') is-invalid @enderror"
                                       id="label" name="label" value="{{ old('label') }}">
                                @error('label')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Hiển thị
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured"
                                           name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Nổi bật
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ảnh chính</label>
                                <div class="input-group">
                                    <input type="file" class="form-control @error('main_image_file') is-invalid @enderror"
                                           id="main_image_file" name="main_image_file" accept="image/*">
                                    <label class="input-group-text" for="main_image_file">Tải lên</label>
                                </div>
                                @error('main_image_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Hoặc nhập URL</div>
                                <input type="url" class="form-control @error('main_image_url') is-invalid @enderror"
                                       id="main_image_url" name="main_image_url" value="{{ old('main_image_url') }}">
                                @error('main_image_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ảnh phụ</label>
                                <div class="input-group">
                                    <input type="file" class="form-control @error('images_files.*') is-invalid @enderror"
                                           id="images_files" name="images_files[]" accept="image/*" multiple>
                                    <label class="input-group-text" for="images_files">Tải lên</label>
                                </div>
                                @error('images_files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Hoặc nhập các URL (mỗi URL trên một dòng)</div>
                                <div id="images-urls-container">
                                    <div class="input-group mb-2">
                                        <input type="url" class="form-control" name="images_urls[]"
                                               value="{{ old('images_urls.0') }}">
                                        <button type="button" class="btn btn-danger remove-image-url">Xóa</button>
                                    </div>
                                </div>
                                <button type="button" id="add-image-url" class="btn btn-sm btn-primary mt-2">Thêm URL ảnh phụ</button>
                            </div>

                           <hr>
 
                            <div class="card mb-3">
                                <div class="card-header">
                                    Biến thể sản phẩm
                                </div>
                                <div class="card-body">
                                    <div id="variants-container">
                                        <div class="variant-group mb-3" data-index="0">
                                            <div class="mb-3">
                                                <label class="form-label">SKU</label>
                                                <input type="text" class="form-control @error('variants.0.sku') is-invalid @enderror"
                                                       name="variants[0][sku]" value="{{ old('variants.0.sku') }}" required>
                                                @error('variants.0.sku')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Thuộc tính</label>
                                                <div id="attributes-container-0">
                                                    <div class="input-group mb-2 attribute-row">
                                                        <input type="text" class="form-control @error('variants.0.attributes.0.name') is-invalid @enderror"
                                                               name="variants[0][attributes][0][name]" placeholder="Tên thuộc tính" required>
                                                        <input type="text" class="form-control @error('variants.0.attributes.0.value') is-invalid @enderror"
                                                               name="variants[0][attributes][0][value]" placeholder="Giá trị" required>
                                                        <button type="button" class="btn btn-danger btn-sm remove-attribute">Xóa</button>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary mt-1 add-attribute" data-variant-index="0">Thêm thuộc tính</button>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Giá</label>
                                                <input type="number" class="form-control @error('variants.0.price') is-invalid @enderror"
                                                       name="variants[0][price]" value="{{ old('variants.0.price') }}" min="0" step="0.01" required>
                                                @error('variants.0.price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Giá khuyến mãi</label>
                                                <input type="number" class="form-control @error('variants.0.sale_price') is-invalid @enderror"
                                                       name="variants[0][sale_price]" value="{{ old('variants.0.sale_price') }}" min="0" step="0.01">
                                                @error('variants.0.sale_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="variants[0][is_main_variant]"
                                                           value="1" {{ old('variants.0.is_main_variant') ? 'checked' : '' }}>
                                                    <label class="form-check-label">Biến thể chính</label>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa biến thể</button>
                                        </div>
                                    </div>
                                    <button type="button" id="add-variant" class="btn btn-sm btn-primary mt-2">Thêm biến thể</button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Tạo sản phẩm</button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Bootstrap 5 Custom File Input fix
            $(document).on('change', '.form-control[type="file"]', function (e) {
                let files = e.target.files;
                let fileNames = Array.from(files).map(file => file.name).join(', ');
                $(this).next('.input-group-text').text(fileNames || 'Tải lên');
            });

            // Xử lý thêm/xóa URL ảnh phụ
            $('#add-image-url').click(function () {
                let container = $('#images-urls-container');
                let index = container.children().length;
                let newInputGroup = $('<div>').addClass('input-group mb-2');
                let newInput = $('<input>').attr({
                    type: 'url',
                    name: 'images_urls[' + index + ']',
                    class: 'form-control'
                });
                let removeButton = $('<button>').attr({
                    type: 'button',
                    class: 'btn btn-danger remove-image-url'
                }).text('Xóa');
                newInputGroup.append(newInput, removeButton);
                container.append(newInputGroup);
            });

            $(document).on('click', '.remove-image-url', function () {
                $(this).closest('.input-group').remove();
            });

            // Xử lý thêm/xóa biến thể
            let variantIndex = 1;
            $('#add-variant').click(function () {
                let newVariant = `
                    <div class="variant-group mb-3" data-index="${variantIndex}">
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" name="variants[${variantIndex}][sku]" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thuộc tính</label>
                            <div id="attributes-container-${variantIndex}">
                                <div class="input-group mb-2 attribute-row">
                                    <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][name]" placeholder="Tên thuộc tính" required>
                                    <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][value]" placeholder="Giá trị" required>
                                    <button type="button" class="btn btn-danger btn-sm remove-attribute">Xóa</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary mt-1 add-attribute" data-variant-index="${variantIndex}">Thêm thuộc tính</button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <input type="number" class="form-control" name="variants[${variantIndex}][price]" min="0" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá khuyến mãi</label>
                            <input type="number" class="form-control" name="variants[${variantIndex}][sale_price]" min="0" step="0.01">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="variants[${variantIndex}][is_main_variant]" value="1">
                                <label class="form-check-label">Biến thể chính</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-variant">Xóa biến thể</button>
                    </div>`;
                $('#variants-container').append(newVariant);
                variantIndex++;
            });

            $(document).on('click', '.remove-variant', function () {
                $(this).closest('.variant-group').remove();
            });

            // Xử lý thêm/xóa thuộc tính trong biến thể
            $(document).on('click', '.add-attribute', function () {
                let variantIndex = $(this).data('variant-index');
                let container = $(`#attributes-container-${variantIndex}`);
                let attrIndex = container.find('.attribute-row').length;
                let newAttrRow = `
                    <div class="input-group mb-2 attribute-row">
                        <input type="text" class="form-control" name="variants[${variantIndex}][attributes][${attrIndex}][name]" placeholder="Tên thuộc tính" required>
                        <input type="text" class="form-control" name="variants[${variantIndex}][attributes][${attrIndex}][value]" placeholder="Giá trị" required>
                        <button type="button" class="btn btn-danger btn-sm remove-attribute">Xóa</button>
                    </div>`;
                container.append(newAttrRow);
            });

            $(document).on('click', '.remove-attribute', function () {
                $(this).closest('.attribute-row').remove();
            });
        });
    </script>
@endpush