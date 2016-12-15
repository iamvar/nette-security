<?php

namespace Iamvar\NetteSecurity\Presenters;

use Nette;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;
use Iamvar\NetteSecurity\IIdentitySerializer;
use Nette\nette\security\User;

abstract class AssertionConsumerBasePresenter extends UnsecuredBasePresenter
{
	/** @var IIdentitySerializer */
	protected $identitySerializer;

	/**
	 * Action where identity request will be processed
	 */
	public function actionConsume()
	{
		$request = $this->getRequest();

		if (!$request->isMethod('POST')) {
			$this->redirectToLogin();
		}

		$postData = $request->getPost();
		$jwt = $postData['identity'];
		try {
			$identity = $this->identitySerializer->deserialize($jwt);
			$this->user->login($identity);
			$this->onUserLogin($this->user);
		} catch (Nette\InvalidArgumentException $e) {
			$this->redirectToLogin();
		}

		if (($url = $this->getSession('default')->redirectUrl) !== NULL) {
			if ($this->getLoginUrl !== $url) {
				$this->redirectUrl($url);
			}
		}

		$this->redirectUrl($this->getDefaultActionUrl());
	}

	/**
	 * Allows child classes to put some logic when user logged in successfully
	 *
	 * @param User $user
	 */
	protected function onUserLogin(User $user)
	{

	}

	/**
	 * @param IIdentitySerializer $identitySerializer
	 * @throws \Exception
	 */
	public function injectIdentitySerializer(IIdentitySerializer $identitySerializer)
	{
		if ($this->identitySerializer !== NULL) {
			throw new \Exception('Identity serializer cannot be injected twice.');
		}

		$this->identitySerializer = $identitySerializer;
	}
}