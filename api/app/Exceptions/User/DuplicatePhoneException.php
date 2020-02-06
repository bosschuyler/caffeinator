<?php
namespace App\Exceptions\User;

use Exception;

use Illuminate\Database\Eloquent\Collection;

class DuplicatePhoneException extends Exception {
    protected $phone = null;
    protected $user = null;
    protected $primary = null;

    public function __construct($message, $phone, $user = null, $primary = null)
    {
        parent::__construct($message);

        $this->user = $user;
        $this->phone = $phone;
        $this->primary = $primary;
    }

    public function primary() {
        return $this->primary;
    }

    public function user() {
        return $this->user;
    }

    public function users() {
        if ($this->user instanceof Collection) {
            return $this->user;
        }
        return collect($this->user);
    }

    public function phone() {
        return $this->phone;
    }

}