<?php
namespace App\Services\Email\Response;

use App\Services\Email\Response\Interfaces\LookupResponseInterface;

class MailgunLookupResponse implements LookupResponseInterface {

    protected $data = null;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getBody() {
        return property_exists($this->data, 'http_response_body') ? $this->data->http_response_body : null;
    }

    public function getKey($name, $default = null) {
        if($body = $this->getBody()) {
            if(property_exists($body, $name))
                return $body->$name;
        }        
        return $default;
    }

    public function isDisposible() {
        return $this->getKey('is_disposible_address');
    }

    public function isValid() {
        return $this->getKey('is_valid');
    }

    public function isVerified() {
        $verified = $this->getKey('mailbox_verification');
        if(in_array($verified, ['true', 'unknown']))
            return true;
        
        return false;
    }

    public function verification() {
        return $this->getKey('mailbox_verification');
    }

    public function exists() {
        return $this->isVerified() && $this->isValid() && !$this->isDisposible();
    }

    public function getEmail() {
        return $this->getKey('address');
    }

    public function __toString() {
        $string = 'address: '.$this->getEmail()."\n";
        $string .= 'disposible: '.($this->isDisposible() ? 'Yes' : 'No')."\n";
        $string .= 'valid: '.($this->isValid() ? 'Yes' : 'No')."\n";
        $string .= 'verified: '.($this->isVerified() ? 'Yes' : 'No')."\n";
        $string .= print_r($this->getBody(), 1)."\n";
        return $string;
    }
}