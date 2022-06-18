<?php

namespace Kinikit\Interfaces\Core\Util\HTTP;

interface SimpleHttpRemoteRequestInterface
{
	/**
	 * Construct a remote request to another server
	 *
	 * @param string $url
	 * @param string $method
	 * @param array<string, string> $parameters
	 * @param string|null $payload
	 * @param array $headers
	 * @param string|null $authUsername
	 * @param string|null $authPassword
	 */
	public function __construct(string $url, string $method = "POST", array $parameters = [], string $payload = null, array $headers = [], string $authUsername = null, string $authPassword = null);

	/**
	 * Dispatch the request and collect the result.
	 *
	 * Returns the response body as a string.
	 */
	public function dispatch(bool $ignoreErrors = true, int $timeout = null): string;
}

