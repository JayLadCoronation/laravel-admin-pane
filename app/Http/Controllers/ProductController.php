<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\VariantImage;
use Illuminate\Support\Facades\Storage;
// use App\Models\Variant; 

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $perPage = 10; // Number of products per page

        // Get the search query (if any)
        $search = $request->get('search', '');

        // Fetch products based on search
        $products = Product::where('name', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->paginate($perPage);

        return view('products.index', compact('products', 'search'));
    }

    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::with('values')->get();
        return view('products.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'required|array',
            'variants.*.attribute_value_ids' => 'required|array',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        // Begin database transaction
        DB::beginTransaction();

        try {
            // Create the product
            $product = Product::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'created_at' => time()
            ]);
            // print_r($product);
            // die;
            // Iterate over each variant and store its data
            foreach ($validated['variants'] as $index => $variantData) {
                // Create the variant
                $variant = $product->variants()->create([
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);

                // Attach the selected attribute values to the variant
                if (isset($variantData['attribute_value_ids'])) {
                    // Ensure the attribute value IDs are in an array format
                    $variant->attributeValues()->attach($variantData['attribute_value_ids']);
                }

                // Handle image upload for each variant
                if ($request->hasFile("variants.$index.images")) {
                    foreach ($request->file("variants.$index.images") as $image) {
                        $path = $image->store('variant_images', 'public');
                        $variant->images()->create([
                            'image_path' => $path
                        ]);
                    }
                }
            }

            // Commit the transaction
            DB::commit();

            // Redirect to the product index page with a success message
            return redirect()->route('products.index')->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error for debugging
            Log::error($e->getMessage());

            // Redirect back with an error message
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    // public function update(Request $request, $id)
    // {
    //     // dd($request);
    //     // die;
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'category_id' => 'nullable|exists:categories,id',
    //         'variants' => 'required|array',
    //         'variants.*.attribute_value_ids' => 'required|array',
    //         'variants.*.price' => 'required|numeric|min:0',
    //         'variants.*.stock' => 'required|integer|min:0',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $product = Product::findOrFail($id);
    //         // $product->update($request->only('name', 'description', 'category_id','updated_at'));

    //         $product->update(
    //             [
    //                 'name' => $request->input('name'),
    //                 'description' => $request->input('description'),
    //                 'category_id' => $request->input('category_id'),
    //                 'updated_at' => time()
    //             ]
    //         );
    //         // Delete existing variants
    //         $product->variants()->each(function ($variant) {
    //             $variant->attributeValues()->detach();
    //             $variant->delete();
    //         });

    //         // Create new variants
    //         foreach ($request->variants as $index => $variantData) {
    //             $variant = $product->variants()->create([
    //                 'price' => $variantData['price'],
    //                 'stock' => $variantData['stock'],

    //             ]);

    //             //$variant = VariantImage::findOrFail($variantData['id']);
               
    //             if (!empty($variantData['remove_images'])) {
    //                 foreach ($variantData['remove_images'] as $imageId) {
    //                     $image = VariantImage::find($imageId);
    //                     if ($image) {
    //                         Storage::delete($image->image_path); // Remove from disk
    //                         $image->delete(); // Remove from DB
    //                     }
    //                 }
    //             }
                

    //             // Handle image upload for each variant
    //             if ($request->hasFile("variants.$index.images")) {
    //                 foreach ($request->file("variants.$index.images") as $image) {
    //                     $path = $image->store('variant_images', 'public');
    //                     $variant->images()->create([
    //                         'image_path' => $path
    //                     ]);
    //                 }
    //             }

    //             $variant->attributeValues()->attach($variantData['attribute_value_ids']);
    //         }

            

    //         DB::commit();

    //         return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'An error occurred: ' . $e->getMessage());
    //     }
    // }

    // 25-04-2025
    // public function update(Request $request, $id)
    // {
    //     // dd($request);
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'nullable|string',
    //         'category_id' => 'nullable|exists:categories,id',
    //         'variants' => 'required|array',
    //         'variants.*.attribute_value_ids' => 'required|array',
    //         'variants.*.price' => 'required|numeric|min:0',
    //         'variants.*.stock' => 'required|integer|min:0',
    //     ]);

    //     DB::beginTransaction();

    //     try {
    //         $product = Product::findOrFail($id);

    //         $product->update([
    //             'name' => $request->input('name'),
    //             'description' => $request->input('description'),
    //             'category_id' => $request->input('category_id'),
    //             'updated_at' => time(),
    //         ]);

    //         $removeVariantIds = $request->input('remove_variant_ids', []);
    //         if (!empty($removeVariantIds)) {
    //             foreach ($removeVariantIds as $variantId) {
    //                 $variant = $product->variants()->find($variantId);
    //                 if ($variant) {
    //                     // Delete associated images
    //                     foreach ($variant->images as $image) {
    //                         Storage::delete($image->image_path);
    //                         $image->delete();
    //                     }
    //                     // Detach attribute values and delete variant
    //                     $variant->attributeValues()->detach();
    //                     $variant->delete();
    //                 }
    //             }
    //         }

    //         // Loop through variants and either update or create
    //         foreach ($request->variants as $index => $variantData) {
    //             if (!empty($variantData['id'])) {
    //                 // Existing variant
    //                 $variant = $product->variants()->find($variantData['id']);
    //                 if (!$variant) continue; // Safety check

    //                 $variant->update([
    //                     'price' => $variantData['price'],
    //                     'stock' => $variantData['stock'],
    //                 ]);

    //                 $variant->attributeValues()->sync($variantData['attribute_value_ids']);

    //             } else {
    //                 // New variant
    //                 $variant = $product->variants()->create([
    //                     'price' => $variantData['price'],
    //                     'stock' => $variantData['stock'],
    //                 ]);

    //                 $variant->attributeValues()->attach($variantData['attribute_value_ids']);
    //             }

    //             // Handle remove_images
    //             if (!empty($variantData['remove_images'])) {
    //                 foreach ($variantData['remove_images'] as $imageId) {
    //                     $image = VariantImage::find($imageId);
    //                     if ($image) {
    //                         Storage::delete($image->image_path);
    //                         $image->delete();
    //                     }
    //                 }
    //             }

    //             // Handle image uploads
    //             if ($request->hasFile("variants.$index.images")) {
    //                 foreach ($request->file("variants.$index.images") as $image) {
    //                     $path = $image->store('variant_images', 'public');
    //                     $variant->images()->create([
    //                         'image_path' => $path
    //                     ]);
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'An error occurred: ' . $e->getMessage());
    //     }
    // }

    public function update(Request $request, $id) {
        // dd($request);
        // dd($request->remove_variation_ids);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|nullable|string',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        DB::beginTransaction();
        
        try {
            $product = Product::findOrFail($id);
            $product->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'category_id' => $request->input('category_id'),
                'updated_at' => time()
            ]);
            
            //  dd($request);
            foreach ($request->variants as $index => $variantData) {
                $variant = null;

                if (!empty($variantData['id'])) {
                    // Update existing variant
                    $variant = $product->variants()->find($variantData['id']);
                    if (!$variant) continue;

                    $variant->update([
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ]);
                    // DB::enableQueryLog();
                    $variant->attributeValues()->sync($variantData['attribute_value_ids']);
                    // dd(DB::getQueryLog());
                } else {
                    // Create new variant
                    $variant = $product->variants()->create([
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                    ]);
                    // DB::enableQueryLog();
                    $variant->attributeValues()->attach($variantData['attribute_value_ids']);
                    // dd(DB::getQueryLog());
                }


                

                // Remove deleted images (from hidden input `deleted_image_ids`)
                if (!empty($variantData['deleted_image_ids'])) {
                    foreach ($variantData['deleted_image_ids'] as $imageId) {
                        $image = VariantImage::find($imageId);
                       
                        if ($image && $image->product_variant_id == $variant->id) {
                            Storage::disk('public')->delete($image->image_path);
                            $image->delete();
                        }
                    }
                }

                // ✅ Upload new images
                if ($request->hasFile("variants.$index.images")) {
                    foreach ($request->file("variants.$index.images") as $imageFile) {
                        $path = $imageFile->store('variant_images', 'public');
                        $variant->images()->create([
                            'image_path' => $path,
                        ]);
                    }
                }
            }

            // Remove deleted variants (if any)
            if($request->remove_variation_ids) {
                $removeVariantIds = explode(",", $request->remove_variation_ids);
                if (!empty($removeVariantIds)) {
                    foreach ($removeVariantIds as $variantId) {
                        $variant = $product->variants()->find($variantId);
                        // dd($variant);
                        if ($variant) {
                            foreach ($variant->images as $image) {
                                Storage::disk('public')->delete($image->image_path);
                                $image->delete();
                            }
                            $variant->attributeValues()->detach();
                            $variant->delete();
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    // public function edit($id)
    // {
    //     // $product = Product::with('variants')->findOrFail($id);
    //     $attributes = Attribute::with('values')->get();
    //     $product = Product::findOrFail($id);
    //     $categories = Category::all();
    //     // return view('products.edit', compact('product', 'categories'));

    //     return view('products.edit', compact('product', 'attributes','categories'));
    // }

    public function edit($id)
    {
        $attributes = Attribute::with('values')->get();
        $product = Product::with(['variants.attributeValues','variants.images'])->findOrFail($id);
        $categories = Category::all();


        return view('products.edit', compact('product', 'attributes', 'categories'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Find the product and its variants
            $product = Product::with('variants')->findOrFail($id);

            // Delete associated variants
            foreach ($product->variants as $variant) {
                $variant->delete();
            }

            // Delete the product
            $product->delete();

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

}

