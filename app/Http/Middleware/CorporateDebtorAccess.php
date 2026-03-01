<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorporateDebtorAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->type == '1') {
            $request->merge(['debtor_id' => auth()->id()]);
        }
        
        return $next($request);
    }
}
