<?php

namespace Iamvar\NetteSecurity\Mocks;

use Iamvar\NetteSecurity\IIdentityResolver;
use Iamvar\NetteSecurity\Presenters\SecuredBasePresenter;

class IdentityResolverMock implements IIdentityResolver
{
	/** @var IIdentityGenerator */
	private $identityGenerator;

	public function __construct(IIdentityGenerator $identityGenerator)
	{
		$this->identityGenerator = $identityGenerator;
	}

	public function resolve(SecuredBasePresenter $presenter)
	{
		$user = $presenter->getUser();
		if (!$user->isLoggedIn()) {
			$user->login($this->identityGenerator->generateIdentity());
		}
	}

	public function logout(SecuredBasePresenter $presenter)
	{
		$user = $presenter->getUser();
		$user->logout();
		$presenter->redirectUrl($presenter->getDefaultActionUrl());
	}
}