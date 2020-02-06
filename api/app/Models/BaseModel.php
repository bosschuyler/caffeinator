<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseModel extends Eloquent {
    
    // attributes that once set will not be overwritten in mass assignment
    protected $reserved_attributes = [];
    
    public function fill(array $attributes) {
        $reserved = $this->reserved_attributes;
        
        foreach($reserved as $key) {
            if($this->key !== null && $this->key != '') {
                unset($attributes[$key]);
            }
        }

        parent::fill($attributes);
    }

    public function appendReservedAttributes($attributes = []) {
        $full = array_merge($this->reserved_attributes, $attributes);
        array_unique($full);
        $this->reserved_attributes = $full;
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
	}
    
    public static function keyName()
    {
        return with(new static)->getKeyName();
    }
    
    public function getDate($key) {
        if ($this->$key instanceof \DateTime) {
            return $this->$key->format('Y-m-d H:i:s');
        }

        if($this->$key && strtotime($this->$key) > 0) {
            return date('Y-m-d H:i:s', strtotime($this->$key));
        }
        return false;
    }
    
}