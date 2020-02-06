<?php

namespace App\Services\Phone\Response\Interfaces;

interface LookupResponseInterface {
    public function getType();
    public function isMobile();
    public function getNumber();
    public function getDigits();
    public function getCountryCode();
}