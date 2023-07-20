<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function update (Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,email,'. Auth::user()->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::find(Auth::user()->id);

            if ($user) {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->update();

                return response()->json([
                    'code' => '200',
                    'status' => 'OK',
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'createdAt' => $user->created_at->toDateString(),
                        'updatedAt' => $user->updated_at->toDateString(),
                    ]
                ]);
            }

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function topup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'balance' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => '422',
                'status' => 'UNPROCESSABLE_CONTENT',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::find(Auth::user()->id);

            if ($user) {
                $user->balance = $request->balance;
                $user->update();

                return response()->json([
                    'code' => '200',
                    'status' => 'OK',
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'balance' => 'Rp.' . number_format($user->balance, 2, ",", "."),
                        'createdAt' => $user->created_at->toDateString(),
                        'updatedAt' => $user->updated_at->toDateString(),
                    ]
                ]);

            }
    }
}
