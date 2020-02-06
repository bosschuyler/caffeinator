<?php

namespace App\Services\Email\Response\Interfaces;

interface LookupResponseInterface {
    public function isDisposible();
    public function isValid();
    public function exists();
    public function getEmail();
}