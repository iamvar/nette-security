<?php

namespace Iamvar\NetteSecurity;

use Iamvar\NetteSecurity\Presenters\SecuredBasePresenter;

class IdentityResolver implements IIdentityResolver
{
	public function __construct()
	{
	}

	/**
	 * If user is not logged in - redirect to login page
	 * @param SecuredBasePresenter $presenter
     */
	public function resolve(SecuredBasePresenter $presenter)
	{
		$user = $presenter->getUser();
		if (!$user->isLoggedIn()) {
			$presenter->redirectToLogin();
		}
	}

	/**
	 * Logout
	 * @param SecuredBasePresenter $presenter
     */
	public function logout(SecuredBasePresenter $presenter)
	{
		$user = $presenter->getUser();
		$user->logout();
		$presenter->flashMessage('You have been signed out.');
		$presenter->redirectToLogout(
			$presenter->getDefaultActionUrl()
		);
	}
}