<?php
namespace Iamvar\NetteSecurity\Authorization;

use Nette\nette\security\IIdentity;

interface IAuthorizator
{
	/**
	 * @param IIdentity $identity
	 * @param $route
	 * @return mixed
	 */
	public function authorize(IIdentity $identity, $route);
}