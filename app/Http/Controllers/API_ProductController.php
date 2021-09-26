<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class API_ProductController extends Controller
{
    public function getProduct() {
        $products = Product::get();
        return $products;
    }

    public function getProductId($id = null) {
        if (empty($id)) {
            $products = Product::get();
            return response()->json(["products" => $products], 200);
        } else {
            $products = Product::find($id);
            return response()->json(["products" => $products], 200);
        }
    }

    public function addProduct(Request $request) {
        if($request->isMethod('post')) {
            $productData = $request->input();

            // Advance Post API Validations
            $rules = [
                "name" => "required|regex:/^[\pL\s\-]+$/u",
                "barcode" => "required|unique:products",
                "price" => "required",
                "launch_product" => "required",
                "expired_product" => "required",
                "is_available" => "required",
            ];

            $customMessage = [
                'name.required' => 'Name is required',
                'barcode.required' => 'Barcode is required',
                'price.required' => 'Price is required',
                'launch_product.required' => 'launch_product is required',
                'expired_product.required' => 'expired_product is required',
                "is_available.required" => 'is_available is required',
            ];

            $validator = Validator::make($productData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $product = new Product;
            $product->name = $productData['name'];
            $product->barcode = $productData['barcode'];
            $product->price = $productData['price'];
            $product->launch_product = $productData['launch_product'];
            $product->expired_product = $productData['expired_product'];
            $product->is_available = $productData['is_available'];
            $product->save();
            return response()->json(['message' => 'Product added successfully!'], 201);
        }
    }

    public function updateProduct(Request $request, $id) {
        if ($request->isMethod('PUT')) {
            $productData = $request->input();

            $rules = [
                "name" => "required|regex:/^[\pL\s\-]+$/u",
                "barcode" => "required|unique:products",
                "price" => "required",
                "launch_product" => "required",
                "expired_product" => "required",
                "is_available" => "required",
            ];

            $customMessage = [
                'name.required' => 'Name is required',
                'barcode.required' => 'Barcode is required',
                'price.required' => 'Price is required',
                'launch_product.required' => 'launch_product is required',
                'expired_product.required' => 'expired_product is required',
                "is_available.required" => 'is_available is required',
            ];

            $validator = Validator::make($productData, $rules, $customMessage);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                Product::where('id', $id)->update([
                    "name" => $productData['name'],
                    "barcode" => $productData['barcode'],
                    "price" => $productData['price'],
                    "launch_product" => $productData['launch_product'],
                    "expired_product" => $productData['expired_product'],
                    "is_available" => $productData['is_available'],
                ]);
                return response()->json(['message' => 'Product updated successfully!'], 202);
            }
        }
    }

    public function deleteProduct($id)
    {
        Product::where('id', $id)->delete();
        return response()->json(['message' => 'Product deleted successfully'], 202);
    }
}
