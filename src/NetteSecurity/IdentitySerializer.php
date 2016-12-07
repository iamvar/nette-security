<?php

namespace Iamvar\NetteSecurity;

use Iamvar\NetteSecurity\Util\JwtEncoder;
use Nette\nette\security\Identity;
use Nette\nette\security\IIdentity;
use Nette\Utils\DateTime;
use Nette\Utils\Json;

class IdentitySerializer implements IIdentitySerializer
{
	const IDENTITY_CLAIM_NAME = 'usr';

	/** @var JwtEncoder */
	private $jwtEncoder;

	/** @var string */
	private $algo;

	/**
	 * @param array|NULL $args
	 */
	public function __construct(array $args = NULL)
	{
		$this->algo = JwtEncoder::DEFAULT_ALGO;

		if ($args !== NULL && array_key_exists('algo', $args)) {
			$this->algo = $args['algo'];
		}

		$this->jwtEncoder = new JwtEncoder();
	}

	/**
	 * @param IIdentity $identity
	 * @param $privateKey
	 * @return string
	 */
	public function serialize(IIdentity $identity, $privateKey)
	{
		$claims = $this->buildClaims($identity);
		$jwt = $this->jwtEncoder->encode($claims, $privateKey, $this->algo);

		return $jwt;
	}

	/**
	 * @param string $jwt
	 * @param string|null $publicKey
	 * @return Identity
	 */
	public function deserialize($jwt, $publicKey = NULL)
	{
		if ($publicKey === NULL) {
			$publicKey = file_get_contents(__DIR__ . '/public.key');
		}

		$claims = $this->jwtEncoder->decode($jwt, $publicKey);
		$identity = $this->stringToIdentity($claims[self::IDENTITY_CLAIM_NAME]);

		return $identity;
	}

	/**
	 * @param IIdentity $identity
	 * @return array
     */
	private function buildClaims(IIdentity $identity)
	{
		// set 1 hour expiration
		$expirationDate = (new DateTime())->add(new \DateInterval('PT1H'));

		$claims = [
			'iss' => 'iamvar identity provider',
			'sub' => $identity->getId(),
			'aud' => 'iamvar applications',
			'exp' => $expirationDate->getTimestamp(),
			self::IDENTITY_CLAIM_NAME => $this->identityToString($identity)
		];

		return $claims;
	}

	/**
	 * @param IIdentity $identity
	 * @return mixed
     */
	private function identityToString(IIdentity $identity)
	{
		$identityData = [
			'id' => $identity->getId(),
			'roles' => $identity->getRoles(),
		];

		if ($identity instanceof Identity) {
			$identityData['data'] = $identity->getData();
		}

		return Json::encode($identityData, Json::FORCE_ARRAY);
	}

	/**
	 * @param $data
	 * @return Identity
     */
	private function stringToIdentity($data)
	{
		$identityData = Json::decode($data, Json::FORCE_ARRAY);

		return new Identity(
			$identityData['id'],
			$identityData['roles'],
			array_key_exists('data', $identityData) ? $identityData['data'] : NULL);
	}
}