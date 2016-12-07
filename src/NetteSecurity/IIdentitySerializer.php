<?php

namespace Iamvar\NetteSecurity;

use Nette\nette\security\Identity;
use Nette\nette\security\IIdentity;

interface IIdentitySerializer
{
	/**
	 * @param IIdentity $identity
	 * @param $privateKey
	 * @return string
	 */
	public function serialize(IIdentity $identity, $privateKey);

	/**
	 * @param string $jwt
	 * @param string|null $publicKey
	 * @return Identity
	 */
	public function deserialize($jwt, $publicKey = NULL);
}