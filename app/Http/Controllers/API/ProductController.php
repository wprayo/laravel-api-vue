<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return response()->json(new ProductCollection($products), Response::HTTP_OK);
    }

public function store(ProductRequest $request)
{
    $data = $request->validated();

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public');
        $data['image'] = $imagePath;
    }

    // --- BARIS INI YANG KURANG ---
    $product = Product::create($data);

    return response()->json([
        'status' => true,
        'message' => 'Product created successfully',
        'data' => new ProductResource($product)
    ], Response::HTTP_CREATED);
}

    public function show(Product $product)
    {
        return response()->json([
            'status' => true,
            'message' => 'Product retrieved successfully',
            'data' => new ProductResource($product)
        ], Response::HTTP_OK);
    }

    public function update(ProductRequest $request, Product $product)
    {

         $data = $request->validated();

    // kalau upload gambar baru
    if ($request->hasFile('image')) {

        // hapus gambar lama kalau ada
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // simpan gambar baru
        $imagePath = $request->file('image')->store('products', 'public');
        $data['image'] = $imagePath;
    }

    $product->update($data);

        // $product->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product),
        ], Response::HTTP_OK);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully',
        ], Response::HTTP_OK);
    }
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'description' => $this->description,
        'price' => $this->price,
        'stock' => $this->stock,
        // Baris ini akan mengubah path jadi http://127.0.0.1:8000/storage/products/abc.jpg
        'image' => $this->image ? asset('storage/' . $this->image) : null,
        'created_at' => $this->created_at,
    ];
}
}

