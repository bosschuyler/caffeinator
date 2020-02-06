<?php

namespace App\Services\Twilio\Entity;

class MessageEvent {
    protected $data = null;

    public function __construct($data) {
        $this->data = collect($data);
    }

    public function getParam($key) {
        return $this->data->get($key);
    }

    public function getMessageId() {
        return $this->data->get('MessageSid');
    }

    public function getMessageStatus() {
        return $this->data->get('MessageStatus');
    }
}