<?php

namespace Iamvar\NetteSecurity\Presenters;

use Iamvar\NetteSecurity\authorization\IAuthorizator;
use Iamvar\NetteSecurity\IIdentityResolver;
use Nette;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;

abstract class SecuredBasePresenter extends UnsecuredBasePresenter
{
	/** @var IIdentityResolver */
	private $identityResolver;

	/** @var IAuthorizator */
	private $authorizator;

	/**
	 * startup
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
	 * logout action
	 */
	public function actionOut()
	{
		$this->identityResolver->logout($this);
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

	/**
	 * @param IAuthorizator $authorizer
	 */
	public function setAuthorizator(IAuthorizator $authorizer)
	{
		$this->authorizator = $authorizer;
	}
	
    /**
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
}