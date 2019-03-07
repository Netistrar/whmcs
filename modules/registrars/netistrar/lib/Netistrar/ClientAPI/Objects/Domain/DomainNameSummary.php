<?php

namespace Netistrar\ClientAPI\Objects\Domain;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Domain name summary object
*/
class DomainNameSummary extends SerialisableObject {

    /**
     * The full domain name as a string
     *
     * @var string 
     */
    protected $domainName;

    /**
     * The date of registration for this domain name if known.  NB:  This may not always be populated when domains have been transferred from elsewhere.
     *
     * @var string 
     */
    protected $registeredDate;

    /**
     * The expiry date for this domain name.
     *
     * @var string 
     */
    protected $expiryDate;

    /**
     * An indicator as to whether or not this domain name is currently locked.  Locking a domain provides an extra level of security as it prevents it from being either deleted or transferred until the lock is removed.
     * <br><br>
     * When unlocked the <a href="#authCode">authCode</a> field will be populated with the authorisation code required to move this domain to another provider.
     * <br><br>
     * Some operations (e.g. new registration, transfer) result in a mandatory lock of a domain for a period of time.  In this case the <a href="#lockedUntil">lockedUntil</a> field will be populated with a date after which the domain will be available for unlocking.
     *
     * @var boolean 
     */
    protected $locked;

    /**
     * A date after which the mandatory lock period for this domain name will cease.  This is used in conjunction with the <a href="#locked">locked</a> field in the case that a mandatory lock has been applied to this domain following an operation (e.g. new registration, transfer).
     *
     * @var string 
     */
    protected $lockedUntil;

    /**
     * The authorisation code required for most domain transfers (notable exceptions are .UK domains and some other country TLDs).  This is only available when this domain is unlocked (i.e. the <a href="#locked">locked</a> member is true).
     * If the domain is locked this will be set to <b>N/A</b>.
     *
     * @var string 
     */
    protected $authCode;

    /**
     * An indicator as to whether or not an attempt will be made to auto renew this domain.  If this is set to true an attempt will be made around 30 days prior to the expiry date to renew the domain using any available payment
     * methods defined within your account in the Netistrar Control Panel.
     *
     * @var boolean 
     */
    protected $autoRenew;

    /**
     * An indicator as to whether or not the free Netistrar Privacy Proxy service is being used for this domain.  If set to true, standard Netistrar privacy contacts will be transmitted to the domain registry for display in Public WHOIS
     * in lieu of the true contact details which will still be held on the Netistrar side.
     *
     * @var integer 
     */
    protected $privacyProxy;

    /**
     * One of the following status values representing the current Netistrar Domain Name status for this domain name.
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
    protected $status;

    /**
     * An array of tags which have been added to this domain for organisational purposes.
     *
     * @var string[] 
     */
    protected $tags;

    /**
     *
     * @var string 
     */
    protected $dnsProvider;

    /**
     *
     * @var string 
     */
    protected $emailProvider;

    /**
     * The owner of this domain name
     *
     * @var string 
     */
    protected $ownerName;



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
     * Get the registeredDate
     *
     * @return string
     */
    public function getRegisteredDate(){
        return $this->registeredDate;
    }

    /**
     * Get the expiryDate
     *
     * @return string
     */
    public function getExpiryDate(){
        return $this->expiryDate;
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
     * Get the lockedUntil
     *
     * @return string
     */
    public function getLockedUntil(){
        return $this->lockedUntil;
    }

    /**
     * Get the authCode
     *
     * @return string
     */
    public function getAuthCode(){
        return $this->authCode;
    }

    /**
     * Get the autoRenew
     *
     * @return boolean
     */
    public function getAutoRenew(){
        return $this->autoRenew;
    }

    /**
     * Get the privacyProxy
     *
     * @return integer
     */
    public function getPrivacyProxy(){
        return $this->privacyProxy;
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
     * Get the tags
     *
     * @return string[]
     */
    public function getTags(){
        return $this->tags;
    }

    /**
     * Get the dnsProvider
     *
     * @return string
     */
    public function getDnsProvider(){
        return $this->dnsProvider;
    }

    /**
     * Get the emailProvider
     *
     * @return string
     */
    public function getEmailProvider(){
        return $this->emailProvider;
    }

    /**
     * Get the ownerName
     *
     * @return string
     */
    public function getOwnerName(){
        return $this->ownerName;
    }


}