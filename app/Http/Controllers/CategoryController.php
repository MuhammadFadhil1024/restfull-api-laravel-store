<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        try {

            // $category = DB::table('categories')->leftJoin('products', 'categories.id', '=' , 'products.categories_id')
            //     ->select('categories.id as category_id','categories.type as type','categories.sold_product_amount as sold_products',
            //      'categories.created_at as createdAt', 'categories.updated_at as updatedAt', 'products.id as id', 'products.title as title',
            //      'products.price as price', 'products.stock as stock', 'products.created_at as createdAt', 'products.updated_at as updatedAt')
            //     ->get();
            //     $data = $category->groupBy('category_id')->map(function ($categoryItems) {
            //         return [
            //             'id' => $categoryItems[0]->category_id,
            //             'type' => $categoryItems[0]->type,
            //             'sold_product_amount' => $categoryItems[0]->sold_products,
            //             'createdAt' => $categoryItems[0]->createdAt,
            //             'updatedAt' => $categoryItems[0]->updatedAt,
            //             'products' => $categoryItems->map(function ($item) {
            //                 return [
            //                     'id' => $item->id,
            //                     'title' => $item->title,
            //                     'price' => $item->price,
            //                     'stock' => $item->stock,
            //                     'createdAt' => $item->createdAt,
            //                     'updatedAt' => $item->updatedAt
            //                 ];
            //             })
            //         ];
            //     });

            $data = Category::with('products')->get();


            return response()->json([
                'code' => '200',
                'status' => 'OK',
                'data' => $data
            ], 200);
            
        } catch (\Exception $e){
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function store (Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'type' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }

            $categories = Category::create($request->all());

            return response()->json([
                'code' => '201',
                'status' => 'CREATED',
                'data' => [
                    'id' => $categories->id,
                    'type' => $categories->type,
                    'created_at' => $categories->created_at->toDateString(),
                    'updated_at' => $categories->updated_at->toDateString(),
                    'sold_product_amount' => $categories->sold_product_amount
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function update (Request $request, int $id)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'type' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = Category::find($id);

            if ($category) {
                
                $category->type = $request->type;
                $category->save();

                return response()->json([
                    'code' => '200',
                    'status' => 'OK',
                    'data' => $category
                ]);

            }

            return response()->json([
                'code' => '401',
                'status' => 'NOT_FOUND',
                'errors' => 'category with id ' . $id . ' not found'
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

            $category = Category::find($id);

            if ($category) {
                $category->delete();
                
                return response()->json([
                    'code' => '200',
                    'status' => 'OK',
                ]);
            }

            return response()->json([
                'code' => '401',
                'status' => 'NOT_FOUND',
                'errors' => 'category with id ' . $id . ' not found'
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
