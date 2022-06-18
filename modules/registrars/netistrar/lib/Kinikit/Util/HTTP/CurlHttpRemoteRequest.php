<?php

namespace Kinikit\Core\Util\HTTP;

use Kinikit\Core\Exception\HttpRequestErrorException;
use Kinikit\Core\Interfaces\Util\HTTP\SimpleHttpRemoteRequestInterface;

class CurlHttpRemoteRequest implements SimpleHttpRemoteRequestInterface
{
	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var array<string, string>
	 */
	private $parameters;

	/**
	 * @var string|null
	 */
	private $payload;

	/**
	 * @var array
	 */
	private $headers;

	/**
	 * @var string|null
	 */
	private $authUsername;

	/**
	 * @var string|null
	 */
	private $authPassword;

	public function __construct(string $url, string $method = "POST", array $parameters = [], string $payload = null, array $headers = [], string $authUsername = null, string $authPassword = null)
	{
		$this->url = $url;
		$this->method = $method;
		$this->parameters = $parameters;
		$this->payload = $payload;
		$this->headers = $headers;
		$this->authUsername = $authUsername;
		$this->authPassword = $authPassword;
	}

	public function dispatch(bool $ignoreErrors = true, int $timeout = null): string
	{
		/**
		 * Initialise cURL
		 */
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout ?? 15);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);

		/**
		 * Headers
		 */
		if (!isset($this->headers["Content-Type"])) {
			$this->headers["Content-Type"] = "application/json";
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

		/**
		 * Authentication
		 */
		if ($this->authUsername && $this->authPassword) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($ch, CURLOPT_USERPWD, $this->authUsername . ':' . $this->authPassword);
		}

		/**
		 * Query string
		 */
		$queryParams = http_build_query($this->parameters);
		$paramsAsGet = $this->payload || $this->method == "GET";

		$url = $this->url;

		if ($paramsAsGet && count($this->parameters) > 0) {
			$url .= "?" . $queryParams;
		}

		curl_setopt($ch, CURLOPT_URL, $url);

		/**
		 * Body
		 */
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->payload ?: ($paramsAsGet ? "" : $queryParams));

		/**
		 * Send it
		 */
		try {
			$responseBody = curl_exec($ch);

			if ($responseBody === false) {
				throw new \Exception(
					'cURL Error: [' . curl_errno($ch) . ']: ' . curl_error($ch)
				);
			}
		} catch(\Exception $e) {
			if (!$ignoreErrors) {
				throw $e;
			}

			return "";
		}

		/**
		 * Check the response
		 */
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($responseCode >= 400) {
			throw new HttpRequestErrorException($url, $responseCode, $responseBody);
		}

		return $responseBody;
	}
}

