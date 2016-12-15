<?php

namespace Iamvar\NetteSecurity\Presenters;

use Nette;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Application\UI\Presenter;

/**
 * Presenter for unsecured routes
 */
abstract class UnsecuredBasePresenter extends Presenter
{
	/**
	 * @var Nette\Application\IRouter
	 */
	protected $router;

	/**
	 * Redirect to login page that can be specified in configuration file
	 *
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
	 * Redirect to logout page that can be specified in configuration file
	 *
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
	 * Returns default action url based on configuration file
	 *
	 * @return string
	 */
	public function getDefaultActionUrl()
	{
		return $this->link('//:' . $this->getSsoParameter('defaultAction'));
	}

	/**
	 * Get specific sso parameter from config
	 *
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