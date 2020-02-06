<?php
namespace App\Models\System;

use App\BaseModel;

use Email as EmailValidator;

class EmailAddress extends \App\Models\BaseModel
{

    protected $table  = 'app_system_email_address';
    protected $primaryKey = 'id';
    protected $fillable = array(
        'email',
        'valid',
        'disposible',
        'verification',
        'found',
        'validated_at'
    );

    protected static $RULES = [
        'email'=>'required:email'
    ];

    public static function getRules() {
        return self::$RULES;
    }
    
    public function validate() {
        if(app('validator')->make($this->getAttributes(), self::getRules())->fails())
            throw new \Exception("No valid email address specified");

        $response = EmailValidator::lookup($this->email);

        $this->valid = $response->isValid() ? 1 : 0;
        $this->disposible = $response->isDisposible() ? 1 : 0;
        $this->found = $response->exists() ? 1 : 0;
        $this->verification = $response->verification();
        $this->validated_at = date('Y-m-d H:i:s');
    }

    public function setEmailAttribute($value) {
        $this->attributes['email'] = strtolower($value);
    }

    public function users() {
        return $this->belongsToMany(\App\Module\User\Model\User::class, 'app_system_user_email_addresses')->withPivot('name', 'primary')->withTimestamps();
    }

    public static function findByEmail($email) {
        return self::where('email', strtolower($email))->first();
    }

    public static function findOrCreateByEmail($email) {
        # If we have a local record, check if it has been validated recently
        if(!$item = static::findByEmail($email))
            return static::createByEmail($email);

        // if 3 months ago is greater than the validated_at, that means it's been over 3 months since last validation
        if(!$item->found && $item->validated_at < date('Y-m-d H:i:s', strtotime('-3 months'))) {
            $item->validate();
            $item->save();
        }

        return $item->found ? $item : null;
         
        // throw new \App\Exceptions\EmailAddress\InvalidException("Email address `{$email}` is not valid", $email);
    }

    public static function createByEmail($email) {
        $item = new static([
            'email'=> $email
        ]);
        $item->save();
        $item->validate();
        $item->save();

        # Revise, make sure only connection errors or strange occurances are caught, anything else should bubble to the view
        // try {} catch (\Exception $e) {
        //     // No need to delete, we should throw onto the queue for
        //     // later validation in the event the request fails
        // }

        return $item->found ? $item : null;
    }

    public function getEmail() {
        return $this->email;
    }

}