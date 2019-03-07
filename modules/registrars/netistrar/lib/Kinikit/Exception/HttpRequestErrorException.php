<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 11/10/2018
 * Time: 14:21
 */

namespace Kinikit\Core\Exception;


class HttpRequestErrorException extends \Exception {

    private $url;
    private $httpStatusCode;
    private $response;

    public function __construct($url, $httpStatusCode, $response) {
        $this->url = $url;
        $this->httpStatusCode = $httpStatusCode;
        $this->response = $response;

        $message = "HTTP Request to " . $url . " failed with error code" . $httpStatusCode . "\n\n" . $response;

        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode() {
        return $this->httpStatusCode;
    }

    /**
     * @return mixed
     */
    public function getResponse() {
        return $this->response;
    }


}