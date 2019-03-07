<?php

namespace Netistrar\ClientAPI\Exception;

use Kinikit\Core\Exception\SerialisableException;

/**
 * Transaction Exception raised when an issue occurs usually in a call to
 * as single update function.
*/
class TransactionException extends SerialisableException {

    /**
     *
     * @var string 
     */
    protected $message;

    /**
     *
     * @var string 
     */
    protected $code;

    /**
     *
     * @var string 
     */
    protected $file;

    /**
     *
     * @var string 
     */
    protected $line;

    /**
     * Indexed string array of exception data in the case that a non-serialisable
     * exception has been shunted into this class
     *
     * @var string[string] 
     */
    protected $sourceException;

    /**
     * Nested array of errors
     *
     * @var \Netistrar\ClientAPI\Objects\Transaction\TransactionError[string] 
     */
    protected $transactionErrors;


    /**
     * Constructor
     *
     */
    public function __construct(){

        
    }

    /**
     * Get the transactionErrors
     *
     * @return \Netistrar\ClientAPI\Objects\Transaction\TransactionError[string]
     */
    public function getTransactionErrors(){
        return $this->transactionErrors;
    }


}