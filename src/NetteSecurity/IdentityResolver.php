<?php

namespace Iamvar\NetteSecurity;

use Iamvar\NetteSecurity\Presenters\SecuredBasePresenter;

class IdentityResolver implements IIdentityResolver
{
	public function __construct()
	{
	}

	public function resolve(SecuredBasePresenter $presenter)
	{
		$user = $presenter->getUser();
		if (!$user->isLoggedIn()) {
			$presenter->redirectToLogin();
		}
	}

	public function logout(SecuredBasePresenter $presenter)
	{
		$user = $presenter->getUser();
		$user->logout();
		$presenter->flashMessage('You have been signed out.');
		$presenter->redirectToLogout($presenter->getDefaultActionUrl());
	}
}