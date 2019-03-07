<?php

namespace Netistrar\ClientAPI;

use Netistrar\ClientAPI\Controllers\domains;
use Netistrar\ClientAPI\Controllers\utility;
use Netistrar\ClientAPI\Controllers\account;
use Netistrar\ClientAPI\Controllers\test;
use Kinikit\Core\Util\HTTP\WebServiceProxy;

class APIProvider  {

    /**
    * @var string
    */
    private $apiURL;


    /**
    * @var string[]
    */
    private $globalParameters;


    /**
    * @var WebServiceProxy[]
    */
    private $instances = array();

    /**
    * Construct with the api url and the api key for access.
    *
    * @param string $apiURL
    * @param string $apiKey
    * @param string $apiSecret
    */
    public function __construct($apiURL, $apiKey, $apiSecret){
        $this->apiURL = $apiURL;

        $this->globalParameters = array();
        $this->globalParameters["apiKey"] = $apiKey;
        $this->globalParameters["apiSecret"] = $apiSecret;
    }

    /**
    * Get an instance of the  API
    *
    * @return \Netistrar\ClientAPI\Controllers\domains
    */
    public function domains(){
        if (!isset($this->instances["domains"])){
            $this->instances["domains"] = new domains($this->apiURL."/domains", $this->globalParameters);
        }
        return $this->instances["domains"];
    }

    /**
    * Get an instance of the  API
    *
    * @return \Netistrar\ClientAPI\Controllers\utility
    */
    public function utility(){
        if (!isset($this->instances["utility"])){
            $this->instances["utility"] = new utility($this->apiURL."/utility", $this->globalParameters);
        }
        return $this->instances["utility"];
    }

    /**
    * Get an instance of the  API
    *
    * @return \Netistrar\ClientAPI\Controllers\account
    */
    public function account(){
        if (!isset($this->instances["account"])){
            $this->instances["account"] = new account($this->apiURL."/account", $this->globalParameters);
        }
        return $this->instances["account"];
    }

    /**
    * Get an instance of the  API
    *
    * @return \Netistrar\ClientAPI\Controllers\test
    */
    public function test(){
        if (!isset($this->instances["test"])){
            $this->instances["test"] = new test($this->apiURL."/test", $this->globalParameters);
        }
        return $this->instances["test"];
    }




}