<?php

namespace Iamvar\NetteSecurity\Util;

use Nette\InvalidArgumentException;

/**
 * Class JwtEncoder
 * To encode and decode jwt
 */
class JwtEncoder
{
	const DEFAULT_ALGO = 'RS256';

	/**
	 * @param $payload
	 * @param $key
	 * @param string $algo
	 * @return string
	 * @throws InvalidArgumentException
     */
	public function encode($payload, $key, $algo = self::DEFAULT_ALGO)
	{
		$header = [
			'typ' => 'JWT',
			'alg' => $algo
		];

		$segments = [
			$this->urlsafeB64Encode(json_encode($header)),
			$this->urlsafeB64Encode(json_encode($payload))
		];

		$signing_input = implode('.', $segments);

		$signature = $this->sign($signing_input, $key, $algo);
		$segments[] = $this->urlsafeB64Encode($signature);

		return implode('.', $segments);
	}

	/**
	 * @param $jwt
	 * @param null $key
	 * @return mixed
	 * @throws InvalidArgumentException
     */
	public function decode($jwt, $key = null)
	{
		$tks = explode('.', $jwt);

		$tokenCount = count($tks);
		if ($tokenCount !== 3) {
			throw new InvalidArgumentException("JWT format is invalid: invalid number of tokens (3 expected, $tokenCount found)");
		}

		list($headb64, $payloadb64, $cryptob64) = $tks;

		if (null === ($header = json_decode($this->urlsafeB64Decode($headb64), true))) {
			throw new InvalidArgumentException("JWT format is invalid: unable to decode header");
		}

		if (null === $payload = json_decode($this->urlsafeB64Decode($payloadb64), true)) {
			throw new InvalidArgumentException("JWT format is invalid: unable to decode payload");
		}

		$sig = $this->urlsafeB64Decode($cryptob64);

		if (!isset($header['alg'])) {
			throw new InvalidArgumentException("JWT format is invalid: 'alg' was not found in header");
		}

		if (!$this->verifySignature($sig, "$headb64.$payloadb64", $key, $header['alg'])) {
			throw new InvalidArgumentException("JWT format is invalid: signature is invalid");
		}

		return $payload;
	}

	/**
	 * @param $signature
	 * @param $input
	 * @param $key
	 * @param string $algo
	 * @return bool
	 * @throws InvalidArgumentException
     */
	private function verifySignature($signature, $input, $key, $algo = 'RS256')
	{
		switch ($algo) {
			case'HS256':
			case'HS384':
			case'HS512':
				return $this->sign($input, $key, $algo) === $signature;

			case 'RS256':
				return @openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA256) === 1;

			case 'RS384':
				return @openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA384) === 1;

			case 'RS512':
				return @openssl_verify($input, $signature, $key, OPENSSL_ALGO_SHA512) === 1;

			default:
				throw new InvalidArgumentException("Unsupported or invalid signing algorithm.");
		}
	}

	/**
	 * @param $input
	 * @param $key
	 * @param string $algo
	 * @return mixed|string
	 * @throws InvalidArgumentException
     */
	private function sign($input, $key, $algo = 'RS256')
	{
		switch ($algo) {
			case 'HS256':
				return hash_hmac('sha256', $input, $key, true);

			case 'HS384':
				return hash_hmac('sha384', $input, $key, true);

			case 'HS512':
				return hash_hmac('sha512', $input, $key, true);

			case 'RS256':
				return $this->generateRSASignature($input, $key, 'sha256');

			case 'RS384':
				return $this->generateRSASignature($input, $key, 'sha384');

			case 'RS512':
				return $this->generateRSASignature($input, $key, 'sha512');

			default:
				throw new InvalidArgumentException("Unsupported or invalid signing algorithm.");
		}
	}

	/**
	 * @param $input
	 * @param $key
	 * @param $algo
	 * @return mixed
	 * @throws InvalidArgumentException
     */
	private function generateRSASignature($input, $key, $algo)
	{
		if (!openssl_sign($input, $signature, $key, $algo)) {
			throw new InvalidArgumentException("Unable to sign data.");
		}

		return $signature;
	}

	/**
	 * @param $data
	 * @return mixed|string
     */
	public function urlSafeB64Encode($data)
	{
		$b64 = base64_encode($data);
		$b64 = str_replace(['+', '/', '\r', '\n', '='],
			['-', '_'],
			$b64);

		return $b64;
	}

	/**
	 * @param $b64
	 * @return string
     */
	public function urlSafeB64Decode($b64)
	{
		$b64 = str_replace(['-', '_'],
			['+', '/'],
			$b64);

		return base64_decode($b64);
	}
}