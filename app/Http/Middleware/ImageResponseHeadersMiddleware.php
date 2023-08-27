<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ImageResponseHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->headers->get('Content-Type', '') === 'image/png' || $response->headers->get('Content-Type', '') === 'image/jpeg') {
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Methods', 'GET,OPTIONS');
        }

        return $response;
    }
}
