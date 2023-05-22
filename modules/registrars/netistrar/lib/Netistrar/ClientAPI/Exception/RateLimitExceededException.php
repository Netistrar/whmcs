<?php

namespace Netistrar\ClientAPI\Exception;

use Kinikit\Core\Exception\SerialisableException;

/**

*/
class RateLimitExceededException extends SerialisableException {

    /**
     * Indexed string array of exception data in the case that a non-serialisable
     * exception has been shunted into this class
     *
     * @var string[string] 
     */
    protected $sourceException;


    /**
     * Constructor
     *
     */
    public function __construct(){

        
    }


}