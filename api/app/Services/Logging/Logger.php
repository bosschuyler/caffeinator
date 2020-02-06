<?php
namespace App\Services\Logging;

use Monolog\Logger as MonologLogger;
use Exception;

class Logger {

    protected $log = null;
    protected $debug = null;

    public function __construct(MonologLogger $log) {
        $this->log = $log;
        $this->debug = config('log.debug');
    }

    public function enableDebug() {
        $this->debug = 1;
        return $this;
    }

    public function disableDebug() {
        $this->debug = 0;
        return $this;
    }

    public function emergency($message, $data = array())
    {
        $this->log($message, MonologLogger::EMERGENCY, $data);
    }

    public function alert($message, $data = array())
    {
        $this->log($message, MonologLogger::ALERT, $data);
    }

    public function critical($message, $data = array())
    {
        $this->log($message, MonologLogger::CRITICAL, $data);
    }

    public function error($message, $data = array())
    {
        $this->log($message, MonologLogger::ERROR, $data);
    }

    public function warning($message, $data = array())
    {
        $this->log($message, MonologLogger::WARNING, $data);
    }

    public function notice($message, $data = array())
    {
        $this->log($message, MonologLogger::NOTICE, $data);
    }

    public function info($message, $data = array())
    {
        if($this->debug)
            $this->log($message, MonologLogger::INFO, $data);
    }

    public function debug($message, $data = array())
    {
        if($this->debug)
            $this->log($message, MonologLogger::DEBUG, $data);
    }

    public function exception(Exception $exception)
    {
        $this->log($exception->getMessage(), MonologLogger::ERROR);
    }

    public function log($message, $priority = Logger::ERROR, $data = array()) {
        if (is_array($message) || is_object($message)) {
            $data = $message;
            $message = ":::: DATA ::::";
            // $message = json_encode($message);
        } else {
            $message = $message."\n";
        }

        if(is_object($data)) {
            if ($data instanceof \Exception) {
                $data = \App\Helpers\Exception::normalize($data, true);
            } else {
                $data = get_object_vars($data);  
            }
        }

        if(is_string($data)) {
            $message .= $data."\n";
            $data = array();
        }

        if(is_bool($data)) {
            $data = ['bool', $data];
        }

        if (empty($message)) {
            return false;
        }


        $this->log->log($priority, $message, $data);
    }

}

