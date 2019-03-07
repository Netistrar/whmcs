<?php
/**
 * Created by PhpStorm.
 * User: nathanalan
 * Date: 11/09/2018
 * Time: 09:38
 */

namespace Kinikit\Core\Exception;


use Kinikit\Core\Util\Serialisation\Serialisable;
use Throwable;

class SerialisableException extends \Exception {

    use Serialisable;

    /**
     * Indexed string array of exception data in the case that a non-serialisable
     * exception has been shunted into this class
     *
     * @var string[string]
     */
    protected $sourceException;


    /**
     * SerialisableException constructor.
     * @param string $message
     * @param integer $code
     * @param \Exception $sourceException
     */
    public function __construct($message = null, $code = null, $sourceException = null) {
        if (!$message && $sourceException) {
            $message = "Non-Serialisable Exception Raised";
        }
        if ($sourceException) {
            $this->sourceException = array();
            $this->sourceException["message"] = $sourceException->getMessage();
            $this->sourceException["code"] = $sourceException->getCode();
            $this->sourceException["file"] = $sourceException->getFile();
            $this->sourceException["line"] = $sourceException->getLine();
            $this->sourceException["trace"] = $sourceException->getTraceAsString();
        }
        parent::__construct($message, $code);


    }

    /**
     * @return array
     */
    public function getSourceException() {
        return $this->sourceException;
    }

    /**
     * Get the exception class
     *
     * @return string
     */
    public function getExceptionClass() {
        return "\\".get_class($this);
    }


    /**
     * Return web service serialisable data
     *
     * @return array
     */
    public function returnWebServiceSerialisableData(){

        $serialisableMap = $this->__getSerialisablePropertyMap();

        unset($serialisableMap["trace"]);
        unset($serialisableMap["file"]);
        unset($serialisableMap["line"]);
        unset($serialisableMap["traceAsString"]);
        unset($serialisableMap["previous"]);
        $serialisableMap["code"] = $this->getSourceException() ? $this->getSourceException()["code"] : $this->getCode();
        $serialisableMap["message"] = $this->getSourceException() ? $this->getSourceException()["message"] : $this->getMessage();
        unset($serialisableMap["sourceException"]);

        return $serialisableMap;

    }

}