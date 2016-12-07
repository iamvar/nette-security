<?php

namespace Iamvar\NetteSecurity\Mocks;

use Nette\nette\security\IIdentity;

interface IIdentityGenerator
{
	/**
	 * @return IIdentity
	 */
	public function generateIdentity();
}