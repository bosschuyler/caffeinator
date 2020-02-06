<?php
namespace App\Services\Email\Handler;

use App\Services\Email\Handler\Interfaces\EmailHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

use App\Services\Email\Response\MailgunLookupResponse;

use Exception;
use Mail;

class MailgunEmailHandler implements EmailHandlerInterface
{
    protected $account = null;
    protected $client = null;

    public function __construct($client) {
        $this->client = $client;
    }

    public function lookup($recipient) {
        try {
			//$this->client->setApiVersion('v3');
			//$this->client->setSslEnabled(false);
            $data = $this->client->get('address/validate', ['address'=>$recipient, 'mailbox_verification'=>'true']);
            return new MailgunLookupResponse($data);
        } catch (Exception $e) {
            Mail::send( (new \App\Mail\ExceptionNotification($e, 'Mailgun Lookup Error'))->onQueue('communication') );
            throw $e;
        }
    }
}