<?php

namespace Iamvar\NetteSecurity\Authorization;

use Nette\nette\security\IIdentity;

/**
 * Class Authorizator
 * checks that user in allowed or deny lists
 */
class Authorizator implements IAuthorizator
{
	/**
	 * List of allowed groups
	 * @var array
	 */
	private $allowedGroups;

	/**
	 * List of allowed users
	 * @var array
	 */
	private $allowedUsers;

	/**
	 * List of deny groups
	 * @var array
     */
	private $denyGroups;

	/**
	 * List of deny users
	 * @var array
	 */
	private $denyUsers;

	/**
	 * List of routes where permissions lists should be skipped (404 for example)
	 * @var array
	 */
	private $insecureRoutes = [];

	/**
	 * @param array $accessRights
	 */
	public function __construct(array $accessRights)
	{
		if (isset($accessRights['insecureRoutes'])) {
			$this->insecureRoutes = (array) $accessRights['insecureRoutes'];
		}

		if (isset($accessRights['allow'])) {
			$this->allowedGroups = $this->getAccessSettings($accessRights['allow'], 'groups');
			$this->allowedUsers = $this->getAccessSettings($accessRights['allow'], 'users');
		}

		if (isset($accessRights['deny'])) {
			$this->denyGroups = $this->getAccessSettings($accessRights['deny'], 'groups');
			$this->denyUsers = $this->getAccessSettings($accessRights['deny'], 'users');
		}
	}

	/**
	 * @param IIdentity $identity
	 * @param $route
	 * @return mixed|void
	 * @throws AccessDeniedException
	 */
	public function authorize(IIdentity $identity, $route)
	{
		if ($this->isInsecureRoute($route)) {
			return;
		}

		if (!($identity instanceof IIdentity)) {
			throw new AccessDeniedException('User identity has invalid type: instance of Nette\nette\security\IIdentity expected)');
		}

		$userData = $identity->getData();

		$userDeny = $this->denyUsers !== NULL && $this->searchInUsers($this->denyUsers, $userData);
		$groupDeny = $this->denyGroups !== NULL && $this->searchInGroups($this->denyGroups, $userData);

		if ($userDeny || $groupDeny) {
			throw new AccessDeniedException('Current identity is in deny list');
		}

		$userAllowed = $this->allowedUsers === NULL || $this->searchInUsers($this->allowedUsers, $userData);
		$groupAllowed = $this->allowedGroups === NULL || $this->searchInGroups($this->allowedGroups, $userData);

		if (!$userAllowed || !$groupAllowed) {
			throw new AccessDeniedException('Current identity is not in allowed list');
		}
	}

	/**
	 * @param $route
	 * @return bool
	 */
	private function isInsecureRoute($route)
	{
		foreach ($this->insecureRoutes as $insecureRoute) {
			if (strpos($route, $insecureRoute) === 0) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * @param $accessRights
	 * @param $setName
	 * @return array|null
	 */
	private function getAccessSettings($accessRights, $setName)
	{
		$result = NULL;
		if (isset($accessRights[$setName])) {
			$accessRights[$setName] = (array) $accessRights[$setName];
			$result = array_map('strtolower', $accessRights[$setName]);
		}

		return $result;
	}

	/**
	 * @param $users
	 * @param $userData
	 * @return bool
	 */
	private function searchInUsers($users, $userData)
	{
		if (!isset($userData['username'])) {
			return FALSE;
		}

		$userPrincipalName = strtolower($userData['username']);

		return in_array($userPrincipalName, $users);
	}

	/**
	 * @param $groups
	 * @param $userData
	 * @return bool
	 */
	private function searchInGroups($groups, $userData)
	{
		if (!isset($userData['group'])) {
			return FALSE;
		}

		foreach ($userData['group'] as $groupName) {
			if (in_array(strtolower($groupName), $groups)) {
				return TRUE;
			}
		}

		return FALSE;
	}
}