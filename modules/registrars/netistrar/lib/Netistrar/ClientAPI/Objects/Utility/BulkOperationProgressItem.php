<?php

namespace Netistrar\ClientAPI\Objects\Utility;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Encodes progress information about a single element within a bulk operation.  These are returned as part of the <a href="bulk-operation-progress">BulkOperationProgress</a> object as subordinate objects for e.g. use in GUIs to
 * update the progress as it completes.
*/
class BulkOperationProgressItem extends SerialisableObject {

    /**
     * A descriptive string for the bulk operation element represented by this progress item.
     *
     * @var string 
     */
    protected $title;

    /**
     * The progress percentage through this item if supported by the bulk operation in question (otherwise this will always be set to 0)
     *
     * @var float 
     */
    protected $progressPercentage;

    /**
     * The status of this bulk operation element.  This will be one of the following values:
     * <b>PENDING:</b> If the element is still in the queue to be processed
     * <b>IN_PROGRESS:</b> If the element is currently being processed (in some cases the <a href="#progressPercentage">progressPercentage</a> property will give more detail about progression through this item)
     * <b>SUCCEEDED:</b> If the element has finished successfully
     * <b>FAILED:</b> If the element has finished but failed.  In this case the <a href="#failureErrors">failureErrors</a> property may be populated with an array of errors qualifying the failure.
     *
     * @var string 
     */
    protected $status;

    /**
     * An array of supporting failure error objects populated when the <a href="#status">status</a> property is set to <b>FAILED</b>.  The set of failure errors expected can be found in the documentation for
     * specific bulk operations.
     *
     * @var \Netistrar\ClientAPI\Objects\Transaction\TransactionError[string] 
     */
    protected $failureErrors;



    /**
     * Constructor
     *
     */
    public function __construct(){

        
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * Get the progressPercentage
     *
     * @return float
     */
    public function getProgressPercentage(){
        return $this->progressPercentage;
    }

    /**
     * Get the status
     *
     * @return string
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * Get the failureErrors
     *
     * @return \Netistrar\ClientAPI\Objects\Transaction\TransactionError[string]
     */
    public function getFailureErrors(){
        return $this->failureErrors;
    }


}