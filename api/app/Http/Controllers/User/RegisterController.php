<?php
namespace App\Http\Controllers\User;

use Mail;
use Exception;

use App\Exceptions\User\ExistingEmailAddressException;
use App\Exceptions\User\InvalidInformationException;
use App\Exceptions\User\DuplicateEmailAddressException;
use App\Exceptions\EmailAddress\InvalidException;

use App\Module\User\Model\User;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use ReallySimpleJWT\Token;

class RegisterController extends \App\Http\Controllers\Controller {

    public function emailCheck(Request $request) {
        $response = [ 'status'=>STATUS_SUCCESS, 'message'=>'', 'data'=>[] ];
    
        try {
            app('validator')->make($request->all(), [
                'email'=>"required|email",
            ], [
                'email.email'=>"This is not a valid email address format, ex: `your_email@some.domain`"
            ])->validate();
    
            $email = $request->input('email');
                
            $emailAddress = \App\Models\System\EmailAddress::findOrCreateByEmail($email);
    
            if(!$emailAddress)
                throw new \App\Exceptions\EmailAddress\InvalidException("Invalid email address: `{$email}`", $email);
    
            $duplicates = $emailAddress->users()->get();
    
            if($duplicates->count())
                throw new DuplicateEmailAddressException('Email already exists in system.', $emailAddress, $duplicates);
    
            $response['message'] = 'Email is valid and unregistered!';
        }
        catch(DuplicateEmailAddressException $e) {
            $response['message'] = $e->getMessage();
            $response['data'] = [
                    'users'=> $duplicates->map(function($item) { return $item->export(); })->all(),
                    'duplicate'=>true,
                    'registered'=> ( ($user = $duplicates->first()) && $user->password) ? 1 : 0
                ];
            $response ['status'] = STATUS_ERROR;
        }
        catch(ValidationException $error) {    
            $response ['status'] = STATUS_ERROR;
            $response ['data'] = $error->validator->messages()->all();
            $response ['message'] = $error->getMessage() . "\n" . implode("\n", $response ['data']);            
        }
        catch (\Exception $e) {
            $response['status'] = STATUS_ERROR;
            $response['message'] = $e->getMessage();
            $response['data'] = \App\Helpers\Exception::normalize($e);
        }
    
        return response()->json($response);
    }

    public function password(Request $request) {
        $response = [ 'status'=>STATUS_SUCCESS, 'message'=>'User successfully registered.', 'data'=>[] ];

        $user = null;
        
        try {
            app('validator')->make($request->all(), [
                'email'=>"required|email",
                'password'=>\App\Module\User\Model\User::getPasswordRule(), 
                'password_confirm'=>'same:password'
            ])->validate();

            $email = $request->input('email');
            $emailAddress = \App\Models\System\EmailAddress::findOrCreateByEmail($email);
            if(!$emailAddress)
                throw new \App\Exceptions\EmailAddress\InvalidException("Invalid email address: `{$email}`", $email);

            $user = $emailAddress->users()->first();

            if($user->password !== null)
                throw new \Exception("User already finished registration, must perform password reset if you do not know your password");
                
            $password = $request->input('password');

            $user->password = $password;
            $user->save();
            
            dispatch( new \App\Module\User\Jobs\Login($user->getEmail(), $password));

            // if not already logged in, we log in with the new user.
            if(!session('user_id', null))
                session(['user_id' => $user->getKey()]);
            
            $secret = config('auth.jwt.secret');
            $jwt = Token::customPayload([
                'iat' => time(),
                'user_id' => $user->getKey(),
                'exp' => strtotime("+ ".config('auth.jwt.expires_in')." Seconds")
            ], $secret);
            $response['data']['token'] = $jwt;
            
            event('UserEvent.UpdatedUser', $user);
        } 
        catch(ValidationException $error) {          
            $response ['status'] = STATUS_ERROR;
            $response ['data'] = $error->validator->messages()->all();
            $response ['message'] = $error->getMessage() . "\n" . implode("\n", $response ['data']);            
        }
        catch(Exception $error) {
            $response ['status'] = STATUS_ERROR;
            $response ['message'] = $error->getMessage();
            $response ['data'] = \App\Helpers\Exception::normalize($error);
        }
    
        return response()->json($response);
    }

    public function register(Request $request) {
        $response = [ 'status'=>STATUS_SUCCESS, 'message'=>'User successfully registered.', 'data'=>[] ];
    
        $user = null;
        try {
            app('validator')->make($request->all(), [
                'email'=>"required|email",
                'first_name'=>"required",
                'last_name'=>"required",
                'password'=>\App\Module\User\Model\User::getPasswordRule(), 
                'password_confirm'=>'required|same:password'
            ])->validate();
    
            $email = $request->input('email');
            $number = $request->input('phone');
    
            $emailAddress = \App\Models\System\EmailAddress::findOrCreateByEmail($email);
    
            ###################################
            #   Email Processing
            ###################################
            /*
            |   Must login to the other account to use this email address
            |   We will not allow the ability to transfer the login credential
            |
            */
            
                if(!$emailAddress)
                    throw new \App\Exceptions\EmailAddress\InvalidException("Invalid email address: `{$email}`", $email);
    
                $emailUsers = $emailAddress->users()->get();
    
                if($emailUsers->count())
                    throw new DuplicateEmailAddressException('Email already exists in system. You must login with this account to proceed.  If you do not know your password, proceed to login page and use forgot password request.', $emailAddress, $emailUsers);
                
            if(!$user) 
                $user = new \App\Module\User\Model\User();
    
            $password = $request->input('password');
            
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->password = $password;
            $user->save();

            $user->addEmailAddress($emailAddress, true);
    
            dispatch( new \App\Module\User\Jobs\Login($user->getEmail(), $password));
            
            // if not already logged in, we log in with the new user.
            if(!session('user_id', null))
                session(['user_id' => $user->getKey()]);
            
            $secret = config('auth.jwt.secret');
            $jwt = Token::customPayload([
                'iat' => time(),
                'user_id' => $user->getKey(),
                'exp' => strtotime("+ ".config('auth.jwt.expires_in')." Seconds")
            ], $secret);
            $response['data']['token'] = $jwt;
            
            event('UserEvent.UpdatedUser', $user);
        }
        catch(ValidationException $error) {          
            $response ['status'] = STATUS_ERROR;
            $response ['data'] = [];
            $response ['errors'] = $error->validator->messages()->all();
            $response ['message'] = $error->getMessage();            
        }
        catch(ExistingEmailAddressException $error) {
            $response ['status'] = STATUS_ERROR;
            $response ['message'] = $error->getMessage();
        }
        catch(InvalidInformationException $error) {
            $response ['status'] = STATUS_ERROR;
            $response ['message'] = $error->getMessage(); 
        }
        catch(Exception $error) {
            $response ['status'] = STATUS_ERROR;
            $response ['message'] = $error->getMessage();
            $response ['data'] = \App\Helpers\Exception::normalize($error);
        }
    
        return response()->json($response);
    }
}




