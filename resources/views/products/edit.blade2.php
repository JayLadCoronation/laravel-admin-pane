@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Product</h3>
    <form method="POST" action="{{ route('products.update', $product->id) }}">
        @csrf
        <div class="mb-2">
            <label>Name</label>
            <input type="text" name="name" value="{{ $product->name }}" class="form-control" required />
        </div>
        <div class="mb-2">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-control">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="attributes">Attributes (name, value)</label>
            <div id="attributes">
                <div class="attribute-group">
                    <input type="text" name="attributes[0][name]" class="form-control" placeholder="Attribute Name">
                    <input type="text" name="attributes[0][value]" class="form-control" placeholder="Attribute Value">
                </div>
            </div>
            <button type="button" class="btn btn-info" id="add-attribute">Add More Attributes</button>
        </div>

        <script>
            document.getElementById('add-attribute').addEventListener('click', function() {
                const attributeGroup = document.createElement('div');
                attributeGroup.classList.add('attribute-group');
                attributeGroup.innerHTML = `
                    <input type="text" name="attributes[][name]" class="form-control" placeholder="Attribute Name">
                    <input type="text" name="attributes[][value]" class="form-control" placeholder="Attribute Value">
                `;
                document.getElementById('attributes').appendChild(attributeGroup);
            });
        </script>
        <hr />
        <h5>Variants</h5>
        <div id="variant-wrapper">
            @foreach($product->variants as $variant)
            <div class="border p-3 mb-2">
                <div class="row">
                    @foreach($attributes as $attr)
                        <div class="col-md-3">
                            <label>{{ $attr->name }}</label>
                            <select name="variants[][attribute_value_ids][]" class="form-control" required>
                                @foreach($attr->values as $val)
                                    <option value="{{ $val->id }}" {{ in_array($val->id, json_decode($variant->attribute_value_ids)) ? 'selected' : '' }}>
                                        {{ $val->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    <div class="col-md-3">
                        <label>Price</label>
                        <input type="number" name="variants[][price]" value="{{ $variant->price }}" class="form-control" required />
                    </div>
                    <div class="col-md-3">
                        <label>Stock</label>
                        <input type="number" name="variants[][stock]" value="{{ $variant->stock }}" class="form-control" required />
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-secondary" onclick="addVariant()">+ Add Variant</button>
        <br><br>

        <button type="submit" class="btn btn-success">Update Product</button>
    </form>
</div>


<script>
    let attributes = @json($attributes);

    function addVariant() {
        let html = '<div class="border p-3 mb-2"><div class="row">';

        attributes.forEach(attr => {
            html += '<div class="col-md-3"><label>'+ attr.name +'</label><select name="variants[][attribute_value_ids][]" class="form-control" required>';
            attr.values.forEach(val => {
                html += `<option value="${val.id}">${val.value}</option>`;
            });
            html += '</select></div>';
        });

        html += `
            <div class="col-md-3">
                <label>Price</label>
                <input type="number" name="variants[][price]" class="form-control" required />
            </div>
            <div class="col-md-3">
                <label>Stock</label>
                <input type="number" name="variants[][stock]" class="form-control" required />
            </div>
        </div></div>`;

        document.getElementById('variant-wrapper').insertAdjacentHTML('beforeend', html);
    }
</script>
@endsection
