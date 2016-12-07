<?php

namespace Iamvar\NetteSecurity\Presenters;

use Nette;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;

abstract class UnsecuredBasePresenter extends Presenter
{
	/**
	 * @var Nette\Application\IRouter
	 */
	protected $router;

	/**
	 * @param string|null $redirectUrl
	 */
	public function redirectToLogin($redirectUrl = NULL)
	{
		if ($redirectUrl === NULL) {
			$redirectUrl = $this->getHttpRequest()->getUrl()->getAbsoluteUrl();
		}

		$this->getSession('default')->$redirectUrl = $redirectUrl;
		$consumeUrl = $this->link('//:' . $this->getSsoParameter('consumeAction'));
		$this->redirectUrl($this->getSsoParameter('loginUrl') . '?acsUrl=' . $consumeUrl);
	}

	/**
	 * @param string|null $redirectUrl
	 */
	public function redirectToLogout($redirectUrl = NULL)
	{
		if ($redirectUrl === NULL) {
			$redirectUrl = $this->getDefaultActionUrl();
		}

		$this->getSession('default')->$redirectUrl = $redirectUrl;
		$this->redirectUrl($this->getSsoParameter('logoutUrl') . '?redirectTo=' . $redirectUrl);
	}

	/**
	 * @return string
	 */
	public function getDefaultActionUrl()
	{
		return $this->link('//:' . $this->getSsoParameter('defaultAction'));
	}

	/**
	 * @param string $parameter
	 * @return mixed
	 */
	private function getSsoParameter($parameter)
	{
		return $this->context->getParameters()['sso'][$parameter];
	}

	/**
	 * @param IRouter $router
	 * @throws \Exception
	 */
	public function injectRouter(IRouter $router)
	{
		if ($this->router !== NULL) {
			throw new \Exception('Router cannot be injected twice.');
		}

		$this->router = $router;
	}
}