<?php
namespace App\Services\Twilio\Response;

use App\Services\Phone\Response\Interfaces\LookupResponseInterface;

use App\Helpers\Phone;

class LookupResponse implements LookupResponseInterface {

    protected $data = null;

    const TYPE_MOBILE = 'mobile';
    const TYPE_LANDLINE = 'landline';
    const TYPE_VOIP = 'voip';

    const TYPES_SMS = [
        self::TYPE_MOBILE,
        self::TYPE_VOIP
    ];

    const NOT_EXIST_ERRORS = [
        60600
    ];

    public function __construct($data) {
        $this->data = $data;
    }

    public function getType() {
        if($carrier = $this->data->carrier)
            return $carrier['type'] ?: null;
    }

    public function isMobile() {
        if($type = $this->getType())
            return $type == self::TYPE_MOBILE;
        return null;
    }

    public function isVoip() {
        if($type = $this->getType())
            return $type == self::TYPE_VOIP;
        return null;
    }

    public function isSmsCapable() {
        if($type = $this->getType())
            return in_array($type, self::TYPES_SMS);
        return null;
    }

    public function getNumber() {
        return $this->data->phoneNumber;
    }

    public function getDigits() {
        return Phone::digits($this->getNumber());
    }

    public function getCountryCode() {
        return $this->data->countryCode;
    }

    public function exists() {
        if($error = $this->getErrorCode())
            return !in_array($error, self::NOT_EXIST_ERRORS);
        
        return true;
    }

    public function getErrorCode() {
        $carrier = $this->data->carrier;
        if(isset($carrier['error_code']))
            return $carrier['error_code'];
        else
            return null;
    }

    public function hasErrorCode() {
        return !empty($this->getErrorCode());
    }

    public function __toString() {
        $string = 'Number: '.$this->getNumber()."\n";
        $string .= 'Country: '.$this->getCountryCode()."\n";
        $string .= 'Error: '.$this->getErrorCode()."\n";
        $string .= 'Exists: '.($this->exists() ? 1 : 0)."\n";
        $string .= print_r($this->data->carrier, 1);
        return $string;
    }
}