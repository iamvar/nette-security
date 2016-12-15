<?php

namespace Iamvar\NetteSecurity;

use Nette\nette\security\IIdentity;

/**
 * Identity Serializer Interface.
 */
interface IIdentitySerializer
{
	/**
	 * Serializes the given identity to the specified output format.
	 *
	 * @param IIdentity $identity
	 * @param $privateKey
	 * @return string
	 */
	public function serialize(IIdentity $identity, $privateKey);

	/**
	 * Deserializes the given token to the Identity.
	 *
	 * @param string $token
	 * @param string|null $publicKey
	 * @return IIdentity
	 */
	public function deserialize($token, $publicKey = NULL);
}