@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Product</h1>

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
    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label for="name">Product Name<span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        {{-- Product Name --}}
        
        <!-- <div class="form-group">
            <label for="name">Product Name<span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div> -->

        {{-- Description --}}
        <div class="form-group mt-3">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        {{-- Category Selection --}}
        <!-- <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">-- Select Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div> -->

        {{-- Variants Section --}}
        <div class="form-group mt-5">
            <label>Variants</label>
            <div class="variant-table-header">
                <div>Color</div>
                <div>Price</div>
                <div>Stock</div>
                <div>Actions</div>
            </div>

            <div id="variants-container">
                <div class="variant-item">
                    <div class="variant-item-row">
                        {{-- Attribute Values --}}
                        @foreach ($attributes as $attribute)
                            <div class="form-group">
                                <label>{{ $attribute->name }}</label>
                                <select name="variants[0][attribute_value_ids][]" class="form-control" required>
                                    <option value="">-- Select {{ $attribute->name }} --</option>
                                    @foreach ($attribute->values as $value)
                                        <option value="{{ $value->id }}">{{ $value->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                        <input type="number" name="variants[0][price]" step="0.01" required placeholder="Price">

                        <input type="number" name="variants[0][stock]" required placeholder="Stock">

                        <div class="form-group col-md-3 variant-buttons">
                            <!-- JS injects buttons here -->
                        </div>

                        <!-- <button type="button" class="btn btn-danger remove-variant">-</button>
                        <button type="button" id="add-variant" class="btn btn-secondary mt-2">+ Add Variant</button> -->
                    </div>
                </div>
            </div>

            
            <button type="submit" class="btn btn-primary">Create Product</button>
        </div>
        <!-- <div class="form-group">
            <label>Variants</label>
            <div id="variants-container">
                {{-- Variant Template --}}
                <div class="variant-item border p-3 mb-3">
                    <h5>Variant</h5>

                    {{-- Attribute Values --}}
                    @foreach ($attributes as $attribute)
                        <div class="form-group">
                            <label>{{ $attribute->name }}</label>
                            <select name="variants[0][attribute_value_ids][]" class="form-control" required>
                                <option value="">-- Select {{ $attribute->name }} --</option>
                                @foreach ($attribute->values as $value)
                                    <option value="{{ $value->id }}">{{ $value->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    {{-- Price --}}
                    <div class="form-group">
                        <label>Price<span class="text-danger">*</span></label>
                        <input type="number" name="variants[0][price]" class="form-control" step="0.01" required>
                    </div>

                    {{-- Stock --}}
                    <div class="form-group">
                        <label>Stock<span class="text-danger">*</span></label>
                        <input type="number" name="variants[0][stock]" class="form-control" required>
                    </div>

                    {{-- Remove Variant Button --}}
                    <button type="button" class="btn btn-danger remove-variant">Remove Variant</button>
                </div>
            </div>

            {{-- Add Variant Button --}}
            <button type="button" id="add-variant" class="btn btn-secondary">Add Variant</button>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn btn-primary">Create Product</button> -->
    </form>
</div>

{{-- JavaScript for Dynamic Variant Management --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let variantIndex = 1;
        const container = document.getElementById('variants-container');
        const addButtonHtml = `<button type="button" class="btn btn-success add-variant">+</button>`;
        const removeButtonHtml = `<button type="button" class="btn btn-danger remove-variant">-</button>`;

        function updateVariantButtons() {
            const items = container.querySelectorAll('.variant-item');
            items.forEach((item, index) => {
                const btnContainer = item.querySelector('.variant-buttons');
                btnContainer.innerHTML = '';

                if (index === items.length - 1) {
                    // Last row gets both + and - buttons
                    btnContainer.innerHTML = addButtonHtml + ' ' + removeButtonHtml;
                } else {
                    // Other rows get only -
                    btnContainer.innerHTML = removeButtonHtml;
                }
            });
        }

        // Add Variant
        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-variant')) {
                const firstVariant = container.querySelector('.variant-item');
                const newVariant = firstVariant.cloneNode(true);

                newVariant.querySelectorAll('input, select').forEach(function (input) {
                    const name = input.getAttribute('name');
                    if (name) {
                        const updatedName = name.replace(/\[\d+\]/, `[${variantIndex}]`);
                        input.setAttribute('name', updatedName);
                        input.value = '';
                    }
                });

                container.appendChild(newVariant);
                variantIndex++;
                updateVariantButtons();
            }

            // Remove Variant
            if (e.target.classList.contains('remove-variant')) {
                const items = container.querySelectorAll('.variant-item');
                if (items.length > 1) {
                    e.target.closest('.variant-item').remove();
                    updateVariantButtons();
                } else {
                    alert('At least one variant is required.');
                }
            }
        });

        updateVariantButtons(); // initialize on page load
    });
</script>


@endpush
@endsection
