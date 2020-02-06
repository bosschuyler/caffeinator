<?php

namespace App\Services\Email;

use Illuminate\Support\Facades\Facade;

use App\Services\Email\EmailGateway;

/**
 * @see \Illuminate\Contracts\Console\Kernel
 */
class EmailFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'email.gateway';
    }
}
