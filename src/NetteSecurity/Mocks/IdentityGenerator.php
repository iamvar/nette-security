<?php

namespace Iamvar\NetteSecurity\Mocks;

use Nette\InvalidArgumentException;
use Nette\nette\security\Identity;
use Nette\nette\security\IIdentity;
use Nette\DI\Config\Helpers;

class IdentityGenerator implements IIdentityGenerator
{
	/**
	 * @var array
	 */
	private $mocks;

	/**
	 * @var array
	 */
	private $parameters;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @param array $args
	 */
	public function __construct(array $args)
	{
		if (!isset($args['validCredentials'])) {
			throw new \InvalidArgumentException(
				'Required parameters missing: validCredentials (Read documentation)'
			);
		}

		$this->mocks = $args['validCredentials'];
	}

	/**
	 * @return array
	 */
	protected function getDefaultParameters()
	{
		return [
			'id' => $this->username . '@email.local',
			'roles' => [],
			'data' => [
				'username' => $this->username,
				'userinfo' => [
					'lastName' => $this->username,
					'firstName' => $this->username,
					'fullName' => $this->username . ' ' . $this->username,
				],
			]
		];
	}

	/**
	 * @param $username
	 * @return $this
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * @return IIdentity
	 */
	public function generateIdentity()
	{
		$this->parameters = $this->getDefaultParameters();
		if (array_key_exists('parameters', $this->mocks[$this->username]) ) {
			$this->parameters = Helpers::merge(
				$this->mocks[$this->username]['parameters'],
				$this->parameters
			);
		}
		return new Identity(
			$this->parameters['id'],
			$this->parameters['roles'],
			$this->parameters['data']
		);
	}
}