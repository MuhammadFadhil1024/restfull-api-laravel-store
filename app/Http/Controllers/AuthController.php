<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login (Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = request()->only(['email', 'password']);

            if (! $token = Auth::attempt($credentials)) {
                return response()->json([
                    'code' => '401',
                    'status' => 'UNATHORIZED',
                    'errrors' => 'Email & Password does not match with our record'
                ], 401);
            }

            return response()->json([
                'code' => '200',
                'status' => 'OK',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60
                ]
                ], 200);
        }
         catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function register (Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string',
                'gender' => 'required|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'code' => '422',
                    'status' => 'UNPROCESSABLE_CONTENT',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'gender' => $request->gender
            ]);

            return response()->json([
                'code' => '201',
                'status' => 'CREATED',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'balance' => 'Rp.' . number_format($user->balance, 2, ",", "."),
                    'createdAt' => $user->created_at->toDateString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'code' => '500',
                'status' => 'INTERNAL_SERVER_ERROR',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        try {

            Auth::logout();

            return response()->json([
                'code' => "200",
                'status' => 'OK'
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


