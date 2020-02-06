<?php
namespace App\Module\User\Model;

use DB;

use App\Models\System\Role;
use App\Models\System\Phone;
use App\Models\System\EmailAddress;
use App\Models\System\Address;
use App\Models\System\Module;

use App\Exceptions\EmailAddress\InvalidException;

class User extends \App\Models\BaseModel
{
    protected static $referenceKey = 'user';

    protected $has_internal_role = null;
    protected $has_lending_access = null;
    protected $owner = null;

    protected $table  = 'app_system_user';
    protected $primaryKey = 'id';
    protected $fillable = array(
        'id',
        'password',
        'password_expires_at',
        'first_name',
        'last_name',
        'status'
    );

    protected $attributes = [
        'status'=>'active',
        'password'=>null
    ];

    protected static $RULES = [
        'first_name'=>'required',
        'last_name'=>'required'
    ];

    protected $module_permission = [];

    public static function getRules() {
        return self::$RULES;
    }

    public static function getPasswordRule() {
        return 'required|min:8|has_number|has_lowercase|has_uppercase';
    }
	
	public function getCreatedAt() {
		return $this->created_at;
	}
	
	public function getUpdatedAt() {
		return $this->updated_at;
    }
    
    public function passwordExpire() {
        $this->password_expires_at = date('Y-m-d H:i:s');
    }
    public function isPasswordExpired() {
        return $this->password_expires_at && $this->password_expires_at < date('Y-m-d H:i:s');
    }

    public function isActive() {
        return ($this->status == 'active');
    }

    public function isInactive() {
        return ($this->status == 'inactive');
    }

    public static function active($query=null) {
        if(!$query)
            $query = static::query();

        return $query->where('status', 'active');
    }

    public function json() {
        return json_encode($this->export());
    }

    public function export() {
        $data = $this->getAttributes();
        $emails = $this->emailAddresses()->get();
        $primaryEmail = $emails->where('pivot.primary', 1)->first();

        $data['email'] = $primaryEmail ? $primaryEmail->getEmail() : null;
        $data['emails'] = $emails->map(function($item) { return $item->getAttributes(); })->all();
        $data['name'] = $this->getName();

        return $data;
    }

    /*====================================
        Relationships
    ======================================*/
        
        public function apiKeys() {
            return $this->belongsToMany(\App\Models\Api\Key::class, 'app_system_api_user', 'user_id', 'api_key_id');
        }

        public function emailAddresses() {
            return $this->belongsToMany(EmailAddress::class, 'app_system_user_email_addresses')->withPivot('name', 'primary')->withTimestamps();
        }

    public static function findByEmail($email) {
        return static::whereHas('emailAddresses', function($query) use ($email) {
            $query->where('email', $email);
        })->first();
    }

    public static function createByEmail($email, $password=null) {
        $user = new User();

        if(!$emailAddress = \App\Models\System\EmailAddress::findOrCreateByEmail($email))
            throw new InvalidException("Invalid email address `{$email}`", $email);

        $user->email = $email;                
        $user->password = $password;
        $user->save();

        $user->addEmailAddress($emailAddress, true);

        return $user;
    }

    public static function findOrCreateByEmail($email) {
        if(!$user = static::findByEmail($email))
            $user = static::createByEmail($email);

        return $user;
    }

    public function isRegistered() {
        return $this->password !== null && $this->emailAddresses->count();
    }

    public function isNameComplete() {
        if(!$this->getFirstName())
            return false;

        if(!$this->getLastName())
            return false;

        return true;
    }

    public function isComplete() {
        if(!$this->isNameComplete()) {
            return false;
        }

        if(!$this->emailAddresses->count())
            return false;
        
        return true;
    }

    # Cannot make changes to primary information using automated processes
    public function isLocked() {
        if($this->isComplete() && $this->isRegistered()) {
            return true;
        } else {
            return false;
        }
    }

    public function hasProblems() {
        if(!$this->getFirstName())
            return true;

        if(!$this->getLastName())
            return true;
    }

    public function setPasswordAttribute($value) {
        $this->attributes['password'] = $value ? encrypt($value) : null;
    }

    public function getPassword() {   
        try {
            return $this->password ? decrypt($this->password) : null;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return null;
        }
    }

    public function getPrimaryEmailAddress() {
        return $this->emailAddresses->where('pivot.primary', 1)->first();
    }

    public function hasEmail($email) {
        return $this->emailAddresses->where('email', $email)->count();
    }

    public function addEmailAddress(EmailAddress $newEmailAddress, $primary=null, $name=null) {        
        if(!is_bool($primary))
            throw new \Exception("Primary must be of type boolean.");

        $existingEmails = $this->emailAddresses()->get();

        if(!$existingEmails->count())
            $primary = 1;

        $sync = $existingEmails->mapWithKeys_v2(function($existing) use ($primary) {
            return [ $existing->getKey() => ['primary'=> $primary ? 0 : $existing->pivot->primary, 'name'=>$existing->pivot->name ] ];
        });

        $sync->put( $newEmailAddress->getKey(), ['primary'=>$primary, 'name'=>$name] );

        // check if phone is to be the new primary phone?
        $this->emailAddresses()->sync($sync->all()); 
    }

    public function getFirstName() {
        return $this->first_name;
    }
	
	public function getLastName() {
        return $this->last_name;
    }

    public function getName() {
        return trim($this->getFirstName().' '.$this->getLastName());
    }

    public function setEmailAttribute($value) {
        $this->attributes['email'] = strtolower($value);
    }

    public function getEmail() {
        // return $this->email;
        if($this->getPrimaryEmailAddress())
            return $this->getPrimaryEmailAddress()->getEmail();
        else
            return '';
    }

    public function getEmails() {
		$emails = $this->emailAddresses()->get();
		if($emails->count()) {
			return $emails->pluck('email')->all();
		}
        return [];
    }
	

	
}