<?php
namespace App\Services\Twilio\Handler;

use App\Services\Message\Handler\Interfaces\MessageHandlerInterface;

use App\Models\System\Phone;

use Illuminate\Contracts\Cache\Repository as CacheStoreContract;
class AccountHandler 
{
    protected $account = null;
    protected $client = null;
    protected $cache = null;

    public function __construct($client, CacheStoreContract $cache) {
        $this->client = $client;
        $this->cache = $cache;
    }

    public function setAccount($account) {
        $this->account = $account;
    }

    public function getAccount() {
        if($this->account === null)
            throw new \Exception("No account was set on this handler");
        
        return $this->account;
    }

    public function getServiceAccount() {
        return null;
    }

    public function hasSubscription() {
        return false;
    }

    public function setAuth($data, $minutes) { }
    public function getAuth() {}

    public function getAuthKey() {}
    public function setAuthKey($key) {}
    
    public function isAuthenticated() {
        return true;
    }

    public function authenticate($account = null) {}

    protected function hasFeature($featureKey) {}

    public function sms(Phone $to, $message, Phone $from) {
        $response = $this->client->messages->create(
            $to->getNumber(),
            [
                'from' => '+'.$from->getNumber(),
                'body' => $message,
                'statusCallback' => route('twilio.message.status.receive')
            ]
        );

        return new \App\Services\Twilio\Response\Sms($response);
    }
}