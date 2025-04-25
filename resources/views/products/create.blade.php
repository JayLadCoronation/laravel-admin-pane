@extends('layouts.app')

@section('content')
<div class="container">
    <div class="three">
        <h1>Create Product</h1>
    </div>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Product Creation Form --}}
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="name">Product Name<span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="">
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group mt-3">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="">{{ old('description') }}</textarea>
        </div>
        {{-- Variants Section --}}
        <div class="form-group mt-5">
            <label>Variants</label>
            
            <table class="table table-bordered" id="variant-table">
                <thead>
                    <tr>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Images</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="variants-container">
                    @foreach ($attributes as $index => $attribute)
                    <tr class="variant-item">
                        <td>
                            <select name="variants[0][attribute_value_ids][]" class="" required>
                                <option value="">-- Select {{ $attribute->name }} --</option>
                                @foreach ($attribute->values as $value)
                                    <option value="{{ $value->id }}">{{ $value->value }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="variants[0][price]" class="" step="0.01" required placeholder="Price">
                        </td>
                        <td>
                            <input type="number" name="variants[0][stock]" class="" required placeholder="Stock">
                        </td>
                        <td>
                            <div class="">
                                <!-- <input type="file" name="variants[0][images][]" multiple class="form-control"> -->
                                <!-- <input type="file" name="variants[{{ $index }}][images][]" 
                                    multiple 
                                    class="form-control variant-image-input" 
                                    data-index="{{ $index }}"> -->
                                    <!-- <label for="variant-{{ $index }}-images" class="custom-file-upload">
                                        <i class="fas fa-upload"></i><br>
                                        Click or Drag to Upload Images
                                    </label>
                                    <input 
                                        type="file" 
                                        name="variants[{{ $index }}][images][]" 
                                        multiple 
                                        class="form-control variant-image-input" 
                                        data-index="{{ $index }}" 
                                        id="variant-{{ $index }}-images"
                                    > -->
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
                                <div class="image-preview mt-2" id="image-preview-{{ $index }}"></div>
                            </div>
                        </td>
                        <td class="variant-buttons text-center">
                            <!-- Buttons injected by JS -->
                        </td>
                        
                    </tr>
                    
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">Create Product</button>
        </div>
    </form>
</div>

{{-- JavaScript for Dynamic Variant Management --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let variantIndex = 1;
    const container = document.getElementById('variants-container');
    const addBtnHtml = `<button type="button" class="btn btn-sm btn-success add-variant">+</button>`;
    const removeBtnHtml = `<button type="button" class="btn btn-sm btn-danger remove-variant">-</button>`;

    function updateButtons() {
        const items = container.querySelectorAll('.variant-item');
        items.forEach((row, index) => {
            const buttonCell = row.querySelector('.variant-buttons');
            buttonCell.innerHTML = ''; // Clear old buttons

            if (index === items.length - 1) {
                // Last row: show both add and remove
                buttonCell.innerHTML = addBtnHtml + ' ' + removeBtnHtml;
            } else {
                // Other rows: show only remove
                buttonCell.innerHTML = removeBtnHtml;
            }
        });
    }

    // Event delegation for add/remove buttons
    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-variant')) {
            const lastRow = container.querySelector('.variant-item:last-child');
            const newRow = lastRow.cloneNode(true);

            newRow.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, `[${variantIndex}]`);
                    input.setAttribute('name', newName);
                    input.value = '';
                }
            });

            container.appendChild(newRow);
            variantIndex++;
            updateButtons();
        }

        if (e.target.classList.contains('remove-variant')) {
            const rows = container.querySelectorAll('.variant-item');
            if (rows.length > 1) {
                e.target.closest('.variant-item').remove();
                updateButtons();
            } else {
                alert('At least one variant is required.');
            }
        }
    });

    updateButtons(); // initial call
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.variant-image-input').forEach(input => {
        input.addEventListener('change', function (e) {
            const index = this.getAttribute('data-index');
            const previewContainer = document.getElementById(`image-preview-${index}`);
            previewContainer.innerHTML = ''; // Clear previous previews

            const files = Array.from(this.files);
            if (!files.length) return;

            files.forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const imgDiv = document.createElement('div');
                    imgDiv.classList.add('d-inline-block', 'position-relative', 'm-1');

                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.width = 80;
                    img.classList.add('img-thumbnail-product-upload');

                    const removeBtn = document.createElement('span');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.classList.add('img-remove', 'position-absolute');
                    removeBtn.style.top = '0';
                    removeBtn.style.right = '0';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.title = 'Remove';

                    removeBtn.onclick = () => {
                        // Remove the file from input
                        const dt = new DataTransfer();
                        files.forEach((f, j) => {
                            if (j !== i) dt.items.add(f);
                        });
                        input.files = dt.files;

                        // Remove the preview image
                        imgDiv.remove();

                        // If all previews removed, also clear input
                        if (dt.files.length === 0) {
                            input.value = '';
                        }
                    };

                    imgDiv.appendChild(img);
                    imgDiv.appendChild(removeBtn);
                    previewContainer.appendChild(imgDiv);
                };
                reader.readAsDataURL(file);
            });
        });
    });
});
</script>





@endpush
@endsection
