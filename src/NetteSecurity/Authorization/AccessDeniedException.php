<?php

namespace Iamvar\NetteSecurity\Authorization;

use Nette\Application\BadRequestException;

class AccessDeniedException extends BadRequestException
{
	/**
	 * @param string $message
	 */
	public function __construct($message)
	{
		parent::__construct($message, 403);
	}
}