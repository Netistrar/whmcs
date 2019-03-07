<?php

namespace Netistrar\ClientAPI\Objects\Domain;

use Kinikit\Core\Object\SerialisableObject;
/**
 * Domain availability class.
*/
class DomainAvailability extends SerialisableObject {

    /**
     * The full domain name for this availability object as a string
     *
     * @var string 
     */
    protected $domainName;

    /**
     * The availability status.  This matches one of the following values.
     *  <b>AVAILABLE:</b> When this domain is known to be available e.g. this object has been returned from a call to getLiveDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a>.
     *  <b>UNAVAILABLE:</b> When this domain is known to be unavailable e.g. this object has been returned from a call to getLiveDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a>.
     *  <b>MY_DOMAIN:</b> When this domain has already been registered to your account and is effectively one of your domains within Netistrar
     *  <b>HINTED_AVAILABLE:</b> When this object has been returned from a call to getHintedDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a> and the caching system does not have an entry for this domain.
     *  <b>HINTED_UNAVAILABLE:</b> When this object has been returned from a call to getHintedDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a> and the caching system does have an entry for this domain.
     *  <b>NO_HINTED_AVAILABILITY:</b> When this object has been returned from a call to getHintedDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a> and the caching system does have any information for the TLD of this domain.
     *
     * @var string 
     */
    protected $availability;

    /**
     * The pricing type for the domain name which matches one of the following values.
     * <b>STANDARD:</b> When the pricing for this domain is known to be standard i.e. when the TLD doesn't support premium domains or this object was returned from a call to getLiveDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a>
     * <b>PREMIUM:</b> When the pricing for this domain is known to be premium i.e. this object was returned from a call to getLiveDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a>.
     * <b>FIXED:</b> When the pricing for this domain has been fixed for a specific offer or deal.
     * <b>HINTED_PREMIUM:</b> When this object has been returned from a call to getHintedDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a> and the pricing for this domain is believed to be premium based upon the presence of an entry in the Netistrar cache of Premium Domains.
     * <b>HINTED_STANDARD:</b> When this object has been returned from a call to getHintedDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a> and  the pricing for this domain is believed to be standard based upon the absence of an entry in the Netistrar cache of Premium Domains.
     * <b>UNKNOWN:</b> When this object has been returned from a call to getHintedDomainAvailability on the <a href="netistrar-domain-availability-api">Netistrar Domain Availability API</a> and the tld for this domain supports premium pricing but there is no cached information for this tld.
     *
     * @var string 
     */
    protected $pricingType;

    /**
     * An indicator as to whether or not the tld for this domain name
     * supports premium domains.  This is particularly useful when searching for hinted availability as it
     * allows marking up of prices as "from" etc.
     *
     * @var string 
     */
    protected $premiumSupported;

    /**
     * An associative array containing sets of domain prices for Registration, Renewal and Transfer for this domain name.  These pricing sets are indexed using the keys <b>registration</b>, <b>renewal</b> and <b>transfer</b> and are included according
     * to the following rules depending upon the availability property.
     * Registration prices are included when the availability property is set to <b>AVAILABLE</b> or <b>HINTED_AVAILABLE</b> or <b>NO_HINTED_AVAILABILITY</b>,  these comprise an array of <a href="domain-availability-price-object">DomainAvailabilityPrice</a> items for all possible registration years (usually 10).
     * Renewal prices are included for all availabilities.  In the <b>AVAILABLE</b>, <b>HINTED_AVAILABLE</b> and <b>NO_HINTED_AVAILABILITY</b> cases this will comprise an array of <a href="domain-availability-price-object">DomainAvailabilityPrice</a> items for all possible renewal years (i.e. registration years -1).  In the
     * <b>MY_DOMAIN</b> case this will comprise an array of <a href="domain-availability-price-object">DomainAvailabilityPrice</a> items for all possible renewal years according to the current registration state.  In the remaining <b>UNAVAILABLE</b>
     * and <b>HINTED_UNAVAILABLE</b> cases this will comprise an array containing a single <a href="domain-availability-price-object">DomainAvailabilityPrice</a> item for one year renewal price.
     * Transfer prices are included only when the availability property is set to <b>UNAVAILABLE</b> or <b>HINTED_UNAVAILABLE</b>.  This will comprise an array containing a single <a href="domain-availability-price-object">DomainAvailabilityPrice</a> item for one year transfer price.
     *
     * @var \Netistrar\ClientAPI\Objects\Domain\DomainAvailabilityPrice[string][] 
     */
    protected $prices;

    /**
     * An arbitrary array of additional data included to qualify given availability and pricing states when required.
     *
     * @var mixed[string] 
     */
    protected $additionalData;



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
     * Get the availability
     *
     * @return string
     */
    public function getAvailability(){
        return $this->availability;
    }

    /**
     * Get the pricingType
     *
     * @return string
     */
    public function getPricingType(){
        return $this->pricingType;
    }

    /**
     * Get the premiumSupported
     *
     * @return string
     */
    public function getPremiumSupported(){
        return $this->premiumSupported;
    }

    /**
     * Get the prices
     *
     * @return \Netistrar\ClientAPI\Objects\Domain\DomainAvailabilityPrice[string][]
     */
    public function getPrices(){
        return $this->prices;
    }

    /**
     * Get the additionalData
     *
     * @return mixed[string]
     */
    public function getAdditionalData(){
        return $this->additionalData;
    }


}