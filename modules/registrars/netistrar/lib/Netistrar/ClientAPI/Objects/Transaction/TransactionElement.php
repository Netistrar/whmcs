<?php

namespace Netistrar\ClientAPI\Objects\Transaction;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Transaction element object.  Represents a single domain name result within a transaction returned from the operations found in the Domain API.
*/
class TransactionElement extends SerialisableObject {

    /**
     * The type of element e.g. Domain Name
     *
     * @var string 
     */
    protected $type;

    /**
     * The description of the element
     *
     * @var string 
     */
    protected $description;

    /**
     * The status of this element.  This will contain one of the following values:
     * <br><br>
     * <b>SUCCEEDED:</b> When this element succeeded.<br>
     * <b>FAILED:</b> When this element failed.
     *
     * @var string 
     */
    protected $elementStatus;

    /**
     * An associative array of operation data relevant to the operation type returned in the case that the <a href="#elementStatus">elementStatus</a> member is set to <b>SUCCEEDED</b>.
     * For <b>DOMAIN_CREATE</b> and <b>DOMAIN_RENEW</b> operations this will contain an array with the following keys
     * <b>expiryDate:</b> The new expiry date for this domain name following the operation in dd/mm/YYYY format.
     * <b>registrationYears:</b> The number of years added to this domain following the operation.
     *
     * @var mixed[string] 
     */
    protected $operationData;

    /**
     * An error member which is populated in the case that the elementStatus member is set to <b>FAILED</b>. These will be indexed by the error code.
     *
     * @var \Netistrar\ClientAPI\Objects\Transaction\TransactionError[string] 
     */
    protected $elementErrors;

    /**
     * The subtotal for the Netistrar order line for this domain name if applicable for this transaction.  This will be set when the <i>netistrarOrderId</i> element is defined for the wrapping <a href="domain-name-transaction">DomainNameTransaction</a> object
     * and will be the net total (pre taxes) for this domain operation in the currency defined in the transaction.
     * <br><br>
     * This property defaults to <b>N/A</b> if no order id is available for this transaction.
     *
     * @var float 
     */
    protected $orderLineSubtotal;

    /**
     * The taxes for the Netistrar order line for this domain name if applicable for this transaction.  This will be set when the <i>netistrarOrderId</i> element is defined for the wrapping <a href="domain-name-transaction">DomainNameTransaction</a> object
     * and will be the tax amount (VAT) for this domain operation in the currency defined in the transaction.
     * <br><br>
     * This property defaults to <b>N/A</b> if no order id is available for this transaction.
     *
     * @var float 
     */
    protected $orderLineTaxes;

    /**
     * The total for the Netistrar order line for this domain name if applicable for this transaction.  This will be set when the <i>netistrarOrderId</i> element is defined for the wrapping <a href="domain-name-transaction">DomainNameTransaction</a> object
     * and will be the total including taxes for this domain operation in the currency defined in the transaction.
     * <br><br>
     * This property defaults to <b>N/A</b> if no order id is available for this transaction.
     *
     * @var float 
     */
    protected $orderLineTotal;



    /**
     * Constructor
     *
     */
    public function __construct(){

        
    }

    /**
     * Get the type
     *
     * @return string
     */
    public function getType(){
        return $this->type;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription(){
        return $this->description;
    }

    /**
     * Get the elementStatus
     *
     * @return string
     */
    public function getElementStatus(){
        return $this->elementStatus;
    }

    /**
     * Get the operationData
     *
     * @return mixed[string]
     */
    public function getOperationData(){
        return $this->operationData;
    }

    /**
     * Get the elementErrors
     *
     * @return \Netistrar\ClientAPI\Objects\Transaction\TransactionError[string]
     */
    public function getElementErrors(){
        return $this->elementErrors;
    }

    /**
     * Get the orderLineSubtotal
     *
     * @return float
     */
    public function getOrderLineSubtotal(){
        return $this->orderLineSubtotal;
    }

    /**
     * Get the orderLineTaxes
     *
     * @return float
     */
    public function getOrderLineTaxes(){
        return $this->orderLineTaxes;
    }

    /**
     * Get the orderLineTotal
     *
     * @return float
     */
    public function getOrderLineTotal(){
        return $this->orderLineTotal;
    }


}