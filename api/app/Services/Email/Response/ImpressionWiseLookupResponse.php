<?php
namespace App\Services\Email\Response;

class ImpressionWiseLookupResponse {

    protected $data = null;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getBody() {
        return property_exists($this->data, 'http_response_body') ? $this->data->http_response_body : null;
    }

    public function getKey($name, $default = null) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }        
        return $default;
    }

    public function getCategory() {
        return strtolower($this->getKey('result'));
    }

    public function getClassification() {
        return strtolower($this->getKey('class'));
    }


}