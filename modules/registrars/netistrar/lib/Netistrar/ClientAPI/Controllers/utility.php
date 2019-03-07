<?php

namespace Netistrar\ClientAPI\Controllers;

use Kinikit\Core\Util\HTTP\WebServiceProxy;

/**
 * The Utility API provides utility services in support of the other APIs
*/
class utility extends WebServiceProxy {

    /**
     * Ping function for checking API service availability.  Returns OK if successful
     *
     * @return string
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function ping(){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("ping", "GET", array(),null,"string",$expectedExceptions);
    }

    /**
     * Create a bulk operation progress key.  This can then be passed to a bulk operation method in e.g. the Domain API to allow progress tracking of the operation
     * using the getBulkOperationProgress method.
     *
     * @return string
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function createBulkOperation(){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("bulkoperation", "POST", array(),null,"string",$expectedExceptions);
    }

    /**
     * Get the progress for a bulk operation as a BulkOperationProgress object using a progress key generated using the createBulkOperation method.  This will typically be attached to an operation in
     * e.g. the Domain API after which repeated calls can be made asynchronously to this method to check progress.
     *
     * @param string $bulkOperationProgressKey
     * @return \Netistrar\ClientAPI\Objects\Utility\BulkOperationProgress
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function getBulkOperationProgress($bulkOperationProgressKey){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("bulkoperation/$bulkOperationProgressKey", "GET", array(),null,"\Netistrar\ClientAPI\Objects\Utility\BulkOperationProgress",$expectedExceptions);
    }


}

