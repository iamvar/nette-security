<?php

namespace Iamvar\NetteSecurity;

use Iamvar\NetteSecurity\Presenters\SecuredBasePresenter;
use Nette\Application\UI\Presenter;

interface IIdentityResolver
{
	public function resolve(SecuredBasePresenter $presenter);

	public function logout(SecuredBasePresenter $presenter);
}