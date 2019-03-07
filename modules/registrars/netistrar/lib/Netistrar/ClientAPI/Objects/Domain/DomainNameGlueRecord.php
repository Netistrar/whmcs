<?php

namespace Netistrar\ClientAPI\Objects\Domain;

use Kinikit\Core\Object\SerialisableObject;
/**
 * A value object used within the <b>Domains API</b> when listing or setting glue records for a domain name.
 * Glue records (sometimes called child nameservers) define explicit mappings of subdomains to IP addresses such that the subdomain may be used as a delegated
 * nameserver for other domains or indeed the domain name itself.
*/
class DomainNameGlueRecord extends SerialisableObject {

    /**
     * The domain name for which this glue record applies.  This field is only populated for results returned from the <b>listGlueRecords</b> method on the
     * <b>Domains API</b> and is not supplied when adding glue records.
     *
     * @var string 
     */
    protected $domainName;

    /**
     * The non fully qualified sub domain prefix for the glue record e.g. ns1
     *
     * @var string 
     */
    private $subDomainPrefix;

    /**
     * An IPv4 address string matching the sub domain in xxx.xxx.xxx.xxx format
     *
     * @var string 
     */
    private $ipv4Address;

    /**
     * An IPv6 address string matching the sub domain in xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx format.
     *
     * @var string 
     */
    private $ipv6Address;



    /**
     * Constructor
     *
     * @param  $subDomainPrefix
     * @param  $ipv4Address
     * @param  $ipv6Address
     */
    public function __construct($subDomainPrefix = null, $ipv4Address = null, $ipv6Address = null){

        $this->subDomainPrefix = $subDomainPrefix;
        $this->ipv4Address = $ipv4Address;
        $this->ipv6Address = $ipv6Address;
        
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
     * Get the subDomainPrefix
     *
     * @return string
     */
    public function getSubDomainPrefix(){
        return $this->subDomainPrefix;
    }

    /**
     * Set the subDomainPrefix
     *
     * @param string $subDomainPrefix
     * @return DomainNameGlueRecord
     */
    public function setSubDomainPrefix($subDomainPrefix){
        $this->subDomainPrefix = $subDomainPrefix;
        return $this;
    }

    /**
     * Get the ipv4Address
     *
     * @return string
     */
    public function getIpv4Address(){
        return $this->ipv4Address;
    }

    /**
     * Set the ipv4Address
     *
     * @param string $ipv4Address
     * @return DomainNameGlueRecord
     */
    public function setIpv4Address($ipv4Address){
        $this->ipv4Address = $ipv4Address;
        return $this;
    }

    /**
     * Get the ipv6Address
     *
     * @return string
     */
    public function getIpv6Address(){
        return $this->ipv6Address;
    }

    /**
     * Set the ipv6Address
     *
     * @param string $ipv6Address
     * @return DomainNameGlueRecord
     */
    public function setIpv6Address($ipv6Address){
        $this->ipv6Address = $ipv6Address;
        return $this;
    }


}