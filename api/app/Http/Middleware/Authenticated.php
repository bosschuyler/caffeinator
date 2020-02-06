<?php
namespace App\Http\Middleware;

use Closure;
use ReallySimpleJWT\Token;

class Authenticated {
    public function handle($request, Closure $next)
    {
        $user_id = session('user_id', null);
        $user = \App\Module\User\Model\User::find($user_id);

        if(!$user) {
            $api_key = $request->input('api-key', null);
            if($apiKey = \App\Models\Api\Key::where('key', $api_key)->first()) {
                $user = $apiKey->users()->first();
            }

            if($jwt = $request->input('jwt', null)) {
                $secret = config('auth.jwt.secret');
                $payload = Token::getPayload($jwt, $secret);

                $user_id = isset($payload['user_id']) ? $payload['user_id'] : null;
                $expires = isset($payload['exp']) ? $payload['exp'] : null;

                if($expires > time()) {
                    $user = \App\Module\User\Model\User::find($user_id);
                }                
            }
        }

        if(!$user) {
            if($request->expectsJson()) {
                $response = [ 'status'=>STATUS_ERROR, 'message'=>'Not authenticated', 'data'=>[] ];
                return response()->json($response, 401);
            } else {
                $request->session()->flush();
                $request->session()->migrate(true);
            }
        }

        $request->loggedUser = $user;        
        return $next($request);
    }
}