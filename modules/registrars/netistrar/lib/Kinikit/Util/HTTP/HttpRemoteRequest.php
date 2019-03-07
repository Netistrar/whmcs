<?php

namespace Kinikit\Core\Util\HTTP;

use Kinikit\Core\Exception\HttpRequestErrorException;

/**
 * Simple CURL-LESS Post request object for dispatching post requests,
 *
 * Class PostRequest
 */
class HttpRemoteRequest {

    private $url;
    private $parameters;
    private $payload;
    private $method;
    private $headers = array();
    private $authUsername;
    private $authPassword;


    /**
     * Construct a remote request to another server
     *
     * @param string $url
     * @param string $method
     * @param string[string] $parameters
     * @param string $payload
     * @param string[string] $headers
     * @param string $authUsername
     * @param string $authPassword
     */
    public function __construct($url, $method = "POST", $parameters = array(), $payload = null, $headers = array(), $authUsername = null, $authPassword = null) {
        $this->url = $url;
        $this->parameters = $parameters;
        $this->payload = $payload;
        $this->method = $method;
        $this->headers = $headers;
        $this->authUsername = $authUsername;
        $this->authPassword = $authPassword;
    }


    /**
     * Dispatch the request and collect the result.
     *
     * @return string
     */
    public function dispatch($ignoreErrors = true, $timeout = null) {

        if (!isset($this->headers)) {
            $this->headers = array();
        }

        if (!isset($this->headers["Content-Type"]))
            $this->headers["Content-Type"] = "application/json";


        // If we have an auth username and password, use it.
        if ($this->authUsername && $this->authPassword) {
            $this->headers["Authorization"] = "Basic " . base64_encode($this->authUsername . ":" . $this->authPassword);
        }

        $headers = array();
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
        $contentData = $payload ? $payload : ($paramsAsGet ? array() : $queryParams);

        $options = array('http' => array('header' => $headers, 'method' => $this->method,
            'content' => $contentData, 'ignore_errors' => $ignoreErrors));

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