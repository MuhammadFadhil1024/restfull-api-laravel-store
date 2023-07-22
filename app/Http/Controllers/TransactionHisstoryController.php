<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\TransactionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionHisstoryController extends Controller
{
    
    public function store (Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer',
                'quantity' => 'required|integer'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }

            $product = Product::find($request->product_id);

            if ($product) {
                
                // Check if the product stock exists
                if ( $product->stock >= $request->quantity) {
                    
                    $total_price = $request->quantity * $product->price;

                    //  check if the user balance is sufficient
                    if (Auth::user()->balance >= $total_price) {
                        
                        // create transaction
                        $transaction = TransactionHistory::create([
                            'product_id' => $request->product_id,
                            'user_id' => Auth::user()->id,
                            'quantity' => $request->quantity,
                            'total_price' => $total_price
                        ]);

                        $data = [
                            'total_price' => $transaction->total_price,
                            'quantity' => $transaction->quantity,
                            'product_name' => Product::where('id', $request->product_id)->first()->title
                        ];

                        // reduce product stock
                        $product->stock = $product->stock - $request->quantity;
                        $product->save();

                        // reduce balance user
                        $user = User::find(Auth::user()->id);
                        $user->balance = $user->balance - $total_price;
                        $user->save();

                        // increase the number of products sold amount from the category
                        $categories = Category::find($product->categories_id);
                        $categories->sold_product_amount = $categories->sold_product_amount + $request->quantity;
                        $categories->save();

                        return response()->json([
                            'code' => '201',
                            'status' => 'CREATED',
                            'data' => $data
                        ], 201);
                    }
                }
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => 'Product stock is insufficient'
                ], 422);
 
            }
            return response()->json([
                'code' => '401',
                'status' => 'NOT_FOUND',
                'error' => 'Product with id ' . $request->product_id . ' not found'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }

    }

    public function transactionUser()
    {
        try {
            
            $transactions = TransactionHistory::with('product')->where('user_id', Auth::user()->id)->get();
            $collection = $transactions->collect();

            $transactionMap = $collection->map(function ($transaction, $key) {
                return [
                    'productId' => $transaction->product_id,
                    'userId' => $transaction->user_id,
                    'quantity' => $transaction->quantity,
                    'total_price' => $transaction->total_price,
                    'createdAt' => $transaction->created_at,
                    'updatedAt' => $transaction->updated_at,
                    'product' => [
                        'id' => $transaction->product->id,
                        'title' => $transaction->product->title,
                        'price' => 'Rp.' . number_format($transaction->product->price, 2, ",", "."),
                        'stock' => $transaction->product->stock,
                        'categoryId' => $transaction->product->categories_id,
                    ]
                ];
            });

            return response()->json([
                'code' => '200',
                'status' => 'OK',
                'data' => $transactionMap
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function transactionAdmin()
    {
        try {
            
            $transactions = TransactionHistory::with('product', 'user')->get();

            $transactionMap = $transactions->map(function ($transaction, $key) {
                return [
                    'productId' => $transaction->product_id,
                    'userId' => $transaction->user_id,
                    'quantity' => $transaction->quantity,
                    'total_price' => $transaction->total_price,
                    'createdAt' => $transaction->created_at,
                    'updatedAt' => $transaction->updated_at,
                    'product' => [
                        'id' => $transaction->product->id,
                        'title' => $transaction->product->title,
                        'price' => 'Rp.' . number_format($transaction->product->price, 2, ",", "."),
                        'stock' => $transaction->product->stock,
                        'categoryId' => $transaction->product->categories_id,
                    ],
                    'user' => [
                        'id' => $transaction->user->id,
                        'email' => $transaction->user->email,
                        'balance' => $transaction->user->balance,
                        'gender' => $transaction->user->gender,
                        'roles' => $transaction->user->roles,
                    ]
                ];
            });

            return response()->json([
                'code' => '200',
                'status' => 'OK',
                'data' => $transactionMap
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function transactionDetail (int $id)
    {
    
        try {
            $transaction = TransactionHistory::with('product')->where('id', $id)->first();

            $data = [
                'productId' => $transaction->product_id,
                'userId' => $transaction->user_id,
                'quantity' => $transaction->quantity,
                'total_price' => $transaction->total_price,
                'createdAt' => $transaction->created_at,
                'updatedAt' => $transaction->updated_at,
                'product' => [
                    'id' => $transaction->product->id,
                    'title' => $transaction->product->title,
                    'price' => 'Rp.' . number_format($transaction->product->price, 2, ",", "."),
                    'stock' => $transaction->product->stock,
                    'categoryId' => $transaction->product->categories_id,
                ]
            ];

            return response()->json([
                'code' => '200',
                'status' => 'OK',
                'data' => $data
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
