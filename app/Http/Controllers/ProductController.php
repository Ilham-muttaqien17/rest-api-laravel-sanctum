<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products =  Product::all();

        if ($request->query('keyword')) {
            $keyword = $request->query('keyword');
            $products = Product::where("name", 'like', '%' . $keyword . '%')->get();
        }

        $response['status'] = true;
        $response['message'] = 'Get products successfully';
        $response['data'] = $products;

        return response()->json($response, 200);
    }

    public function show($id)
    {
        $product = Product::find($id);
        $response['status'] = true;
        $response['message'] = 'Get product successfully';
        $response['data'] = $product;

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {

        $validations = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products|max:255',
            'description' => 'string|max:255',
            'price' => 'required|numeric',
        ]);

        if ($validations->fails()) {
            $response['status'] = false;
            $response['errors'] = $validations->errors();

            return response()->json($response, 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price
        ]);

        $response['status'] = true;
        $response['message'] = 'Product created successfully';
        $response['data'] = $product;

        return response()->json($response, 201);
    }

    public function update(Request $request, $id)
    {

        $validations = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'slug' => 'string|unique:products|max:255',
            'description' => 'string|max:255',
            'price' => 'numeric',
        ]);

        if ($validations->fails()) {
            $response['status'] = false;
            $response['errors'] = $validations->errors();

            return response()->json($response, 422);
        }


        $product = Product::find($id);

        if (!$product) {
            $response['status'] = false;
            $response['message'] = "Product not found!";

            return response()->json($response, 404);
        }

        $product->update($request->all());

        $response['status'] = true;
        $response['message'] = 'Product updated successfully';
        $response['data'] = $product;

        return response()->json($response, 200);
    }

    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            $response['status'] = false;
            $response['message'] = "Product not found!";

            return response()->json($response, 404);
        }

        $product->delete();
        $response['status'] = true;
        $response['message'] = 'Product deleted successfully';

        return response()->json($response, 200);
    }
}
