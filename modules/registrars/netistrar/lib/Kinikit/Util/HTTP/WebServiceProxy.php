<?php
/**
 * Created by PhpStorm.
 * User: markrobertshaw
 * Date: 10/10/2018
 * Time: 10:38
 */

namespace Kinikit\Core\Util\HTTP;


use Kinikit\Core\Exception\HttpRequestErrorException;
use Kinikit\Core\Exception\SerialisableException;
use Kinikit\Core\Util\ArrayUtils;
use Kinikit\Core\Util\Logging\Logger;
use Kinikit\Core\Util\SerialisableArrayUtils;
use Kinikit\Core\Util\Serialisation\JSON\JSONToObjectConverter;
use Kinikit\Core\Util\Serialisation\JSON\ObjectToJSONConverter;
use Kinikit\Core\Util\Serialisation\XML\ObjectToXMLConverter;
use Kinikit\Core\Util\Serialisation\XML\XMLToObjectConverter;

class WebServiceProxy {

    private $webServiceURL;
    private $globalParameters;
    private $dataFormat = "json";

    const DATA_FORMAT_JSON = "json";
    const DATA_FORMAT_XML = "xml";


    /**
     * Construct the proxy object with a webservice endpoint URL and optionally any global parameters
     * which will be appended to every request.
     *
     * WebServiceProxy constructor.
     * @param string $webServiceURL
     * @param array $globalParameters
     */
    public function __construct($webServiceURL, $globalParameters = array(), $dataFormat = self::DATA_FORMAT_JSON) {

        if (!$webServiceURL) {
            throw new \Exception("No Web Service URL passed to the Web Service Proxy");
        }

        $this->webServiceURL = $webServiceURL;
        $this->globalParameters = $globalParameters;
        $this->dataFormat = $dataFormat;
    }


    /**
     * Implement the call method to call a proxy service
     *
     * @param string $name
     * @param $httpMethod
     * @param array $params
     * @param mixed $payload
     * @param string $returnClass
     * @param array $expectedExceptions an array of expected exceptions.  This can simply be a values array of class names or an associative array defining mappings to client classes.
     */
    public function callMethod($name, $httpMethod = "POST", $params = array(), $payload = null, $returnClass = null, $expectedExceptions = null) {

        $objectToFormatConverter = $this->dataFormat == self::DATA_FORMAT_JSON ? new ObjectToJSONConverter() : new ObjectToXMLConverter();
        $formatToObjectConverter = $this->dataFormat == self::DATA_FORMAT_JSON ? new JSONToObjectConverter() : new XMLToObjectConverter();


        if (!is_array($expectedExceptions)) {
            $expectedExceptions = array();
        }

        if (!ArrayUtils::isAssociative($expectedExceptions)) {
            $expectedExceptions = array_combine($expectedExceptions, $expectedExceptions);
        }

        $parameters = $this->globalParameters;
        if (is_array($parameters) && is_array($params)) {
            $parameters = array_merge($parameters, $params);
        }

        foreach ($parameters as $key => $value) {
            $parameters[$key] = $value;
        }

        if ($payload) {
            $payload = $objectToFormatConverter->convert($payload);
        }

        $request = new HttpRemoteRequest($this->webServiceURL . "/" . $name, $httpMethod, $parameters, $payload);

        try {
            $result = $request->dispatch();

            if ($this->dataFormat == self::DATA_FORMAT_JSON) {
                $result = $formatToObjectConverter->convert($result);
            }


            if ($returnClass) {
                $result = SerialisableArrayUtils::convertArrayToSerialisableObjects($result, $returnClass);
            }

            return $result;

        } catch (HttpRequestErrorException $e) {

            $response = $e->getResponse();
            
            if ($this->dataFormat == self::DATA_FORMAT_JSON) {
                $response = $formatToObjectConverter->convert($response);


                if ($response["exceptionClass"] && isset($expectedExceptions[$response["exceptionClass"]])) {
                    $response = SerialisableArrayUtils::convertArrayToSerialisableObjects($response, $expectedExceptions[$response["exceptionClass"]]);
                }

            }

            if ($response instanceof SerialisableException) {
                throw $response;
            } else {
                throw $e;
            }

        }


    }

}