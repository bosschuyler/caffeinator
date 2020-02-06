<?php
namespace App\Exceptions\User;

use Exception;

use Illuminate\Database\Eloquent\Collection;

use App\Models\System\EmailAddress;

class DuplicateEmailAddressException extends Exception {
    protected $emailAddress = null;
    protected $user = null;

    public function __construct($message, EmailAddress $emailAddress, $user=null)
    {
        parent::__construct($message);

        $this->user = $user;
        $this->emailAddress = $emailAddress;
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

    public function emailAddress() {
        return $this->emailAddress;
    }

}