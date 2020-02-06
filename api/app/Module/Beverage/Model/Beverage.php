<?php
namespace App\Module\Beverage\Model;

class Beverage extends \App\Models\BaseModel
{
    protected $table  = 'beverage';
    protected $primaryKey = 'id';
    protected $fillable = array(
        'id',
        'name',
        'caffiene',
        'measure',
        'status'
    );
}