<?php namespace ReallySimpleJWT;

use ReallySimpleJWT\Helper\Signature;
use ReallySimpleJWT\Helper\Base64;
use Carbon\Carbon;

class TokenBuilder extends TokenAbstract
{
	private $type = 'JWT';

	private $secret;

	private $expiration;

	private $issuer;

	private $audience;

	private $subject;

	private $payload = [];

	private $signature;	

	public function getType()
	{
		return $this->type;
	}

	public function getSecret()
	{
		return $this->secret;
	}

	public function getExpiration()
	{
		return $this->expiration;
	}

	public function getIssuer()
	{
		return $this->issuer;
	}

	public function getAudience()
	{
		return $this->audience;
	}

	public function getSubject()
	{
		return $this->subject;
	}

	public function getHeader()
	{
		return json_encode(['alg' => $this->getAlgorithm(), 'typ' => $this->getType()]);
	}

	public function getPayload()
	{
		if (!array_key_exists('iss', $this->payload)) {
			$this->payload = array_merge($this->payload, ['iss' => $this->getIssuer()]);
			$this->payload = array_merge($this->payload, ['exp' => $this->getExpiration()->toDateTimeString()]);
			$this->payload = array_merge($this->payload, ['sub' => $this->getSubject()]);
			$this->payload = array_merge($this->payload, ['aud' => $this->getAudience()]);
		}

		return json_encode($this->payload);
	}

	public function getSignature()
	{
		return new Signature($this->getHeader(), $this->getPayload(), $this->getSecret(), $this->getHash());
	}

	public function setSecret($secret)
	{
		$this->secret = $secret;

		return $this;
	}

	public function setExpiration($expiration)
	{
		$this->expiration = Carbon::parse($expiration);

		return $this;
	}

	public function setIssuer($issuer)
	{
		$this->issuer = $issuer;

		return $this;
	}

	public function addPayload($key, $value)
	{
		$this->payload = array_merge($this->payload, [$key => $value]);

		return $this;
	}

	public function build()
	{
		return Base64::encode($this->getHeader()) . "." . 
			Base64::encode($this->getPayload()) . "." .
			$this->getSignature()->get();
	}
}