<?php

namespace Kinikit\Core\Util\HTTP;

use Kinikit\Core\Exception\HttpRequestErrorException;
use Kinikit\Core\Interfaces\Util\HTTP\SimpleHttpRemoteRequestInterface;

/**
 * Simple CURL-LESS Post request object for dispatching post requests.
 */
class HttpRemoteRequest implements SimpleHttpRemoteRequestInterface
{
    private $url;
    private $parameters;
    private $payload;
    private $method;
    private $headers;
    private $authUsername;
    private $authPassword;

	/**
	 * @inheritDoc
	 */
	public function __construct(string $url, string $method = "POST", array $parameters = [], string $payload = null, array $headers = [], string $authUsername = null, string $authPassword = null)
	{
        $this->url = $url;
        $this->parameters = $parameters;
        $this->payload = $payload;
        $this->method = $method;
        $this->headers = $headers;
        $this->authUsername = $authUsername;
        $this->authPassword = $authPassword;
    }

	/**
	 * @inheritDoc
	 */
	public function dispatch(bool $ignoreErrors = true, int $timeout = null): string
	{
        if (!isset($this->headers)) {
            $this->headers = [];
        }

        if (!isset($this->headers["Content-Type"]))
            $this->headers["Content-Type"] = "application/json";


        // If we have an auth username and password, use it.
        if ($this->authUsername && $this->authPassword) {
            $this->headers["Authorization"] = "Basic " . base64_encode($this->authUsername . ":" . $this->authPassword);
        }

        $headers = [];
        foreach ($this->headers as $key => $header) {
            $headers[] = $key . ": " . $header;
        }

        if (is_array($this->parameters))
            $queryParams = http_build_query($this->parameters);

        if ($this->payload) {
            $payload = $this->payload;
        } else {
            $payload = null;
        }

        $paramsAsGet = $payload || $this->method == "GET";
        $contentData = $payload ? $payload : ($paramsAsGet ? [] : $queryParams);

        $options = [
			'http' => [
				'header' => $headers,
				'method' => $this->method,
            	'content' => $contentData,
				'ignore_errors' => $ignoreErrors
			]
		];

        $url = $this->url;
        if ($paramsAsGet && sizeof($this->parameters) > 0) {
            $url .= "?" . $queryParams;
        }

        if ($timeout) {
            $options["timeout"] = $timeout;
        }

        $context = stream_context_create($options);

        $results = file_get_contents($url, false, $context);

        $responseCode = explode(" ", $http_response_header[0])[1];

        if ($responseCode >= 400) {
            throw new HttpRequestErrorException($url, $responseCode, $results);
        }

        return $results;
    }
}

