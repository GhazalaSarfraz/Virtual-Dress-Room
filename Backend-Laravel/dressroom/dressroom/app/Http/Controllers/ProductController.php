<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        foreach ($products as $product) {
            // accessor automatically returns absolute url
        }

        return response()->json([
            'status' => 'success',
            'products' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'product' => $product
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);

        $productData = $request->except('image');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $productData['image_url'] = '/storage/' . $path;
        }

        $product = Product::create($productData);
        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'category' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);

        $productData = $request->except('image', '_method');

        if ($request->hasFile('image')) {
            if ($product->getRawOriginal('image_url')) {
                $oldPath = str_replace('/storage/', '', $product->getRawOriginal('image_url'));
                Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $productData['image_url'] = '/storage/' . $path;
        }

        $product->update($productData);
        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        if ($product->getRawOriginal('image_url')) {
            $oldPath = str_replace('/storage/', '', $product->getRawOriginal('image_url'));
            Storage::disk('public')->delete($oldPath);
        }
        
        $product->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }
}
