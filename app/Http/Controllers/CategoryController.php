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

            $category = DB::table('categories')->get();

            return response()->json([
                'code' => '200',
                'status' => 'OK',
                'data' => $category
            ], 200);
            
        } catch (\Exception $e){
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
                'message' => 'An error occurred',
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
                'message' => 'category with id ' . $id . ' not found'
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
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
                    'message' => 'Category has been successfully deleted'
                ]);
            }

            return response()->json([
                'code' => '401',
                'status' => 'NOT_FOUND',
                'message' => 'category with id ' . $id . ' not found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
