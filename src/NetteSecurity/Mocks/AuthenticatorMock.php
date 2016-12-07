<?php

namespace Iamvar\NetteSecurity\Mocks;

use Nette\nette\security\AuthenticationException;
use Nette\nette\security\IAuthenticator;
use Nette\nette\security\IIdentity;

class AuthenticatorMock implements IAuthenticator
{
	/** @var array */
	private $validCredentials;

	/** @var IdentityGenerator */
	private $identityGenerator;

	public function __construct(IIdentityGenerator $identityGenerator, array $args)
	{
		$this->identityGenerator = $identityGenerator;
		$this->mocks = $args['validCredentials'];
	}

	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 * @param array $credentials
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		if (!isset($this->mocks[$username]) || $password !== $this->mocks[$username]['password']) {
			throw new AuthenticationException('Username and/or password are invalid');
		}

		$this->identityGenerator->setUsername($username);
		return $this->identityGenerator->generateIdentity();
	}
}