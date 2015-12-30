<?php

namespace App\Http\Middleware;

use App\Helpers\Logger;
use Closure;

class Main
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Logger::middlewareInfo('@ExampleMiddle begin, request:', [
            'headers' => $request->headers,
            'url' => $request->fullUrl(),
            'params' => $request->all()
        ]);

        $response = $next($request);

        Logger::middlewareInfo('@ExampleMiddle end, response:', [
            'code' => $response->getStatusCode(),
            'content' => $response->getContent()
        ]);

        return $response;
    }
}
