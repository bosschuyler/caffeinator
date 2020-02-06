<?php
namespace App\Services\Email\Handler;

use App\Services\Email\Handler\Interfaces\EmailHandlerInterface;
use Symfony\Component\HttpFoundation\Response;

use App\Services\Email\Response\ImpressionWiseLookupResponse;

use GuzzleHttp\Client as GuzzleClient;

use Exception;
use Mail;

class ImpressionWiseEmailHandler implements EmailHandlerInterface
{
    protected $user = null;
    protected $password = null;

    public function __construct($user, $password) {
        $this->user = $user;
        $this->password = $password;
    }

    public function lookup($recipient) {
        try {
            $client = new GuzzleClient;
            $guzzleResponse = $client->get("https://post.impressionwise.com/verifyme.aspx?CODE={$this->user}&PWD={$this->password}&EMAIL={$recipient}");
            
            if(in_array($guzzleResponse->getStatusCode(), [200, 204]))
            {
                $data = json_decode($guzzleResponse->getBody(), true);
                if($data === null)
                    throw new Exception("Malformed json: `{$guzzleResponse->getBody()}`");

                $response = new ImpressionWiseLookupResponse($data);
                return $response;
            } else {
                throw new Exception("Invalid status code returned: `{$guzzleResponse->getStatusCode()}`");
            }
        } catch (Exception $e) {
            Mail::send( (new \App\Mail\ExceptionNotification($e, 'ImpressionWise Lookup Error'))->onQueue('communication') );
            throw $e;
        }
    }
}