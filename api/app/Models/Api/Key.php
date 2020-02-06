<?php
namespace App\Models\Api;

use App\BaseModel;
use Auth;
use Exception;
use App\Models\SettingsVariables;

class Key extends \App\Models\BaseModel
{
    protected $table  = 'app_system_api_key';
    protected $primaryKey = 'id';
    protected $fillable = array(
        'id',
        'key',
        'type'
    );

    public function users() {
        return $this->belongsToMany(\App\Module\User\Model\User::class, 'app_system_api_user', 'api_key_id', 'user_id');
    }
}
