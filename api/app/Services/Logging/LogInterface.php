<?php
namespace App\Logging;

interface LogInterface {
    public function log($priority, $message, $data = null);

    public function error($message, $data = null);

    public function debug($message, $data = null);

    
}