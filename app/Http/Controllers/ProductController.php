<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::all();
            return response()->json([
                'code' => '200',
                'status' => 'OK',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function store (Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'price' => 'required|integer',
                'stock' => 'required|integer',
                'categories_id' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (Category::find($request->categories_id)) {
                
                $product = Product::create($request->all());
    
                return response()->json([
                    'code' => '201',
                    'status' => 'CREATED',
                    'data' => [
                        'id' => $product->id,
                        'title' => $product->title,
                        'price' => 'Rp.' . number_format($product->price, 2, ",", "."),
                        'stock' => $product->stock,
                        'categoryId' => $product->categories_id,
                        'updatedAt' => $product->updated_at,
                        'createdAt' => $product->created_at,
                    ]
                    ]);
            }

            return response()->json([
                'code' => '401',
                'status' => 'NOT_FOUND',
                'errors' => 'category with id ' . $request->categories_id . ' not found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }

    }

    public function update(Request $request, int $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'price' => 'required|integer',
                'stock' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product = Product::find($id);

            if ($product) {
                $product->title = $request->title;
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->save();

                return response()->json([
                    'code' => '200',
                    'status' => 'OK',
                    'data' => $product
                ]);
            }

            return response()->json([
                'code' => '401',
                'status' => 'NOT_FOUND',
                'errors' => 'product with id ' . $id . ' not found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function updateCategory(Request $request, int $id)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'categories_id' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $product = $product = Product::find($id);
            if ($product) {
                if (Category::find($request->categories_id)) {
                    $product->categories_id = $request->categories_id;
                    $product->save();
    
                    return response()->json([
                        'code' => '200',
                        'status' => 'OK',
                        'data' => $product
                    ]);
                } 
                return response()->json([
                    'code' => '401',
                    'status' => 'NOT_FOUND',
                    'errors' => 'categories with id ' . $request->categories_id . ' not found'
                ]);
            }
            return response()->json([
                'code' => '401',
                'status' => 'NOT_FOUND',
                'errors' => 'product with id ' . $id . ' not found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(int $id)
    {
        try {

            $product = Product::find($id);

            if ($product) {
                $product->delete();

                return response()->json([
                    'code' => '200',
                    'status' => 'OK',
                ]);
            }

            return response()->json([
                'code' => '401',
                'status' => 'NOT_FOUND',
                'errors' => 'product with id ' . $id . ' not found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
