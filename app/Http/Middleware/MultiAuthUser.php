<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MultiAuthUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $userType): Response
    {

        if (auth()->check()) {
            if (auth()->user()->type == $userType) {
                return $next($request);
            }
            
            // Redirect based on user type
            switch (auth()->user()->type) {
                case 'parcel':
                    return redirect('/parcel/dashboard');
                case 'sticker':
                    return redirect('/sticker/dashboard');
            }
        }

        return response()->json(['You are not authorized to access this page.']);
    }
}
