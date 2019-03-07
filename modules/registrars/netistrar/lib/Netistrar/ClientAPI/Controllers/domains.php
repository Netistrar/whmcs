<?php

namespace Netistrar\ClientAPI\Controllers;

use Kinikit\Core\Util\HTTP\WebServiceProxy;

/**
 * The Domains API provides the core services for managing Domains within the Netistrar Registrar system.
*/
class domains extends WebServiceProxy {

    /**
     * Provides fast hinted search results for one or more TLDs or predefined TLD Categories for a supplied domain prefix string.  This allows for rapid indicative search results without the overhead of real time checking via each registry.
     * <br>It is strongly recommended that a call is made to the <b>getLiveAvailability</b> function for individual domains before confirming a sale or adding to an application cart.
     * Hinted availability is provided via the use of cached zone file data.  Pricing data is returned according to the availability detected and where premium pricing may apply for a given TLD, a hint of the premium status and pricing is returned via cached premium data where available.
     * <br>The returned object contains DomainAvailability objects structured according to the parameters passed.
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameAvailabilityDescriptor $descriptor
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainAvailabilityResults
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function hintedAvailability($descriptor){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("hinted", "POST", array(),$descriptor,"\Netistrar\ClientAPI\Objects\Domain\DomainAvailabilityResults",$expectedExceptions);
    }

    /**
     * Get live domain availability for a single domain name.  This actually checks the real time availability with the Registry and returns a single <a href="domain-availability-object">DomainAvailability</a> object with actual availability and confirmed pricing.  This method
     * should be called before committing to a sale (usually at the point of adding to a cart).
     *
     * @param string $domainName
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainAvailability
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function liveAvailability($domainName){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("available/$domainName", "GET", array(),null,"\Netistrar\ClientAPI\Objects\Domain\DomainAvailability",$expectedExceptions);
    }

    /**
     * List domains currently contained within your account as <a href="../../object/DomainNameSummaryObject">DomainNameSummaryObject</a> items.
     * This method supports paging operations using the <i>pageSize</i> and <i>page</i> parameters and allows for sorting of results using the <i>orderBy</i> and <i>orderDirection</i> parameters.
     *
     * @param string $searchTerm
     * @param int $pageSize
     * @param int $page
     * @param string $orderBy
     * @param string $orderDirection
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameListResults
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function list($searchTerm = "", $pageSize = 10, $page = 1, $orderBy = "domainName", $orderDirection = "ASC"){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("", "GET", array("searchTerm" => $searchTerm, "pageSize" => $pageSize, "page" => $page, "orderBy" => $orderBy, "orderDirection" => $orderDirection),null,"\Netistrar\ClientAPI\Objects\Domain\DomainNameListResults",$expectedExceptions);
    }

    /**
     * Return a single domain name from within your account as a full domain name object.
     *
     * @param string $domainName
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameObject
    * @throws \Netistrar\ClientAPI\Exception\TransactionException
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function get($domainName){
        $expectedExceptions = array();
        $expectedExceptions["\\Netistrar\\WebServices\\Common\\Exception\\TransactionException"] = "\Netistrar\ClientAPI\Exception\TransactionException";
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("$domainName", "GET", array(),null,"\Netistrar\ClientAPI\Objects\Domain\DomainNameObject",$expectedExceptions);
    }

    /**
     * Get multiple domain names from within your account as full domain name objects.
     *
     * @param string[] $domainNames
     * @param boolean $ignoreMissingItems
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameObject[string]
    * @throws \Netistrar\ClientAPI\Exception\TransactionException
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function getMultiple($domainNames, $ignoreMissingItems = 1){
        $expectedExceptions = array();
        $expectedExceptions["\\Netistrar\\WebServices\\Common\\Exception\\TransactionException"] = "\Netistrar\ClientAPI\Exception\TransactionException";
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("multiple", "POST", array("ignoreMissingItems" => $ignoreMissingItems),$domainNames,"\Netistrar\ClientAPI\Objects\Domain\DomainNameObject[string]",$expectedExceptions);
    }

    /**
     * Validate one or more domain names for creation using a single set of contact and nameserver details.  This is designed to be called as a lightweight check before calling the create API.
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameCreateDescriptor $createDescriptor
     * @return \Netistrar\ClientAPI\Objects\Transaction\TransactionError[string][string]
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function validate($createDescriptor){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("validate", "POST", array(),$createDescriptor,"\Netistrar\ClientAPI\Objects\Transaction\TransactionError[string][string]",$expectedExceptions);
    }

    /**
     * Create one or more domain names using a single set of contact and nameserver details.
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameCreateDescriptor $createDescriptor
     * @param string $bulkOperationProgressKey
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function create($createDescriptor, $bulkOperationProgressKey = ""){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("", "POST", array("bulkOperationProgressKey" => $bulkOperationProgressKey),$createDescriptor,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * Update one or more domain names within your account.  This allows for contact, nameserver and domain name attributes to be updated in bulk.
     * <b>NB: </b> Please note the special additional verification workflow documented below when changing owner contacts for GTLDs.
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameUpdateDescriptor $updateDescriptor
     * @param string $bulkOperationProgressKey
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function update($updateDescriptor, $bulkOperationProgressKey = ""){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("", "PATCH", array("bulkOperationProgressKey" => $bulkOperationProgressKey),$updateDescriptor,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * Renew a single domain for the number of years supplied.
     *
     * @param string $domainName
     * @param integer $numberOfYears
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function renew($domainName, $numberOfYears){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("renew/$domainName/$numberOfYears", "GET", array(),null,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * Renew multiple domains
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameRenewDescriptor $renewDescriptor
     * @param string $bulkOperationProgressKey
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function renewMultiple($renewDescriptor, $bulkOperationProgressKey = ""){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("renew", "POST", array("bulkOperationProgressKey" => $bulkOperationProgressKey),$renewDescriptor,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * Check the transfer status for a domain name.  This will return a <b>DomainNameTransferStatus</b> object detailing the timings for the transfer window in progress where the
     * domain is currently in a transfer cycle or N/A if this is not the case
     *
     * @param string $domainName
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameTransferStatus
    * @throws \Netistrar\ClientAPI\Exception\TransactionException
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function transferCheck($domainName){
        $expectedExceptions = array();
        $expectedExceptions["\\Netistrar\\WebServices\\Common\\Exception\\TransactionException"] = "\Netistrar\ClientAPI\Exception\TransactionException";
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("transfer/$domainName", "GET", array(),null,"\Netistrar\ClientAPI\Objects\Domain\DomainNameTransferStatus",$expectedExceptions);
    }

    /**
     * Validate multiple domains for transfer in.  This accepts a transfer descriptor which encodes one or more domain names for transfer in along with proposed contact details.
     * <b>NB: </b>Since the introduction of the 2018 Temporary Specification for GTLD registration data, post transfer contact details need to be supplied upfront when creating / validating incoming transfers
     * as these are no longer readable via WHOIS due to privacy redaction.
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameTransferDescriptor $transferDescriptor
     * @return \Netistrar\ClientAPI\Objects\Transaction\TransactionError[string][string]
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function transferValidate($transferDescriptor){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("transfer/validate", "POST", array(),$transferDescriptor,"\Netistrar\ClientAPI\Objects\Transaction\TransactionError[string][string]",$expectedExceptions);
    }

    /**
     * Create multiple domains for transfer in. This accepts a transfer descriptor which encodes one or more domain names for transfer in along with proposed contact details.  This call should usually be preceded by a call to <b>validateIncomingTransferDomains</b> to confirm auth codes etc.
     * <b>NB: </b>Since the introduction of the 2018 Temporary Specification for GTLD registration data, post transfer contact details need to be supplied upfront when creating / validating incoming transfers
     * as these are no longer readable via WHOIS due to privacy redaction.
     * <br><br>
     * If successful, this method starts the transfer process for the supplied domains by taking payment for the transfer (for Pull Transfer operations) and starting the transfer operation with the Registry.
     * It returns a <b>DomainNameTransaction</b> object detailing the result of the operation.
     * <b>For Pull Transfers:</b> Once a transfer is created it will be added to your account with a status of <i>TRANSFER_IN_AWAITING_RESPONSE</i> until it is either cancelled, accepted,
     * rejected or automatically accepted after 5 days.
     * <b>For Push Transfers</b>: the domain name will be imported and activated within your account.
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\Descriptor\DomainNameTransferDescriptor $transferDescriptor
     * @param string $bulkOperationProgressKey
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function transferCreate($transferDescriptor, $bulkOperationProgressKey = ""){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("transfer", "POST", array("bulkOperationProgressKey" => $bulkOperationProgressKey),$transferDescriptor,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * Cancel incoming transfer operations for one or more domain names.  Domain transfers can be cancelled while domains have <i>TRANSFER_IN_PENDING_CONFIRMATION</i> or <i>TRANSFER_IN_AWAITING_RESPONSE</i>
     * status (applicable for Pull Transfers only).
     * This operation if successful will cancel the transfer operation and remove the domain name from your account.
     * A <b>DomainNameTransaction</b> object is returned detailing the success or failure for each attempted domain name.
     *
     * @param string[] $domainNames
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function transferCancel($domainNames){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("transfer", "DELETE", array(),$domainNames,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * Cancel any pending owner contact changes for the supplied domain names.  Pending changes arise when a call to the <b>updateDomainNames</b> method has resulted in a change to key fields for the owner contact of a GTLD.
     * In this case the owner contact will be returned with a status of <b>PENDING_CHANGES</b>.  This method will remove the pending data awaiting verification and restore the Owner contact back a status of <b>LIVE</b> with the previous details intact.  This returns a DomainNameTransaction object with transaction elements which will be successful if the owner contact is pending changes or will contain an operation error if not successful.
     *
     * @param string[] $domainNames
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function ownerChangeCancel($domainNames){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("cancelownerchanges", "DELETE", array(),$domainNames,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * List all previously set glue records for a domain name.   Glue records (sometimes called child nameservers) define explicit mappings of subdomains to IP addresses such that the subdomain may be used as a delegated
     * nameserver for other domains or indeed the domain name itself.
     * PLEASE NOTE:  This method will only list glue records which have been explicitly set using the <b>setGlueRecords</b> method or via the Netistrar Control Panel.  There is no guarantee that this list is the complete list of records defined at the registry if this name has been transferred into Netistrar with existing additional glue records intact.
     * This method returns an array of <b>DomainNameGlueRecord</b> objects currently defined for the domain name
     *
     * @param string $domainName
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameGlueRecord[]
    * @throws \Netistrar\ClientAPI\Exception\TransactionException
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function glueRecordsList($domainName){
        $expectedExceptions = array();
        $expectedExceptions["\\Netistrar\\WebServices\\Common\\Exception\\TransactionException"] = "\Netistrar\ClientAPI\Exception\TransactionException";
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("gluerecords/$domainName", "GET", array(),null,"\Netistrar\ClientAPI\Objects\Domain\DomainNameGlueRecord[]",$expectedExceptions);
    }

    /**
     * Set one or more glue records for a domain name.  Glue records (sometimes called child nameservers) define explicit mappings of subdomains to IP addresses such that the subdomain may be used as a delegated
     * nameserver for other domains or indeed the domain name itself.
     * This method accepts a domain name and an array of <b>DomainNameGlueRecord</b> objects which comprise a subdomain prefix and either an ipv4 or ipv6 address or both.
     * It returns a <b>DomainNameTransaction</b> object which encodes the result of the set operation with a transaction element for each glue record passed.  If the glue records are
     * in an invalid format or have missing data, a validation error will be returned as part of the element.
     *
     * @param string $domainName
     * @param \Netistrar\ClientAPI\Objects\Domain\DomainNameGlueRecord[] $glueRecords
     * @param string $bulkOperationProgressKey
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function glueRecordsSet($domainName, $glueRecords, $bulkOperationProgressKey = ""){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("gluerecords/$domainName", "PATCH", array("bulkOperationProgressKey" => $bulkOperationProgressKey),$glueRecords,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * Remove one or more glue records for a domain name.  Glue records (sometimes called child nameservers) define explicit mappings of subdomains to IP addresses such that the subdomain may be used as a delegated nameserver for other domains or indeed the domain name itself.
     * This method accepts a domain name and an array of String objects which represent the subdomains to remove as glue records.
     * It returns a <b>DomainNameTransaction</b> object which encodes the result of the remove operation with a transaction element for each glue record passed.  Operation errors will be raised
     * if the glue record does not exist or cannot be removed
     *
     * @param string $domainName
     * @param string[] $glueRecordSubdomains
     * @param string $bulkOperationProgressKey
     * @return \Netistrar\ClientAPI\Objects\Transaction\Transaction
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function glueRecordsRemove($domainName, $glueRecordSubdomains, $bulkOperationProgressKey = ""){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("gluerecords/$domainName", "DELETE", array("bulkOperationProgressKey" => $bulkOperationProgressKey),$glueRecordSubdomains,"\Netistrar\ClientAPI\Objects\Transaction\Transaction",$expectedExceptions);
    }

    /**
     * Get all available TLDs enabled within the system.  This can optionally be limited by one of the categories obtained from the method <b>getAllTLDCategories</b> below.
     *
     * @param string $categoryName
     * @return string[]
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function tldList($categoryName = ""){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("tld", "GET", array("categoryName" => $categoryName),null,"string[]",$expectedExceptions);
    }

    /**
     * Get all defined TLD categories as a string array.
     *
     * @return string[]
    * @throws \Netistrar\ClientAPI\Exception\RateLimitExceededException
     */
    public function tldCategoryList(){
        $expectedExceptions = array();
        $expectedExceptions["\\Kinikit\\MVC\\Exception\\RateLimitExceededException"] = "\Netistrar\ClientAPI\Exception\RateLimitExceededException";
        return parent::callMethod("tldcategory", "GET", array(),null,"string[]",$expectedExceptions);
    }


}

