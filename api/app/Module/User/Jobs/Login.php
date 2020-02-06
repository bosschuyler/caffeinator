<?php

namespace App\Module\User\Jobs;

use App\Module\User\Model\User;
use App\Models\System\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class Login
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $password;
    
    /**
     * @param  $email, $password
     * @return void
     */
    public function __construct( $email, $password )
    {        
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return User
     */
    public function handle()
    {
        $email = $this->email;
        $password = $this->password;

        $user = \App\Module\User\Model\User::whereHas('emailAddresses', function($query) use ($email) {
            $query->where('email', $email);
        })->first();
        
        if(!$user)
            throw new \Exception("No users with that email address");    
    
        if(!$user || !$user->getPassword()) {            
            if(!($user))
                throw new \Exception('Password incorrect.');
            
            if(!$user) 
                $user = User::createByEmail($email);
            
            $user->password = $password;            
        }
        
        if($user && $user->getPassword() != $password)
            throw new \Exception('Password incorrect.');

        $user->save();

        return $user;
    }
}