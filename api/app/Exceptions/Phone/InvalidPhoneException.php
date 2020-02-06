<?php
namespace App\Exceptions\Phone;

use Exception;

use Illuminate\Database\Eloquent\Collection;

class InvalidPhoneException extends Exception {
    protected $number = null;
    protected $extension = null;

    public function __construct($message, $number, $extension=null)
    {
        parent::__construct($message);

        $this->number = $number;
        $this->extension = $extension;
    }

    public function number() {
        return $this->number;
    }

    public function extension() {
        return $this->extension;
    }

}