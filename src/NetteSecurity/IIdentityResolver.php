<?php

namespace Iamvar\NetteSecurity;

use Iamvar\NetteSecurity\Presenters\SecuredBasePresenter;
use Nette\Application\UI\Presenter;

/**
 * IdentityResolver Interface
 */
interface IIdentityResolver
{
	/**
	 * Make actions when user should be logged in
	 * @param SecuredBasePresenter $presenter
	 */
	public function resolve(SecuredBasePresenter $presenter);

	/**
	 * Logout
	 * @param SecuredBasePresenter $presenter
	 */
	public function logout(SecuredBasePresenter $presenter);
}