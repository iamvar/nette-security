<?php

namespace Iamvar\NetteSecurity\Presenters;

use Iamvar\NetteSecurity\authorization\IAuthorizator;
use Iamvar\NetteSecurity\IIdentityResolver;
use Nette;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;

/**
 * Base class for secured routes (that needs identity to be checked)
 */
abstract class SecuredBasePresenter extends UnsecuredBasePresenter
{
	/** @var IIdentityResolver */
	private $identityResolver;

	/** @var IAuthorizator */
	private $authorizator;

	/**
	 * Startup
	 */
	protected function startup()
	{
		parent::startup();

		$this->identityResolver->resolve($this);

		if ($this->authorizator !== NULL) {
			$this->authorizator->authorize(
				$this->getUser()->getIdentity(),
				$this->getAction(TRUE)
			);
		}
	}

	/**
	 * Logout action
	 */
	public function actionOut()
	{
		$this->identityResolver->logout($this);
	}

    /**
	 * Checks if user is in a specified group
	 *
     * @param string $group
     * @return bool
     */
    protected function isUserInGroup($group)
    {
        return !empty($this->user->getIdentity()->data['groups'])
            && in_array($group, $this->user->getIdentity()->data['groups']);
    }

	/**
	 * @param IAuthorizator $authorizator
	 */
	public function injectAuthorizator(IAuthorizator $authorizator)
	{
		$this->authorizator = $authorizator;
	}

	/**
	 * @param IIdentityResolver $identityResolver
	 * @throws \Exception
	 */
	public function injectIdentityResolver(IIdentityResolver $identityResolver)
	{
		if ($this->identityResolver !== NULL) {
			throw new \Exception('Identity resolver cannot be injected twice.');
		}

		$this->identityResolver = $identityResolver;
	}
}