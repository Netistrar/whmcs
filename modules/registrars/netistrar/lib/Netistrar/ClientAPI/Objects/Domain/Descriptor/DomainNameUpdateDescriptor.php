<?php

namespace Netistrar\ClientAPI\Objects\Domain\Descriptor;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Descriptor for a domain name update operation.  This should be passed to the update operation on the Domains API.
*/
class DomainNameUpdateDescriptor extends SerialisableObject {

    /**
     *
     * @var string[] the array of domain names to be updated.
     */
    private $domainNames;

    /**
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainNameContact A new owner contact to apply to all supplied domains.  If this is supplied as null, no owner contact update will be performed.<br><br><b>NB: </b> Key changes to owner details for GTLDs will trigger a verification email to the owner of the domain before these changes will be applied. Once approved by the owner a 60 day transfer lock will be placed on the domain.  If a verification has been triggered, it will be noted in the Transaction Element operation data for the update.  Please see extra documentation in the <a href="domain-name-contact">DomainNameContact</a> object definition.
     */
    private $ownerContact;

    /**
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainNameContact A new admin contact to apply to all supplied domains.  If this is supplied as null, no admin contact update will be performed.
     */
    private $adminContact;

    /**
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainNameContact A new admin contact to apply to all supplied domains.  If this is supplied as null, no billing contact update will be performed.
     */
    private $billingContact;

    /**
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainNameContact A new admin contact to apply to all supplied domains.  If this is supplied as null, no technical contact update will be performed.
     */
    private $technicalContact;

    /**
     *
     * @var string[] The array of nameservers to apply to the supplied domain names.  If this is supplied as null, no nameserver update will be performed.
     */
    private $nameservers;

    /**
     *
     * @var boolean  When set to 1 or 0 this will set / unset respectively the locked status for all passed domains.  If left unset no change will be made to the locked status for the domains.<br><br><b>NB:</b>It may not always be possible to unlock a domain if a mandatory lock has been applied such as after a create / transfer operation.
     */
    private $locked;

    /**
     *
     * @var integer This should be set to one of the following values: <br><br><b>0</b> if all contact details are to be made public within the WHOIS system for all supplied domains<br><b>1</b> if the free Netistrar Privacy Proxy service will be used for all supplied domains<br><b>2</b> if partial details are to be made public within the WHOIS system with other details redacted.  (defaults to 1).
     */
    private $privacyProxy;

    /**
     *
     * @var boolean A boolean indicator as to whether the an attempt will be made to auto renew this domain using account payment methods (defaults to 0)
     */
    private $autoRenew;

    /**
     *
     * @var string[] An array of tags to add to the supplied domain names for organisational purposes.
     */
    private $addTags;

    /**
     *
     * @var string[] An array of tags to remove from the supplied domain names for organisational purposes.
     */
    private $removeTags;



    /**
     * Constructor
     *
     * @param  $domainNames
     * @param  $ownerContact
     * @param  $adminContact
     * @param  $billingContact
     * @param  $technicalContact
     * @param  $nameservers
     * @param  $locked
     * @param  $privacyProxy
     * @param  $autoRenew
     * @param  $addTags
     * @param  $removeTags
     */
    public function __construct($domainNames = null, $ownerContact = null, $adminContact = null, $billingContact = null, $technicalContact = null, $nameservers = null, $locked = null, $privacyProxy = null, $autoRenew = null, $addTags = null, $removeTags = null){

        $this->domainNames = $domainNames;
        $this->ownerContact = $ownerContact;
        $this->adminContact = $adminContact;
        $this->billingContact = $billingContact;
        $this->technicalContact = $technicalContact;
        $this->nameservers = $nameservers;
        $this->locked = $locked;
        $this->privacyProxy = $privacyProxy;
        $this->autoRenew = $autoRenew;
        $this->addTags = $addTags;
        $this->removeTags = $removeTags;
        
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
     * @return DomainNameUpdateDescriptor
     */
    public function setDomainNames($domainNames){
        $this->domainNames = $domainNames;
        return $this;
    }

    /**
     * Get the ownerContact
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameContact
     */
    public function getOwnerContact(){
        return $this->ownerContact;
    }

    /**
     * Set the ownerContact
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\DomainNameContact $ownerContact
     * @return DomainNameUpdateDescriptor
     */
    public function setOwnerContact($ownerContact){
        $this->ownerContact = $ownerContact;
        return $this;
    }

    /**
     * Get the adminContact
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameContact
     */
    public function getAdminContact(){
        return $this->adminContact;
    }

    /**
     * Set the adminContact
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\DomainNameContact $adminContact
     * @return DomainNameUpdateDescriptor
     */
    public function setAdminContact($adminContact){
        $this->adminContact = $adminContact;
        return $this;
    }

    /**
     * Get the billingContact
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameContact
     */
    public function getBillingContact(){
        return $this->billingContact;
    }

    /**
     * Set the billingContact
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\DomainNameContact $billingContact
     * @return DomainNameUpdateDescriptor
     */
    public function setBillingContact($billingContact){
        $this->billingContact = $billingContact;
        return $this;
    }

    /**
     * Get the technicalContact
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameContact
     */
    public function getTechnicalContact(){
        return $this->technicalContact;
    }

    /**
     * Set the technicalContact
     *
     * @param \Netistrar\ClientAPI\Objects\Domain\DomainNameContact $technicalContact
     * @return DomainNameUpdateDescriptor
     */
    public function setTechnicalContact($technicalContact){
        $this->technicalContact = $technicalContact;
        return $this;
    }

    /**
     * Get the nameservers
     *
     * @return string[]
     */
    public function getNameservers(){
        return $this->nameservers;
    }

    /**
     * Set the nameservers
     *
     * @param string[] $nameservers
     * @return DomainNameUpdateDescriptor
     */
    public function setNameservers($nameservers){
        $this->nameservers = $nameservers;
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
     * @return DomainNameUpdateDescriptor
     */
    public function setLocked($locked){
        $this->locked = $locked;
        return $this;
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
     * Set the privacyProxy
     *
     * @param integer $privacyProxy
     * @return DomainNameUpdateDescriptor
     */
    public function setPrivacyProxy($privacyProxy){
        $this->privacyProxy = $privacyProxy;
        return $this;
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
     * Set the autoRenew
     *
     * @param boolean $autoRenew
     * @return DomainNameUpdateDescriptor
     */
    public function setAutoRenew($autoRenew){
        $this->autoRenew = $autoRenew;
        return $this;
    }

    /**
     * Get the addTags
     *
     * @return string[]
     */
    public function getAddTags(){
        return $this->addTags;
    }

    /**
     * Set the addTags
     *
     * @param string[] $addTags
     * @return DomainNameUpdateDescriptor
     */
    public function setAddTags($addTags){
        $this->addTags = $addTags;
        return $this;
    }

    /**
     * Get the removeTags
     *
     * @return string[]
     */
    public function getRemoveTags(){
        return $this->removeTags;
    }

    /**
     * Set the removeTags
     *
     * @param string[] $removeTags
     * @return DomainNameUpdateDescriptor
     */
    public function setRemoveTags($removeTags){
        $this->removeTags = $removeTags;
        return $this;
    }


}