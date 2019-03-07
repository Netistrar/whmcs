<?php

namespace Netistrar\ClientAPI\Objects\Test\Domain;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Update descriptor for updating one or more domain names with attributes not normally updatable for testing
 * purposes.
*/
class TestDomainNameUpdateDescriptor extends SerialisableObject {

    /**
     *
     * @var string[] An array of domain names to update
     */
    private $domainNames;

    /**
     * The new status for the supplied domain names.  This will be applied directly to the name
     * bypassing any workflow associated with the particular status.
     * Possible values are:
     * <b>ACTIVE</b> - The domain name is active and in use.
     * <b>EXPIRED</b> - The domain name has expired.
     * <b>SUSPENDED</b> - The domain name has been suspended (either for unlawful behaviour or invalid ownership details).
     * <b>RGP</b> - The domain has entered the Redemption Grace Period (usually around 45 days after expiry).  During this period additional costs are incurred to redeem the domain.
     * <b>DELETED</b> - The domain has dropped completely and has been deleted (usually around 75 days after expiry once the RGP has completed).
     * <b>TRANSFER_IN_PENDING_CONFIRMATION</b> - The domain is being transferred in and is awaiting confirmation from the legal owner.
     * <b>TRANSFER_IN_OWNER_DECLINED</b> - The domain is being transferred in but the legal owner declined the transfer.
     * <b>TRANSFER_IN_AWAITING_RESPONSE</b> - The domain transfer in operation has been started and is awaiting a response from the current Registrar.  If no response is received after 5 days the transfer would simply complete.
     * <b>TRANSFER_IN_REJECTED</b> - The domain transfer in operation has been rejected by the current Registrar.
     * <b>TRANSFER_OUT_PENDING_CONFIRMATION</b> - The domain has been requested to be transferred out and is awaiting confirmation from the legal owner.
     *
     * @var string 
     */
    private $status;

    /**
     *
     * @var string The date of registration in <b>dd/mm/YYYY</b> format.
     */
    private $registeredDate;

    /**
     *
     * @var string The locked until date in <b>dd/mm/YYYY</b> format.  This allows for testing unsetting of locks.
     */
    private $lockedUntil;

    /**
     *
     * @var boolean Forcibly update the lock status for testing regardless of whether or not this is permitted for a domain.
     */
    private $locked;



    /**
     * Constructor
     *
     * @param  $domainNames
     * @param  $status
     * @param  $registeredDate
     * @param  $lockedUntil
     * @param  $locked
     */
    public function __construct($domainNames = null, $status = null, $registeredDate = null, $lockedUntil = null, $locked = null){

        $this->domainNames = $domainNames;
        $this->status = $status;
        $this->registeredDate = $registeredDate;
        $this->lockedUntil = $lockedUntil;
        $this->locked = $locked;
        
    }

    /**
     * Get the domainNames
     *
     * @return string[]
     */
    public function getDomainNames(){
        return $this->domainNames;
    }

    /**
     * Set the domainNames
     *
     * @param string[] $domainNames
     * @return TestDomainNameUpdateDescriptor
     */
    public function setDomainNames($domainNames){
        $this->domainNames = $domainNames;
        return $this;
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
     * Set the status
     *
     * @param string $status
     * @return TestDomainNameUpdateDescriptor
     */
    public function setStatus($status){
        $this->status = $status;
        return $this;
    }

    /**
     * Get the registeredDate
     *
     * @return string
     */
    public function getRegisteredDate(){
        return $this->registeredDate;
    }

    /**
     * Set the registeredDate
     *
     * @param string $registeredDate
     * @return TestDomainNameUpdateDescriptor
     */
    public function setRegisteredDate($registeredDate){
        $this->registeredDate = $registeredDate;
        return $this;
    }

    /**
     * Get the lockedUntil
     *
     * @return string
     */
    public function getLockedUntil(){
        return $this->lockedUntil;
    }

    /**
     * Set the lockedUntil
     *
     * @param string $lockedUntil
     * @return TestDomainNameUpdateDescriptor
     */
    public function setLockedUntil($lockedUntil){
        $this->lockedUntil = $lockedUntil;
        return $this;
    }

    /**
     * Get the locked
     *
     * @return boolean
     */
    public function getLocked(){
        return $this->locked;
    }

    /**
     * Set the locked
     *
     * @param boolean $locked
     * @return TestDomainNameUpdateDescriptor
     */
    public function setLocked($locked){
        $this->locked = $locked;
        return $this;
    }


}