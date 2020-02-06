<?php

namespace App\Services\Phone;

use Illuminate\Support\Facades\Facade;

use App\Services\Phone\PhoneGateway;

/**
 * @see \Illuminate\Contracts\Console\Kernel
 */
class PhoneFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'phone.gateway';
    }
}
