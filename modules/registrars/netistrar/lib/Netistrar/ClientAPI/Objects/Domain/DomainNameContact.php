<?php

namespace Netistrar\ClientAPI\Objects\Domain;

use Kinikit\Core\Object\SerialisableObject;
/**
 * An updatable contact object used within the <a href="netistrar-domain-lifecycle-api">Netistrar Domain Lifecycle API</a> methods and as a related object to a <a href="domain-name-object">DomainNameObject</a>.
 * Changes to key data for the owner contact for GTLD domain names require verification via email to the current owner for the domain name before these changes can be applied.  If the change is accepted the
 * domain name will be locked for 60 days for transfer.
*/
class DomainNameContact extends SerialisableObject {

    /**
     * The full name of this contact
     *
     * @var string 
     */
    private $name;

    /**
     * The email address for this contact.
     *
     * @var string 
     */
    private $emailAddress;

    /**
     * The organisation if applicable for this contact.
     *
     * @var string 
     */
    private $organisation;

    /**
     * The first line of the street address for this contact
     *
     * @var string 
     */
    private $street1;

    /**
     * The second line of the street address for this contact
     *
     * @var string 
     */
    private $street2;

    /**
     * The city of the street address for this contact
     *
     * @var string 
     */
    private $city;

    /**
     * The county of the street address for this contact
     *
     * @var string 
     */
    private $county;

    /**
     * The postcode for this contact
     *
     * @var string 
     */
    private $postcode;

    /**
     * The 2 digit country code for this contact (e.g. GB).  If not supplied, this will default to <b>GB</b>
     *
     * @var string 
     */
    private $country;

    /**
     * The telephone international dialling code with leading + if a telephone number is being supplied (e.g. +44, +1 etc)
     *
     * @var string 
     */
    private $telephoneDiallingCode;

    /**
     * The local telephone number bit after the dialling code with no spaces (e.g. 1235987878)
     *
     * @var string 
     */
    private $telephone;

    /**
     * An optional telephone extension (e.g. 201)
     *
     * @var string 
     */
    private $telephoneExt;

    /**
     * The fax international dialling code with leading + if a fax number is being supplied (e.g. +44, +1 etc)
     *
     * @var string 
     */
    private $faxDiallingCode;

    /**
     * The local number bit after the dialling code with no spaces (e.g. 1235987878)
     *
     * @var string 
     */
    private $fax;

    /**
     * An optional fax extension (e.g. 201)
     *
     * @var string 
     */
    private $faxExt;

    /**
     * An associative array of data where specific TLDs support / require additional properties
     * <b>Nominet UK domains:</b>
     * <br>
     * The following additional fields are supported for Nominet .UK domains
     * <b>nominetRegistrantType</b> String (required): One of the following code values
     * LTD: <i>UK Limited Company: </i><br>
     * PLC: <i>UK Public Limited Company: </i><br>
     * PTNR: <i>UK Partnership: </i><br>
     * STRA: <i>UK Sole Trader: </i><br>
     * LLP: <i>UK Limited Liability Partnership: </i><br>
     * IP: <i>UK Industrial/Provident Registered Company: </i><br>
     * IND: <i>UK Individual (representing self): </i><br>
     * SCH: <i>UK School: </i><br>
     * RCHAR: <i>UK Registered Charity: </i><br>
     * GOV: <i>UK Government Body: </i><br>
     * CRC: <i>UK Corporation by Royal Charter: </i><br>
     * STAT: <i>UK Statutory Body: </i><br>
     * OTHER: <i>UK Entity that does not fit into any of the above (e.g. clubs, associations, : </i><br>
     * <b>nominetTradingName</b> String (optional): An optional trading name which will display in WHOIS if set.
     * <b>nominetCompanyNumber</b> String (conditionally required):  The company number - required if the nominetRegistrantType field is set to any of the values
     * LTD, PLC, LLP, IP, SCH or RCHAR.
     *
     * @var mixed[string] 
     */
    private $additionalData;

    /**
     * The status of this contact (read only).  This will contain one of the following values.
     * <b>LIVE:</b> For all Admin, Billing, Technical and newly created Owner contacts
     * <b>PENDING_CHANGES:</b> For any GTLD Owner Contacts where key data has been changed and is awaiting verification by the owner.  This is usually following a call to <i>updateDomainNames</i> on the <a href="netistrar-domain-lifecycle-api">Netistrar Domain Lifecycle API</a>.  For contacts with pending changes, the <a href="#pendingContact">pendingContact</a> member will contain the queued data awaiting approval.
     *
     * @var string 
     */
    protected $status;

    /**
     * Pending contact field.  This is only populated when the <a href="#status">status</a> member is set to <b>PENDING_CHANGES</b>.  This will contain a nested <a href="domain-name-contact">DomainNameContact</a> object containing the data awaiting verification by the owner of the domain.
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainNameContact 
     */
    protected $pendingContact;



    /**
     * Constructor
     *
     * @param  $name
     * @param  $emailAddress
     * @param  $organisation
     * @param  $street1
     * @param  $street2
     * @param  $city
     * @param  $county
     * @param  $postcode
     * @param  $country
     * @param  $telephoneDiallingCode
     * @param  $telephone
     * @param  $telephoneExt
     * @param  $faxDiallingCode
     * @param  $fax
     * @param  $faxExt
     * @param  $additionalData
     */
    public function __construct($name = null, $emailAddress = null, $organisation = null, $street1 = null, $street2 = null, $city = null, $county = null, $postcode = null, $country = null, $telephoneDiallingCode = null, $telephone = null, $telephoneExt = null, $faxDiallingCode = null, $fax = null, $faxExt = null, $additionalData = null){

        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->organisation = $organisation;
        $this->street1 = $street1;
        $this->street2 = $street2;
        $this->city = $city;
        $this->county = $county;
        $this->postcode = $postcode;
        $this->country = $country;
        $this->telephoneDiallingCode = $telephoneDiallingCode;
        $this->telephone = $telephone;
        $this->telephoneExt = $telephoneExt;
        $this->faxDiallingCode = $faxDiallingCode;
        $this->fax = $fax;
        $this->faxExt = $faxExt;
        $this->additionalData = $additionalData;
        
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return DomainNameContact
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }

    /**
     * Get the emailAddress
     *
     * @return string
     */
    public function getEmailAddress(){
        return $this->emailAddress;
    }

    /**
     * Set the emailAddress
     *
     * @param string $emailAddress
     * @return DomainNameContact
     */
    public function setEmailAddress($emailAddress){
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * Get the organisation
     *
     * @return string
     */
    public function getOrganisation(){
        return $this->organisation;
    }

    /**
     * Set the organisation
     *
     * @param string $organisation
     * @return DomainNameContact
     */
    public function setOrganisation($organisation){
        $this->organisation = $organisation;
        return $this;
    }

    /**
     * Get the street1
     *
     * @return string
     */
    public function getStreet1(){
        return $this->street1;
    }

    /**
     * Set the street1
     *
     * @param string $street1
     * @return DomainNameContact
     */
    public function setStreet1($street1){
        $this->street1 = $street1;
        return $this;
    }

    /**
     * Get the street2
     *
     * @return string
     */
    public function getStreet2(){
        return $this->street2;
    }

    /**
     * Set the street2
     *
     * @param string $street2
     * @return DomainNameContact
     */
    public function setStreet2($street2){
        $this->street2 = $street2;
        return $this;
    }

    /**
     * Get the city
     *
     * @return string
     */
    public function getCity(){
        return $this->city;
    }

    /**
     * Set the city
     *
     * @param string $city
     * @return DomainNameContact
     */
    public function setCity($city){
        $this->city = $city;
        return $this;
    }

    /**
     * Get the county
     *
     * @return string
     */
    public function getCounty(){
        return $this->county;
    }

    /**
     * Set the county
     *
     * @param string $county
     * @return DomainNameContact
     */
    public function setCounty($county){
        $this->county = $county;
        return $this;
    }

    /**
     * Get the postcode
     *
     * @return string
     */
    public function getPostcode(){
        return $this->postcode;
    }

    /**
     * Set the postcode
     *
     * @param string $postcode
     * @return DomainNameContact
     */
    public function setPostcode($postcode){
        $this->postcode = $postcode;
        return $this;
    }

    /**
     * Get the country
     *
     * @return string
     */
    public function getCountry(){
        return $this->country;
    }

    /**
     * Set the country
     *
     * @param string $country
     * @return DomainNameContact
     */
    public function setCountry($country){
        $this->country = $country;
        return $this;
    }

    /**
     * Get the telephoneDiallingCode
     *
     * @return string
     */
    public function getTelephoneDiallingCode(){
        return $this->telephoneDiallingCode;
    }

    /**
     * Set the telephoneDiallingCode
     *
     * @param string $telephoneDiallingCode
     * @return DomainNameContact
     */
    public function setTelephoneDiallingCode($telephoneDiallingCode){
        $this->telephoneDiallingCode = $telephoneDiallingCode;
        return $this;
    }

    /**
     * Get the telephone
     *
     * @return string
     */
    public function getTelephone(){
        return $this->telephone;
    }

    /**
     * Set the telephone
     *
     * @param string $telephone
     * @return DomainNameContact
     */
    public function setTelephone($telephone){
        $this->telephone = $telephone;
        return $this;
    }

    /**
     * Get the telephoneExt
     *
     * @return string
     */
    public function getTelephoneExt(){
        return $this->telephoneExt;
    }

    /**
     * Set the telephoneExt
     *
     * @param string $telephoneExt
     * @return DomainNameContact
     */
    public function setTelephoneExt($telephoneExt){
        $this->telephoneExt = $telephoneExt;
        return $this;
    }

    /**
     * Get the faxDiallingCode
     *
     * @return string
     */
    public function getFaxDiallingCode(){
        return $this->faxDiallingCode;
    }

    /**
     * Set the faxDiallingCode
     *
     * @param string $faxDiallingCode
     * @return DomainNameContact
     */
    public function setFaxDiallingCode($faxDiallingCode){
        $this->faxDiallingCode = $faxDiallingCode;
        return $this;
    }

    /**
     * Get the fax
     *
     * @return string
     */
    public function getFax(){
        return $this->fax;
    }

    /**
     * Set the fax
     *
     * @param string $fax
     * @return DomainNameContact
     */
    public function setFax($fax){
        $this->fax = $fax;
        return $this;
    }

    /**
     * Get the faxExt
     *
     * @return string
     */
    public function getFaxExt(){
        return $this->faxExt;
    }

    /**
     * Set the faxExt
     *
     * @param string $faxExt
     * @return DomainNameContact
     */
    public function setFaxExt($faxExt){
        $this->faxExt = $faxExt;
        return $this;
    }

    /**
     * Get the additionalData
     *
     * @return mixed[string]
     */
    public function getAdditionalData(){
        return $this->additionalData;
    }

    /**
     * Set the additionalData
     *
     * @param mixed[string] $additionalData
     * @return DomainNameContact
     */
    public function setAdditionalData($additionalData){
        $this->additionalData = $additionalData;
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
     * Get the pendingContact
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainNameContact
     */
    public function getPendingContact(){
        return $this->pendingContact;
    }


}