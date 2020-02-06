<?php

namespace App\Services\Twilio\Response;

class ResponseAbstract {
    public static $entity = null;

    protected $data;
    protected $items = null;


    public function __construct($data) {
        $this->data = $data;
        
        $this->process();
    }

    protected function getEntityClass() {
        $entity = static::$entity;

        if(!class_exists($entity))
            throw new \Exception("No valid entity class for `{$entity}`");

        return $entity;
    }

    public function process() {
        $entity = $this->getEntityClass();

        $this->items = collect([]);
        if($this->hasRecords()){
            foreach($this->data->records as $record) { $this->items->push(new $entity($record)); }
        } else {
            $this->items->push(new $entity($this->data));
        }
    }

    public function hasRecords() {
        if(property_exists($this->data, 'records'))
            return true;

        return false;
    }

    public function count() {
        if($this->hasRecords()) {
            return count($this->data->records);
        } else {
            return 1;
        }
    }

    public function items() {
        return $this->items;
    }

    public function item() {
        return $this->items()->first();
    }
}