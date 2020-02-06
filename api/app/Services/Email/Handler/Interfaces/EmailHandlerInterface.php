<?php

namespace App\Services\Email\Handler\Interfaces;

interface EmailHandlerInterface {
	public function lookup($recipient);
}