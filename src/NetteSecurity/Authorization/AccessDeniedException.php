<?php

namespace Iamvar\NetteSecurity\Authorization;

use Nette\Application\BadRequestException;

/**
 * Class AccessDeniedException
 * Specific exception should be used when Access is Denied
 */
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