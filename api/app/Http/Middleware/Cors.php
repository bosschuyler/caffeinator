<?php
namespace App\Http\Middleware;
use Closure;
class Cors
{
  public function handle($request, Closure $next)
  {
    return $next($request)
        ->header('Access-Control-Allow-Origin', 'http://localhost:4200')
      ->header('Access-Control-Allow-Credentials', 'true')
      ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
      ->header('Access-Control-Allow-Headers', 'Content-Type, *')
      ->header('Set-Cookie', 'PHPSESSID='.session()->getId());
      
  }
}