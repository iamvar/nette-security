<?php

namespace Iamvar\NetteSecurity\Mocks;

use Iamvar\NetteSecurity\IIdentityResolver;
use Iamvar\NetteSecurity\Presenters\SecuredBasePresenter;

class LocalAuthIdentityResolver implements IIdentityResolver
{
	/** @var string */
	private $signInRoute;

	public function __construct(array $args)
	{
		$this->signInRoute = $args['singInRoute'];
	}

	public function resolve(SecuredBasePresenter $presenter)
	{
		$presenter->redirect($this->signInRoute);
	}

	public function logout(SecuredBasePresenter $presenter)
	{
		$user = $presenter->getUser();
		$user->logout(TRUE);
		$presenter->redirect($this->signInRoute);
	}
}