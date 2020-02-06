<?php
namespace App\Exceptions\EmailAddress;

use Exception;

use Illuminate\Database\Eloquent\Collection;

class InvalidException extends Exception {
    protected $email = null;

    public function __construct($message, $email)
    {
        parent::__construct($message);

        $this->email = $email;
    }

    public function email() {
        return $this->email;
    }
}