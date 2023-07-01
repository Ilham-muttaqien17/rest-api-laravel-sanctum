<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $products = Cache::remember('products_list', 60, function () {
            return Product::all();
        });

        if ($request->query('keyword')) {
            $products = Cache::remember('products_list', 60, function () use ($request) {
                $keyword = $request->query('keyword');

                return Product::where('name', 'like', '%' . $keyword . '%')->get();
            });
        }

        return $this->success($products, 'Get products successfully');
    }

    public function show(Product $product)
    {
        return $this->success($product, 'Get product successfully');
    }

    public function store(StoreProductRequest $request)
    {
        $request->validated($request->all());

        $product = Product::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return $this->success($product, 'Product created successfully', 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $request->validated($request->all());

        $product->update($request->all());

        return $this->success($product, 'Product updated successfully');
    }

    public function delete(Product $product)
    {
        $product->delete();

        return $this->success(null, 'Product deleted successfully');
    }
}
