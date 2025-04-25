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
        @method('POST')
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="form-group">
            <label>Product Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="" value="{{ old('name', $product->name) }}" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category_id" class="">
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
            <textarea name="description" class="">{{ old('description', $product->description) }}</textarea>
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
                <!-- Hidden input field to store removed variant IDs -->
                <input type="tes" id="remove_variation_ids" name="remove_variation_ids" value="">
                @foreach ($product->variants as $index => $variant)
                    <tr class="variant-item" data-id="{{ $variant->id }}">
                        <td>
                            @foreach ($attributes as $attribute)
                                <select name="variants[{{ $index }}][attribute_value_ids][]" class=" mb-1" required >
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
                            <input type="hidden" name="variants[{{ $index }}][id]" class="" value="{{ $variant->id }}">
                            <input type="number" name="variants[{{ $index }}][price]" class="" value="{{ $variant->price }}" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" name="variants[{{ $index }}][stock]" class="" value="{{ $variant->stock }}" required>
                        </td>
                        <td>
                            <div class="form-group">
                                <label for="variant-{{ $index }}-images" class="custom-file-upload">
                                    <i class="fas fa-image"></i>
                                </label>
                                <input 
                                    type="file" 
                                    name="variants[{{ $index }}][images][]" 
                                    multiple 
                                    class="form-control variant-image-input" 
                                    data-index="{{ $index }}" 
                                    id="variant-{{ $index }}-images"
                                >

                                <div class="mt-2" id="image-preview-{{ $index }}" class="new-preview">
                                    {{-- Existing images from DB --}}
                                    @foreach ($variant->images as $image)
                                        <div class="d-inline-block position-relative m-1 existing-image" data-image-id="{{ $image->id }}">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" width="80" class="img-thumbnail">
                                            <span class="btn btn-sm btn-danger position-absolute remove-existing-image" style="top:0;right:0;">&times;</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="deleted-images-container-{{ $index }}"></div>
                            </div>

                        </td>
                        <td class="variant-buttons text-center"></td>
                    </tr>
                    <!-- Hidden Template Row -->
                    <template id="variant-template">
                        <tr class="variant-item" data-id="{{ $variant->id }}">
                            <td>
                                @foreach ($attributes as $attribute)
                                    <select name="variants[__INDEX__][attribute_value_ids][]" class="mb-1" required>
                                        <option value="">-- Select {{ $attribute->name }} --</option>
                                        @foreach ($attribute->values as $value)
                                            <option value="{{ $value->id }}">{{ $value->value }}</option>
                                        @endforeach
                                    </select>
                                @endforeach
                            </td>
                            <td>
                                <input type="number" name="variants[__INDEX__][price]" class="" value="" step="0.01" required>
                            </td>
                            <td>
                                <input type="number" name="variants[__INDEX__][stock]" class="" value="" required>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="variant-__INDEX__-images" class="custom-file-upload">
                                        <i class="fas fa-image"></i>
                                    </label>
                                    <input 
                                        type="file" 
                                        name="variants[__INDEX__][images][]" 
                                        multiple 
                                        class="form-control variant-image-input" 
                                        data-index="__INDEX__" 
                                        id="variant-__INDEX__-images"
                                    >
                                    <div class="mt-2" id="image-preview-__INDEX__"></div>
                                    <!-- <div class="mt-2" id="image-preview-__INDEX__"></div> -->
                                    <!-- <div id="deleted-images-container-__INDEX__"></div> -->
                                </div>
                            </td>
                            <td class="variant-buttons text-center"></td>
                        </tr>
                    </template>

                @endforeach

            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>

<script>
    function updateButtons() {
        const container = document.getElementById('variants-container');
        const rows = container.querySelectorAll('.variant-item');
        rows.forEach((row, index) => {
            const buttonCell = row.querySelector('.variant-buttons');
            buttonCell.innerHTML = '';
            if (index === rows.length - 1) {
                buttonCell.innerHTML += `
                    <button type="button" class="btn btn-success btn-sm add-variant">+</button>
                    <button type="button" class="btn btn-danger btn-sm remove-variant">-</button>
                `;
            } else {
                buttonCell.innerHTML += `
                    <button type="button" class="btn btn-danger btn-sm remove-variant">-</button>
                `;
            }
        });
    }

    // Function to update the hidden input field with removed variant IDs
    function updateRemoveVariationIds(variantId) {
        const removeVariationIdsInput = document.getElementById('remove_variation_ids');
        let removedIds = removeVariationIdsInput.value ? removeVariationIdsInput.value.split(',') : [];
        
        // Add the variant ID to the list if not already added
        if (!removedIds.includes(variantId)) {
            removedIds.push(variantId);
        }
        
        // Update the value of the hidden input
        removeVariationIdsInput.value = removedIds.join(',');
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initial buttons update
        updateButtons();
        const variantFileMap = new Map();

        // Dynamically handle file input for all variants, including newly added ones
        document.querySelectorAll('.variant-image-input').forEach(function (input) {
            const index = input.dataset.index;
            variantFileMap.set(index, []);
            console.log(input,  "input");

            input.addEventListener('change', function () {
                const previewContainer = document.getElementById('image-preview-' + index);
                console.log(previewContainer, "previewContainer");

                const files = Array.from(input.files);
                variantFileMap.set(index, files);

                files.forEach((file, i) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const preview = document.createElement('div');
                        preview.className = 'd-inline-block m-1 new-preview position-relative';
                        preview.dataset.fileIndex = i;

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.width = 80;
                        img.className = 'img-thumbnail';

                        const removeBtn = document.createElement('span');
                        removeBtn.innerHTML = '&times;';
                        removeBtn.className = 'btn btn-sm btn-danger position-absolute';
                        removeBtn.style = 'top:0;right:0;';
                        removeBtn.addEventListener('click', function () {
                            const updatedFiles = variantFileMap.get(index).filter((_, fi) => fi !== i);
                            variantFileMap.set(index, updatedFiles);
                            preview.remove();
                        });

                        preview.appendChild(img);
                        preview.appendChild(removeBtn);
                        previewContainer.appendChild(preview);
                    };
                    reader.readAsDataURL(file);
                });
            });
        });

        // Remove existing image from DOM + track in hidden input
        document.querySelectorAll('.remove-existing-image').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const parent = this.closest('.existing-image');
                const imageId = parent.dataset.imageId;
                const variantIndex = parent.closest('[id^="image-preview-"]').id.split('-').pop();

                // Add hidden input to track deletion
                const container = document.getElementById('deleted-images-container-' + variantIndex);
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `variants[${variantIndex}][deleted_image_ids][]`;
                input.value = imageId;
                container.appendChild(input);

                // Remove image from preview
                parent.remove();
            });
        });

        const container = document.getElementById('variants-container');
        const template = document.getElementById('variant-template').innerHTML;
        let variantIndex = document.querySelectorAll('.variant-item').length;

        container.addEventListener('click', function (e) {
            // ADD VARIANT
            if (e.target.classList.contains('add-variant')) {
                const newRow = template.replace(/__INDEX__/g, variantIndex);
                container.insertAdjacentHTML('beforeend', newRow);
                variantIndex++;

                // Re-bind file input handler for the new row
                const newFileInput = container.querySelector(`.variant-image-input[data-index="${variantIndex - 1}"]`);
                if (newFileInput) {
                    const newIndex = newFileInput.dataset.index;
                    variantFileMap.set(newIndex, []);
                    newFileInput.addEventListener('change', function () {
                        const previewContainer = document.getElementById('image-preview-' + newIndex);
                        const files = Array.from(newFileInput.files);
                        variantFileMap.set(newIndex, files);

                        files.forEach((file, i) => {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                const preview = document.createElement('div');
                                preview.className = 'd-inline-block m-1 new-preview position-relative';
                                preview.dataset.fileIndex = i;

                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.width = 80;
                                img.className = 'img-thumbnail';

                                const removeBtn = document.createElement('span');
                                removeBtn.innerHTML = '&times;';
                                removeBtn.className = 'btn btn-sm btn-danger position-absolute';
                                removeBtn.style = 'top:0;right:0;';
                                removeBtn.addEventListener('click', function () {
                                    const updatedFiles = variantFileMap.get(newIndex).filter((_, fi) => fi !== i);
                                    variantFileMap.set(newIndex, updatedFiles);
                                    preview.remove();
                                });

                                preview.appendChild(img);
                                preview.appendChild(removeBtn);
                                previewContainer.appendChild(preview);
                            };
                            reader.readAsDataURL(file);
                        });
                    });
                }

                updateButtons();
            }

            // REMOVE VARIANT
            if (e.target.classList.contains('remove-variant')) {
                const row = e.target.closest('.variant-item');
                row.remove();
                updateButtons();

                // Get the variant ID from the hidden input field within the row
                const variantId = row.getAttribute('data-id');
                // Optionally: Add the removed ID to a hidden input for form submission
                updateRemoveVariationIds(variantId);
            }
        });

        // Initial button setup
        updateButtons();
    });
</script>


@endsection
