<?php

namespace Netistrar\ClientAPI\Objects\Utility;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Encodes information about the current progress of a bulk operation.  This includes the status of all contained <a href="bulk-operation-progress-item">BulkOperationProgressItem</a> objects which make up the bulk operation as well as general information about the progress of the whole operation both as a percentage and as a number of completed items for use in progressively updating GUIs etc.
*/
class BulkOperationProgress extends SerialisableObject {

    /**
     * The overall status of this bulk operation.  This will be set to one of the following values.
     * <b>PENDING:</b> When the bulk operation has been created but not yet started.
     * <b>RUNNING:</b> When the bulk operation has been started.
     * <b>COMPLETED:</b> When the bulk operation has finished.
     *
     * @var string 
     */
    protected $status;

    /**
     * The total number of progress items making up this bulk operation.
     *
     * @var integer 
     */
    protected $totalNumberOfItems;

    /**
     * The number of completed progress items (either successfully or not).
     *
     * @var integer 
     */
    protected $completedItems;

    /**
     * The current percentage (between 0 and 100) of progress through the bulk operation.
     *
     * @var float 
     */
    protected $percentageComplete;

    /**
     * An indicator as to whether or not there are currently any failed items in this bulk operation (useful for changing colours of progress bars etc).
     *
     * @var boolean 
     */
    protected $hasFailedItems;

    /**
     * An array of <a href="bulk-operation-progress-item">BulkOperationProgressItem</a> items each representing the progress for an individual element within a bulk operation.
     *
     * @var \Netistrar\ClientAPI\Objects\Utility\BulkOperationProgressItem[] 
     */
    protected $progressItems;



    /**
     * Constructor
     *
     */
    public function __construct(){

        
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
     * Get the totalNumberOfItems
     *
     * @return integer
     */
    public function getTotalNumberOfItems(){
        return $this->totalNumberOfItems;
    }

    /**
     * Get the completedItems
     *
     * @return integer
     */
    public function getCompletedItems(){
        return $this->completedItems;
    }

    /**
     * Get the percentageComplete
     *
     * @return float
     */
    public function getPercentageComplete(){
        return $this->percentageComplete;
    }

    /**
     * Get the hasFailedItems
     *
     * @return boolean
     */
    public function getHasFailedItems(){
        return $this->hasFailedItems;
    }

    /**
     * Get the progressItems
     *
     * @return \Netistrar\ClientAPI\Objects\Utility\BulkOperationProgressItem[]
     */
    public function getProgressItems(){
        return $this->progressItems;
    }


}