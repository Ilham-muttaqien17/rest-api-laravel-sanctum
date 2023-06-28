<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    use HttpResponses;

    public function index(Request $request)
    {
        $products =  Product::all();

        if ($request->query('keyword')) {
            $keyword = $request->query('keyword');
            $products = Product::where("name", 'like', '%' . $keyword . '%')->get();
        }

        return $this->success($products, 'Get products successfully');
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) return $this->failed('Product not found!', 404);

        return $this->success($product, 'Get product successfully');
    }

    public function store(StoreArticleRequest $request)
    {
        $request->validated($request->all());

        $product = Product::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price
        ]);

        return $this->success($product, 'Product created successfully', 201);
    }

    public function update(Request $request, $id)
    {

        $validations = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'slug' => 'string|unique:products|max:255',
            'description' => 'string|max:255',
            'price' => 'numeric',
        ]);

        if ($validations->fails()) return $this->error($validations->errors(), 'Validation failed', 422);

        $product = Product::find($id);

        if (!$product) return $this->failed('Product not found!', 404);

        $product->update($request->all());

        return $this->success($product, 'Product updated successfully');
    }

    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) return $this->failed('Product not found!', 404);

        $product->delete();

        return $this->success(null, 'Product deleted successfully');
    }
}
