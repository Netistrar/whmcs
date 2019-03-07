<?php

namespace Netistrar\ClientAPI\Objects\Domain;

use Kinikit\Core\Object\SerialisableObject;
/**
 * This encodes details about a domain transfer.  When a domain name is in the <i>TRANSFER_IN_AWAITING_RESPONSE</i> status this will be fully populated with details about the
 * transfer window as obtained from the Registry.
 * <br><br>
 * Otherwise if the domain name is in a pending confirmation status this will only have the <a href="#domainName">domainName</a> and <a href="#status">status</a> members set.
*/
class DomainNameTransferStatus extends SerialisableObject {

    /**
     * The domain name for which the transfer status applies.
     *
     * @var string 
     */
    protected $domainName;

    /**
     * The status for this domain.  This will be either <i>TRANSFER_IN_PENDING_CONFIRMATION</i>, <i>TRANSFER_IN_AWAITING_RESPONSE</i>, <i>TRANSFER_IN_REJECTED</i> for transfer in operations
     * or <i>TRANSFER_OUT_PENDING_CONFIRMATION</i> for transfer out operations.
     *
     * @var string 
     */
    protected $status;

    /**
     * The transfer status as returned from the Registry.  This is usually "Pending"
     * <br><br>
     * This is only set when the <a href="#status">status</a> is set to <i>TRANSFER_IN_AWAITING_RESPONSE</i> otherwise <b>N/A</b> will be returned.
     *
     * @var string 
     */
    protected $transferStatus;

    /**
     * The date and time that the transfer was started in <b>dd/mm/YYYY HH:mm:ss</b> format
     * <br><br>
     * This is only set when the <a href="#status">status</a> is set to <i>TRANSFER_IN_AWAITING_RESPONSE</i> otherwise <b>N/A</b> will be returned.
     *
     * @var string 
     */
    protected $transferStartedDate;

    /**
     * The date and time that the transfer expires - usually 5 days after the transfer started date in <b>dd/mm/YYYY HH:mm:ss</b> format
     * <br><br>
     * This is only set when the <a href="#status">status</a> is set to <i>TRANSFER_IN_AWAITING_RESPONSE</i> otherwise <b>N/A</b> will be returned.
     *
     * @var string 
     */
    protected $transferExpiryDate;

    /**
     * The date and time for the new expiry date for the domain after the transfer completes (usually 1yr more than current expiry) in <b>dd/mm/YYYY HH:mm:ss</b> format
     * <br><br>
     * This is only set when the <a href="#status">status</a> is set to <i>TRANSFER_IN_AWAITING_RESPONSE</i> otherwise <b>N/A</b> will be returned.
     *
     * @var string 
     */
    protected $domainExpiryDate;



    /**
     * Constructor
     *
     */
    public function __construct(){

        
    }

    /**
     * Get the domainName
     *
     * @return string
     */
    public function getDomainName(){
        return $this->domainName;
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
     * Get the transferStatus
     *
     * @return string
     */
    public function getTransferStatus(){
        return $this->transferStatus;
    }

    /**
     * Get the transferStartedDate
     *
     * @return string
     */
    public function getTransferStartedDate(){
        return $this->transferStartedDate;
    }

    /**
     * Get the transferExpiryDate
     *
     * @return string
     */
    public function getTransferExpiryDate(){
        return $this->transferExpiryDate;
    }

    /**
     * Get the domainExpiryDate
     *
     * @return string
     */
    public function getDomainExpiryDate(){
        return $this->domainExpiryDate;
    }


}