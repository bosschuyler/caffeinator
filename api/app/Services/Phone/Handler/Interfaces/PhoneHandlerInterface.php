<?php

namespace App\Services\Phone\Handler\Interfaces;

interface PhoneHandlerInterface {
	public function lookup($recipient);
}