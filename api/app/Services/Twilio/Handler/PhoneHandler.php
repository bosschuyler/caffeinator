<?php
namespace App\Services\Twilio\Handler;

use App\Helpers\Phone as PhoneHelper;

use App\Services\Phone\Handler\Interfaces\PhoneHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

use App\Services\Twilio\Response\LookupResponse;

use Twilio\Exceptions\RestException;

class PhoneHandler implements PhoneHandlerInterface
{
    protected $account = null;
    protected $client = null;

    public function __construct($client) {
        $this->client = $client;
    }

    public function lookup($recipient) {
        try {
            $number = $this->client->lookups
            ->phoneNumbers(PhoneHelper::digits($recipient))
            ->fetch(
                array("type" => "carrier")
            );
            return new LookupResponse($number);
        } catch (RestException $e) {
            if ($e->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return false;
            }
        }
    }
}