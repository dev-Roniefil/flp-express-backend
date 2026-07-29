<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category', 'variations')
        ->where('is_active', true);

        // Filter by status (publish / draft)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(20);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        // Featured Image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $fullPath = public_path('Images/Products/' . $filename);
            
            // Create directory if not exists
            $dir = public_path('Images/Products');
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $image->move($dir, $filename);
            
            $data['image_url'] = 'Images/Products/' . $filename;

            \Log::info('Image saved to: ' . $fullPath);
        }

        // Gallery Images
        $gallery = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('Images/Products'), $filename);
                $gallery[] = 'Images/Products/' . $filename;
            }
            $data['gallery'] = json_encode($gallery);
        }

        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'],
            'sku' => $data['sku'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'status' => $data['status'] ?? 'publish',
            'image_url' => $data['image_url'] ?? null,
            'gallery' => $data['gallery'] ?? null,
            'has_variations' => $request->has_variations,
            'is_package' => $request->is_package === '1' || $request->is_package === true,
            'package_data' => $request->package_data ?? null,
            
        ]);

        // Handle variations
        if ($request->has_variations && $request->variations) {
            $variations = is_string($request->variations) 
                ? json_decode($request->variations, true) 
                : $request->variations;

            if (is_array($variations)) {
                $product->variations()->delete(); // Clear old

                foreach ($variations as $var) {
                $product->variations()->create([
                    'name' => $var['name'],
                    'options' => $var['options'] ?? []  // Contains name + image_url
                ]);
                }
            }
        }

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product->load('variations')
        ]);
    }

    public function show($id): JsonResponse
    {
        $product = Product::with('category', 'variations')->findOrFail($id);
    
        return response()->json($product);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $dir = public_path('Images/Products');
            
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $image->move($dir, $filename);
            $product->image_url = 'Images/Products/' . $filename;

            \Log::info('Image updated to: ' . $filename);
        }
        
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'image_url' => $request->image_url ?? $product->image_url,  // Keep old if no new image
            'is_package' => $request->is_package === '1' || $request->is_package === true,
            'has_variations' => $request->has_variations === '1' || $request->has_variations === true,
            'package_data' => $request->package_data,
        ]);

        // Handle variations if provided
        if ($request->has_variations && $request->variations) {
            $variations = is_string($request->variations) 
                ? json_decode($request->variations, true) 
                : $request->variations;

            if (is_array($variations)) {
                $product->variations()->delete();

                foreach ($variations as $var) {
                $product->variations()->create([
                    'name' => $var['name'],
                    'options' => $var['options'] ?? []
                ]);
                }
            }
        }

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product->fresh()->load('variations')
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}