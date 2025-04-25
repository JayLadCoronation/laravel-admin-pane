@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Update Product</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="form-control">
                <option value="">-- Select Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
        </div>

        <hr>
        <h4>Variants</h4>
        <table class="table table-bordered" id="variant-table">
            <thead>
                <tr>
                    <th>Attributes</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Images</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody id="variants-container">
                @foreach ($product->variants as $index => $variant)
                    <tr class="variant-item" data-id="{{ $variant->id }}">
                        <td>
                            @foreach ($attributes as $attribute)
                                <select name="variants[{{ $index }}][attribute_value_ids][]" class="form-control mb-1" required>
                                    <option value="">-- Select {{ $attribute->name }} --</option>
                                    @foreach ($attribute->values as $value)
                                        <option value="{{ $value->id }}"
                                            {{ in_array($value->id, old("variants.{$index}.attributes", $variant->attributeValues->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $value->value }}
                                        </option>
                                    @endforeach
                                </select>
                            @endforeach
                        </td>
                        <td>
                            <input type="number" name="variants[{{ $index }}][price]" class="form-control" value="{{ $variant->price }}" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" name="variants[{{ $index }}][stock]" class="form-control" value="{{ $variant->stock }}" required>
                        </td>
                        <td>
                            <div class="form-group">
                                <label for="variant-image-{{ $index }}">Upload Images</label>
                                <div class="d-flex align-items-center">
                                    <label class="btn btn-primary btn-sm mb-0">
                                        <i class="fa fa-upload"></i> Upload
                                        <input type="file" name="variant_images[{{ $index }}][]" id="variant-image-{{ $index }}" multiple hidden onchange="previewImages(this, {{ $index }})">
                                    </label>
                                </div>

                                <div class="mt-2" id="image-preview-{{ $index }}">
                                    {{-- Existing images from DB --}}
                                    @foreach ($variant->images as $image)
                                        <div class="d-inline-block position-relative m-1 existing-image" data-image-id="{{ $image->id }}">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" width="80" class="img-thumbnail">
                                            <span class="btn btn-sm btn-danger position-absolute remove-existing-image" style="top:0;right:0;">&times;</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td class="variant-buttons text-center"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('variants-container');
    let variantIndex = {{ $product->variants->count() }};

    const addBtnHtml = `<button type="button" class="btn btn-success btn-sm add-variant">+</button>`;
    const removeBtnHtml = `<button type="button" class="btn btn-danger btn-sm remove-variant">-</button>`;

    function updateButtons() {
        const rows = container.querySelectorAll('.variant-item');
        rows.forEach((row, index) => {
            const buttonCell = row.querySelector('.variant-buttons');
            buttonCell.innerHTML = '';
            buttonCell.innerHTML += index === rows.length - 1 ? addBtnHtml + ' ' + removeBtnHtml : removeBtnHtml;
        });
    }

    container.addEventListener('click', function (e) {
        
        if (e.target.classList.contains('add-variant')) {
            alert();
            const lastRow = container.querySelector('.variant-item:last-child');
            const newRow = lastRow.cloneNode(true);

            newRow.querySelectorAll('input, select, textarea').forEach(el => {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace(/\[\d+\]/, `[${variantIndex}]`));
                    if (el.type !== 'select-one') el.value = '';
                }
            });

            newRow.querySelector('.variant-image-input').setAttribute('data-index', variantIndex);
            newRow.querySelector(`#image-preview-${variantIndex - 1}`)?.remove();
            newRow.querySelector('.variant-image-input').insertAdjacentHTML('afterend', `<div class="mt-2" id="image-preview-${variantIndex}"></div>`);

            newRow.setAttribute('data-id', '');
            container.appendChild(newRow);
            variantIndex++;
            updateButtons();
        }

        if (e.target.classList.contains('remove-variant')) {
            const row = e.target.closest('.variant-item');
            const rows = container.querySelectorAll('.variant-item');

            if (rows.length > 1) {
                const id = row.dataset.id;
                if (id) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'remove_variant_ids[]';
                    input.value = id;
                    document.querySelector('form').appendChild(input);
                }
                row.remove();
                updateButtons();
            } else {
                alert('At least one variant is required.');
            }
        }

    });

    updateButtons();
});
</script>
<script>
function previewImages(input, index) {
    const previewDiv = document.getElementById('image-preview-' + index);
    previewDiv.innerHTML = ''; // clear previous previews (optional)

    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const imagePreview = document.createElement('div');
                imagePreview.className = 'd-inline-block position-relative m-1';
                imagePreview.innerHTML = `
                    <img src="${e.target.result}" width="80" class="img-thumbnail">
                `;
                previewDiv.appendChild(imagePreview);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>

@endpush
@endsection
