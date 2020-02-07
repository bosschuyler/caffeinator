<?php
namespace App\Http\Middleware;
use Closure;
class Cors
{
  public function handle($request, Closure $next)
  {
    $allowedOrigins = [
        'http://caffeinator.skybos.com',
        'http://caffeinator.com'
    ];

    if (env('APP_ENV') == 'development') {
        $allowedOrigins[] = 'http://localhost:4200';
        $allowedOrigins[] = 'http://localhost:3000';
    }
    
    if (in_array($request->header('origin'), $allowedOrigins))
        $origin = $request->header('origin');
    else
        $origin = 'http://caffeinator.skybos.com';

    return $next($request)
        ->header('Access-Control-Allow-Origin', $origin)
        ->header('Vary', 'Origin')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, *')
        ->header('Set-Cookie', 'PHPSESSID='.session()->getId());
  }
}