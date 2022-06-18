<?php

namespace Kinikit\Core\Util\HTTP;

use Kinikit\Core\Exception\HttpRequestErrorException;
use Kinikit\Interfaces\Core\Util\HTTP\SimpleHttpRemoteRequestInterface;

class CurlHttpRemoteRequest implements SimpleHttpRemoteRequestInterface
{
	public function __construct(string $url, string $method = "POST", array $parameters = [], string $payload = null, array $headers = [], string $authUsername = null, string $authPassword = null)
	{
	}

	public function dispatch(bool $ignoreErrors = true, int $timeout = null): string
	{
		// TODO: Implement dispatch() method.
	}
}

// TODO

