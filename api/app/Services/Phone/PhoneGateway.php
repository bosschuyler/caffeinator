<?php
namespace App\Services\Phone;

use App\Services\Phone\Handler\Interfaces\PhoneHandlerInterface;

class PhoneGateway {

	protected $handlers = [];
	protected $defaultHandler = null;
	protected $defaultAccount = null;

	public function __construct($defaultAccount = null) {
		$this->defaultAccount = $defaultAccount;
	}

	public function addHandler($key, PhoneHandlerInterface $handler) {
		$this->handlers[$key] = $handler;

		if($this->defaultHandler === null) {
			$this->defaultHandler = $handler;
		}
	}

	public function getHandler($key) {
		if(!isset($this->handlers[$key])) {
			throw new Exception("Invalid Handler: '".$key."'");
		}
		return $this->handlers[$key];
	}

	public function lookup($recipient, $handlerName=null) {
		if($handlerName===null) {
			// use global default account
			$handler = $this->defaultHandler;
		} else {
            $handler = $this->getHandler($handlerName);
        }
		
		return $handler->lookup($recipient);
	}
}