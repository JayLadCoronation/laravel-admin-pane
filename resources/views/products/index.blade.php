@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Product List</h5>
            <!-- <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">+ Add Product</a> -->
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm btn-wave waves-effect waves-light" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Product">
                    <i class="bi bi-plus-lg"></i> Product
            </a>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="GET" action="{{ route('products.index') }}" class="form-inline mb-3">
                <div class="input-group w-50">
                    <input type="text" name="search" value="{{ old('search', $search) }}" class="form-control" placeholder="Search products...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Variants</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->description }}</td>
                                <td>{{ $product->variants->count() }}</td>
                                <td>
                                    <a href="{{ route('products.edit', $product->id) }}" class="icon-btn bg-orange" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <!-- <button class="btn btn-sm btn-red" onclick="return confirm('Are you sure?')">Delete</button> -->
                                        <button type="button" class="icon-btn bg-red" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between">
                <div>
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products.
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
