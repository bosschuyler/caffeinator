<?php
namespace App\Exceptions\Phone;

use Exception;

use Illuminate\Database\Eloquent\Collection;

class CodeInvalidException extends Exception {
    protected $phone = null;
    protected $code = null;

    public function __construct($message, $phone, $code)
    {
        parent::__construct($message);

        $this->phone = $phone;
        $this->code = $code;
    }

    public function phone() {
        return $this->phone;
    }

    public function code() {
        return $this->code;
    }
}