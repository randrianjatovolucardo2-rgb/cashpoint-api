<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PatronMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'patron') {
            return response()->json([
                'message' => 'Accès réservé au Patron.',
            ], 403);
        }

        return $next($request);
    }
}