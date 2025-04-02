<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertFormDataToArray
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('PUT') || $request->isMethod('PATCH')) {
            if (str_contains($request->header('Content-Type'), 'multipart/form-data')) {
                $data = array_merge($request->all(), $_POST);
                $request->merge($data);
            }
        }
        
        return $next($request);
    }
}
