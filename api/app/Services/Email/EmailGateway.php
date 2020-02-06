<?php
namespace App\Services\Email;

use App\Services\Email\Handler\Interfaces\EmailHandlerInterface;

class EmailGateway {

	protected $handlers = [];
	protected $defaultHandler = null;
    
    protected $classHandlers = [];
    protected $defaultClassHandler = null;

	public function __construct() {}

	public function addHandler($key, EmailHandlerInterface $handler) {
        if(!$handler)
            throw new \Exception("Missing handler to add");

		$this->handlers[$key] = $handler;

		if($this->defaultHandler === null) {
			$this->defaultHandler = $key;
		}
	}

	public function getHandler($key) {
		if(!isset($this->handlers[$key])) {
			throw new \Exception("Invalid Handler: '".$key."'");
		}
		return $this->handlers[$key];
    }
    
    public function addClassificationHandler($key, EmailHandlerInterface $handler) {
        $this->classHandlers[$key] = $handler;
        
        if($this->defaultClassHandler === null)
            $this->defaultClassHandler = $handler;
	}

	public function getClassificationHandler($key) {
		if(!isset($this->classHandlers[$key])) {
			throw new \Exception("Invalid Handler: '".$key."'");
		}
		return $this->classHandlers[$key];
	}

	public function lookup($recipient, $handlerName=null) {
		if($handlerName===null)
            $handlerName = $this->defaultHandler;
        $handler = $this->getHandler($handlerName);
		return $handler->lookup($recipient);
    }
    
    public function classification($recipient, $handlerName=null) {
        if($handlerName===null) {
			// use global default account
			$handler = $this->defaultClassHandler;
		} else {
            $handler = $this->getClassificationHandler($handlerName);
        }
		
		return $handler->lookup($recipient);
    }

}