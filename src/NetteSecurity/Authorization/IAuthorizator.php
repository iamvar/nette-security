<?php
namespace Iamvar\NetteSecurity\Authorization;

use Nette\nette\security\IIdentity;

/**
 * Authorizator Interface
 */
interface IAuthorizator
{
	/**
	 * Gets IIdentity and tries to authorize
	 *
	 * @param IIdentity $identity
	 * @param $route
	 * @return mixed
	 */
	public function authorize(IIdentity $identity, $route);
}