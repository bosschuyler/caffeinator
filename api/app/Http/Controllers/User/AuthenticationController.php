<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Api\AbstractController;

use App;
use Redis;
use Exception;
use Mail;
use DB;

use App\Exceptions\Authentication\IpValidationException;
use App\Exceptions\Token\InvalidTokenException;
use App\Exceptions\EmailAddress\InvalidEmailAddressException;

use App\Models\Interaction as InteractionType;
use App\Models\System\Address;
use App\Module\User\Model\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// Importing the Api controller causes a necessary authorization upon
// initializing the controller, which will validate the API key against the credentials table.
// this will allow implementing API credentials.
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use ReallySimpleJWT\Token;

class AuthenticationController extends \App\Http\Controllers\Controller {

    public function login(Request $request) {
        $response = [ 'status'=>STATUS_SUCCESS, 'message'=>'Successfully logged in with user.', 'data'=>[]];

        try {
            $user_email = trim($request->input('email', null));

            if(!$user_password = $request->input('password', null))
                throw new Exception("Please enter a password");

            # Destroying the session
            $request->session()->flush();

            $dispatcher = app(\Illuminate\Bus\Dispatcher::class);
            $user = $dispatcher->dispatchNow( new \App\Module\User\Jobs\Login($user_email, $user_password));

            session(['user_id' => $user->getKey()]);
            
            $secret = config('auth.jwt.secret');
            $jwt = Token::customPayload([
                'iat' => time(),
                'user_id' => $user->getKey(),
                'exp' => strtotime("+ ".config('auth.jwt.expires_in')." Seconds")
            ], $secret);
            $response['data']['token'] = $jwt;
        }
        catch(ValidationException $error)
        {          
            $response ['status'] = STATUS_ERROR;
            $response ['data'] = $error->validator->messages()->all();
            $response ['message'] = $error->getMessage() . "\n" . implode("\n", $response ['data']);
                  
            app('Logger', ['authentication.login'])->error($error);      
        }
        catch(IpValidationException $error) {
            $response ['status'] = STATUS_ERROR;
            $response ['message'] = $error->getMessage(); 
            $response ['data'] = ['ip_validate'=>1];
        }
        catch(Exception $error) {
            $response ['status'] = STATUS_ERROR;
            $response ['message'] = $error->getMessage();    
            $response ['trace'] = \App\Helpers\Exception::normalize($error, true);  
            
            app('Logger', ['authentication.login'])->error($error);
            app('Logger', ['authentication.login'])->debug($request->all());
        }
        return response()->json($response);
    }
    
    public function logout(Request $request){
        $response = [ 'status'=>STATUS_SUCCESS, 'message'=>'Successfully logged out with user.', 'data'=>[]];
        # Destroying the session
        $request->session()->flush();
        # Removing data from handler
        $request->session()->migrate(true);
        return response()->json($response);
    }
}




