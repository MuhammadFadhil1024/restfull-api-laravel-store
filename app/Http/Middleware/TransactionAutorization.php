<?php

namespace App\Http\Middleware;

use App\Models\TransactionHistory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TransactionAutorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $transaction_id = $request->route()->parameters();
        // dd($transaction_id['id']);

        $transaction = TransactionHistory::find($transaction_id['id']);
        // dd($transaction);

        if (Auth::user()->roles == 'ADMIN') {
            
            return $next($request);    

        } elseif( Auth::user()->roles == 'USER' )
        {   
            if (Auth::user()->id == $transaction->user_id) {
                
                return $next($request);

            } 

            return response()->json([
                'code' => '403',
                'status' => 'FORBIDDEN',
                'message' => 'You dont have access for this resource'
            ]);

        } else {
            return response()->json([
                'code' => '403',
                'status' => 'FORBIDDEN',
                'message' => 'You dont have access for this resource'
            ]);
        }

        
    }
}
