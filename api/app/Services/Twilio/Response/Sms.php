<?php

namespace App\Services\Twilio\Response;

class Sms extends ResponseAbstract {
    public static $entity = "App\Services\Twilio\Entity\Message";

    public function process() {
        $entity = $this->getEntityClass();
        $this->items = collect([]);
        $this->items->push(new $entity($this->data));
    }
}