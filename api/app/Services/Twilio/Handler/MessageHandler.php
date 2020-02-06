<?php
namespace App\Services\Twilio\Handler;

use App\Services\Message\Handler\Interfaces\MessageHandlerInterface;

use App\Helpers\Phone;

class MessageHandler implements MessageHandlerInterface
{
    protected $account = null;
    protected $client = null;

    public function __construct($client) {
        $this->client = $client;
    }

    public function authenticate($account) {}

    protected function hasFeature($featureKey) {}

    public function sms($recipient, $message, $account) {
        try {
            $response = $this->client->messages->create(
                // the number you'd like to send the message to
                Phone::clean($recipient),
                array(
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => '+'.$account->account,
                    // the body of the text message you'd like to send
                    'body' => $message
                )
            );
        } catch(Exception $e) {
            // handle this exception
            throw $e;
        }

        // verify API response for the send message;
        return $response;
    }
}